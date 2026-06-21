<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingDay;
use App\Models\Hall;
use App\Models\ExtraService;
use App\Models\ServiceFeeSetting;
use App\Jobs\SendReviewRequest;
use App\Observers\BookingObserver;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling booking operations.
 *
 * Implements dual fee system matching GuestBookingController:
 *   - CommissionService  → commission_amount (owner-side, invisible to customer)
 *   - ServiceFeeSetting  → platform_fee (customer-facing, added to total)
 *
 * Previously, CommissionService was used for BOTH commission and platform_fee
 * (same value in both fields). Now they are resolved independently:
 *   platform_fee  = ServiceFeeSetting::resolveForHall()  → customer pays
 *   commission    = CommissionService::calculateFees()    → deducted from owner
 *
 * @package App\Services
 */
class BookingService
{
    /**
     * Commission service instance for fee calculation.
     *
     * @var CommissionService
     */
    protected CommissionService $commissionService;

    /**
     * Constructor with dependency injection.
     *
     * @param CommissionService $commissionService Service to resolve commission settings
     */
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Check if a hall is available for a specific date and time slot.
     *
     * Looks at both the legacy bookings.booking_date/time_slot pair (day 1
     * of every booking, and the only record for historical bookings created
     * before BookingDay existed) AND the booking_days table (covers
     * additional days 2-3 of multi-day bookings, which are NOT reflected in
     * bookings.booking_date/time_slot).
     *
     * @param int    $hallId      The hall ID to check
     * @param string $bookingDate The date to check (Y-m-d format)
     * @param string $timeSlot    The time slot to check
     * @return bool True if the slot is available
     */
    public function checkAvailability(int $hallId, string $bookingDate, string $timeSlot): bool
    {
        $activeStatuses = ['pending', 'confirmed', 'paid'];

        $takenSlots = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', $activeStatuses)
            ->pluck('time_slot')
            ->merge(
                BookingDay::where('hall_id', $hallId)
                    ->where('date', $bookingDate)
                    ->whereHas('booking', fn($query) => $query->whereIn('status', $activeStatuses))
                    ->pluck('time_slot')
            )
            ->unique();

        if ($takenSlots->contains($timeSlot)) {
            return false;
        }

        // Requesting full_day → blocked if ANY slot is already taken
        if ($timeSlot === 'full_day') {
            return $takenSlots->isEmpty();
        }

        // Requesting an individual slot → blocked if full_day is already taken
        return !$takenSlots->contains('full_day');
    }

    /**
     * Check availability for multiple days at once (multi-day booking).
     *
     * @param int   $hallId The hall ID to check
     * @param array $days   Array of ['date' => 'Y-m-d', 'time_slot' => string]
     * @return array<int, array{date: string, time_slot: string}> The days that are NOT available (empty = all clear)
     */
    public function checkAvailabilityForDays(int $hallId, array $days): array
    {
        $conflicts = [];

        foreach ($days as $day) {
            if (!$this->checkAvailability($hallId, $day['date'], $day['time_slot'])) {
                $conflicts[] = $day;
            }
        }

        return $conflicts;
    }

    /**
     * Create a new booking with proper commission and service fee calculation.
     *
     * Fee resolution (matches GuestBookingController logic):
     *   1. CommissionService → resolves commission (owner-side, invisible to customer)
     *      Priority: Hall-specific > Owner-specific > Global CommissionSetting
     *   2. ServiceFeeSetting → resolves platform fee (customer-facing, added to total)
     *      Priority: Hall-specific > Owner-specific > Global ServiceFeeSetting
     *
     * Financial flow:
     *   platform_fee      = ServiceFeeSetting fee (customer pays this ON TOP of subtotal)
     *   commission_amount  = CommissionSetting commission (deducted from owner payout)
     *   total_amount       = subtotal + platform_fee
     *   owner_payout       = subtotal - commission_amount
     *
     * @param array $data Validated booking data
     * @return Booking The created booking instance
     * @throws Exception If booking creation fails
     */
    public function createBooking(array $data): Booking
    {
        DB::beginTransaction();

        try {
            // Get hall details with eager loading
            $hall = Hall::findOrFail($data['hall_id']);

            // Days breakdown for this booking (1 day = ordinary booking, up to 3 = multi-day).
            // Falls back to the single booking_date/time_slot pair when 'days' isn't provided,
            // so existing callers behave exactly as before.
            $days = $data['days'] ?? [
                ['date' => $data['booking_date'], 'time_slot' => $data['time_slot']],
            ];

            // Calculate hall price as the sum of each day's resolved price
            // (custom_price from HallAvailability if set, else the hall's slot price)
            $hallPrice = 0.0;
            $dayPrices = [];
            foreach ($days as $day) {
                $dayPrice = $hall->getPriceForDate($day['date'], $day['time_slot']);
                $dayPrices[] = $dayPrice;
                $hallPrice += $dayPrice;
            }

            // Calculate services total
            $servicesPrice   = 0.0;
            $selectedServices = [];

            if (!empty($data['extra_services'])) {
                $services = ExtraService::whereIn('id', $data['extra_services'])
                    ->where('is_active', true)
                    ->get();

                foreach ($services as $service) {
                    $servicesPrice += (float) $service->price;
                    $selectedServices[] = [
                        'id'    => $service->id,
                        'name'  => $service->name,
                        'price' => $service->price,
                    ];
                }
            }

            // Calculate subtotal (hall + services, before platform fee)
            $subtotal = round($hallPrice + $servicesPrice, 2);

            // ==========================================================
            // 1. COMMISSION — Owner-side charge (deducted from payout)
            // ==========================================================
            // Resolve the applicable commission using CommissionService.
            // Commission is invisible to the customer.
            // Priority: Hall-specific > Owner-specific > Global
            // ==========================================================
            $feeData = $this->commissionService->calculateFees($hall, $subtotal);

            // Commission values (owner-side — invisible to customer)
            $commissionAmount = (float) $feeData['commission_amount'];
            $commissionType   = $feeData['commission_type'];
            $commissionValue  = $feeData['commission_value'];

            // ==========================================================
            // 2. SERVICE FEE — Customer-facing fee (added to total)
            // ==========================================================
            // Resolve the applicable service fee using ServiceFeeSetting.
            // Service fees are OPTIONAL. If no active setting exists,
            // platform_fee remains 0 (no charge to customer).
            // Priority: Hall-specific → Owner-specific → Global
            // ==========================================================
            $platformFee     = 0.00;
            $serviceFeeType  = null;
            $serviceFeeValue = null;

            /** @var ServiceFeeSetting|null $serviceFee */
            $serviceFee = ServiceFeeSetting::resolveForHall($hall);

            if ($serviceFee) {
                // Extract service fee metadata for audit trail
                $serviceFeeType  = $serviceFee->fee_type->value;   // 'percentage' or 'fixed'
                $serviceFeeValue = (float) $serviceFee->fee_value;

                // Calculate the customer-facing platform fee from ServiceFeeSetting
                $platformFee = $serviceFee->calculateFee($subtotal);

                Log::info('Service fee applied to authenticated booking', [
                    'fee_id'     => $serviceFee->id,
                    'fee_type'   => $serviceFeeType,
                    'fee_value'  => $serviceFeeValue,
                    'subtotal'   => $subtotal,
                    'fee_amount' => $platformFee,
                    'scope'      => $serviceFee->hall_id ? 'hall'
                        : ($serviceFee->owner_id ? 'owner' : 'global'),
                ]);
            }

            // ==========================================================
            // 3. TOTALS — Calculate final amounts
            // ==========================================================
            // total_amount  = subtotal + platform_fee (what customer pays)
            // owner_payout  = subtotal - commission   (what owner receives)
            // ==========================================================
            $totalAmount = round($subtotal + $platformFee, 3);
            $ownerPayout = round($subtotal - $commissionAmount, 3);

            Log::info('Booking pricing calculated (commission + service fee)', [
                'hall_id'            => $hall->id,
                'hall_price'         => $hallPrice,
                'services_price'     => $servicesPrice,
                'subtotal'           => $subtotal,
                'platform_fee'       => $platformFee,       // From ServiceFeeSetting (customer pays)
                'service_fee_type'   => $serviceFeeType,
                'service_fee_value'  => $serviceFeeValue,
                'commission_amount'  => $commissionAmount,   // From CommissionSetting (owner pays)
                'commission_type'    => $commissionType,
                'commission_value'   => $commissionValue,
                'total_amount'       => $totalAmount,
                'owner_payout'       => $ownerPayout,
            ]);

            // Generate unique booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking record
            $booking = Booking::create([
                'booking_number'    => $bookingNumber,
                'hall_id'           => $data['hall_id'],
                'user_id'           => $data['user_id'],
                'booking_date'      => $days[0]['date'],
                'time_slot'         => $days[0]['time_slot'],
                'number_of_guests'  => $data['number_of_guests'],
                'customer_name'     => $data['customer_name'],
                'customer_email'    => $data['customer_email'],
                'customer_phone'    => $data['customer_phone'],
                'customer_notes'    => $data['customer_notes'] ?? null,
                'event_type'        => $data['event_type'] ?? null,
                'event_details'     => isset($data['event_details'])
                    ? json_encode($data['event_details'])
                    : null,

                // Pricing: dual fee system (service fee + commission)
                'hall_price'        => $hallPrice,
                'services_price'    => $servicesPrice,
                'subtotal'          => $subtotal,
                'platform_fee'      => $platformFee,       // From ServiceFeeSetting (customer-facing)
                'total_amount'      => $totalAmount,        // subtotal + platform_fee
                'commission_amount' => $commissionAmount,   // From CommissionSetting (owner-side)
                'commission_type'   => $commissionType,     // 'percentage' or 'fixed' (from CommissionSetting)
                'commission_value'  => $commissionValue,    // Raw value (from CommissionSetting)
                'owner_payout'      => $ownerPayout,        // subtotal - commission_amount

                // Service fee audit trail (recorded at booking time)
                'service_fee_type'  => $serviceFeeType,     // 'percentage' or 'fixed' (from ServiceFeeSetting)
                'service_fee_value' => $serviceFeeValue,    // Raw value (from ServiceFeeSetting)

                // Status
                'status'            => 'pending',
                'payment_status'    => 'pending',
            ]);

            // Attach extra services to booking pivot table
            if (!empty($selectedServices)) {
                foreach ($selectedServices as $service) {
                    DB::table('booking_extra_services')->insert([
                        'booking_id'       => $booking->id,
                        'extra_service_id' => $service['id'],
                        'service_name'     => json_encode($service['name']),
                        'unit_price'       => $service['price'],
                        'quantity'         => 1,
                        'total_price'      => $service['price'],
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Record the day-by-day breakdown (1 row for ordinary bookings, up to 3 for multi-day)
            foreach ($days as $index => $day) {
                $booking->bookingDays()->create([
                    'hall_id'   => $hall->id,
                    'date'      => $day['date'],
                    'time_slot' => $day['time_slot'],
                    'price'     => $dayPrices[$index],
                ]);
            }

            // Re-sync HallAvailability now that all BookingDay rows exist.
            // The 'created' event fired above (before these rows existed)
            // already marked day 1 via BookingObserver's legacy fallback;
            // this re-sync covers any additional days for multi-day bookings.
            if (count($days) > 1) {
                $booking->load('bookingDays');
                app(BookingObserver::class)->markSlotAsBooked($booking);
            }

            DB::commit();

            // Load relationships for return
            $booking->load(['hall', 'user', 'extraServices']);

            return $booking;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed in service: ' . $e->getMessage(), [
                'data'      => $data,
                'exception' => $e,
            ]);

            throw new Exception('Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique booking number.
     *
     * Format: BK-YYYY-NNNNN (e.g., BK-2026-00042)
     *
     * @return string
     */
    protected function generateBookingNumber(): string
    {
        $year = date('Y');

        // Get the last booking number for this year
        $lastBooking = Booking::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBooking) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastBooking->booking_number, -5);
            $newNumber  = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: BK-2026-00001
        return sprintf('BK-%s-%05d', $year, $newNumber);
    }

    /**
     * Calculate booking pricing with dual fee system.
     *
     * Resolves both commission (owner-side) and service fee (customer-facing)
     * using the same logic as createBooking(). Used for pricing preview
     * and advance payment calculations.
     *
     * @param Hall  $hall       The hall being booked
     * @param array $serviceIds Array of extra service IDs
     * @return array Pricing breakdown with both fee types
     */
    public function calculatePricing(Hall $hall, array $serviceIds = []): array
    {
        $hallPrice     = (float) $hall->price_per_slot;
        $servicesPrice = 0.0;

        if (!empty($serviceIds)) {
            $servicesPrice = (float) ExtraService::whereIn('id', $serviceIds)
                ->where('is_active', true)
                ->sum('price');
        }

        $subtotal = round($hallPrice + $servicesPrice, 2);

        // 1. Commission — owner-side (invisible to customer)
        $feeData = $this->commissionService->calculateFees($hall, $subtotal);

        $commissionAmount = (float) $feeData['commission_amount'];
        $commissionType   = $feeData['commission_type'];
        $commissionValue  = $feeData['commission_value'];

        // 2. Service Fee — customer-facing (added to total)
        $platformFee     = 0.00;
        $serviceFeeType  = null;
        $serviceFeeValue = null;

        /** @var ServiceFeeSetting|null $serviceFee */
        $serviceFee = ServiceFeeSetting::resolveForHall($hall);

        if ($serviceFee) {
            $serviceFeeType  = $serviceFee->fee_type->value;
            $serviceFeeValue = (float) $serviceFee->fee_value;
            $platformFee     = $serviceFee->calculateFee($subtotal);
        }

        // 3. Final totals
        $totalAmount = round($subtotal + $platformFee, 3);
        $ownerPayout = round($subtotal - $commissionAmount, 3);

        return [
            'hall_price'        => $hallPrice,
            'services_price'    => $servicesPrice,
            'subtotal'          => $subtotal,
            'platform_fee'      => $platformFee,        // From ServiceFeeSetting
            'commission_amount' => $commissionAmount,    // From CommissionSetting
            'commission_type'   => $commissionType,
            'commission_value'  => $commissionValue,
            'service_fee_type'  => $serviceFeeType,
            'service_fee_value' => $serviceFeeValue,
            'total_amount'      => $totalAmount,
            'owner_payout'      => $ownerPayout,
        ];
    }

    /**
     * Mark past confirmed bookings as completed and dispatch review-request emails.
     *
     * Called nightly by AutoCompleteBookings job.
     * The review email is delayed 2 hours from now (the moment the job runs),
     * ensuring it arrives after the event has finished.
     *
     * @return int Number of bookings completed
     */
    public function autoCompletePastBookings(): int
    {
        $bookings = Booking::query()
            ->where('status', 'confirmed')
            ->where('booking_date', '<', now()->toDateString())
            ->with(['hall', 'user'])
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $booking->complete();
                });

                // Dispatch review request 2 hours after completion
                SendReviewRequest::dispatch($booking)
                    ->delay(now()->addHours(2));

                $count++;
            } catch (Exception $e) {
                Log::error('autoCompletePastBookings: Failed to complete booking', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }
}
