<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TimeSlot;
use App\Jobs\SendReviewRequest;
use App\Models\Booking;
use App\Models\BookingDay;
use App\Models\ExtraService;
use App\Models\Hall;
use App\Models\ServiceFeeSetting;
use App\Observers\BookingObserver;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
 */
class BookingService
{
    /**
     * Commission service instance for fee calculation.
     */
    protected CommissionService $commissionService;

    /**
     * Constructor with dependency injection.
     *
     * @param  CommissionService  $commissionService  Service to resolve commission settings
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
     * @param  int  $hallId  The hall ID to check
     * @param  string  $bookingDate  The date to check (Y-m-d format)
     * @param  string  $timeSlot  The time slot to check
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
                    ->whereHas('booking', fn ($query) => $query->whereIn('status', $activeStatuses))
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
        return ! $takenSlots->contains('full_day');
    }

    /**
     * Check availability for multiple days at once (multi-day booking).
     *
     * @param  int  $hallId  The hall ID to check
     * @param  array  $days  Array of ['date' => 'Y-m-d', 'time_slot' => string]
     * @return array<int, array{date: string, time_slot: string}> The days that are NOT available (empty = all clear)
     */
    public function checkAvailabilityForDays(int $hallId, array $days): array
    {
        $conflicts = [];

        foreach ($days as $day) {
            if (! $this->checkAvailability($hallId, $day['date'], $day['time_slot'])) {
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
     * @param  array  $data  Validated booking data
     * @return Booking The created booking instance
     *
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

            // Prices: one shared breakdown so quote() and createBooking() can never disagree.
            $pricing = $this->priceBreakdown($hall, $days, $data['extra_services'] ?? []);

            $dayPrices = $pricing['day_prices'];
            $hallPrice = $pricing['hall_price'];
            $selectedServices = $pricing['services'];
            $servicesPrice = $pricing['services_price'];
            $subtotal = $pricing['subtotal'];
            $platformFee = $pricing['platform_fee'];
            $serviceFeeType = $pricing['service_fee_type'];
            $serviceFeeValue = $pricing['service_fee_value'];
            $commissionAmount = $pricing['commission_amount'];
            $commissionType = $pricing['commission_type'];
            $commissionValue = $pricing['commission_value'];
            $totalAmount = $pricing['total_amount'];
            $ownerPayout = $pricing['owner_payout'];

            Log::info('Booking pricing calculated (commission + service fee)', [
                'hall_id' => $hall->id,
                'hall_price' => $hallPrice,
                'services_price' => $servicesPrice,
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'owner_payout' => $ownerPayout,
            ]);

            // Generate unique booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking record
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'hall_id' => $data['hall_id'],
                'user_id' => $data['user_id'],
                'booking_date' => $days[0]['date'],
                'time_slot' => $days[0]['time_slot'],
                'number_of_guests' => $data['number_of_guests'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'event_type' => $data['event_type'] ?? null,
                'event_details' => isset($data['event_details'])
                    ? json_encode($data['event_details'])
                    : null,

                // Pricing: dual fee system (service fee + commission)
                'hall_price' => $hallPrice,
                'services_price' => $servicesPrice,
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,       // From ServiceFeeSetting (customer-facing)
                'total_amount' => $totalAmount,        // subtotal + platform_fee
                'commission_amount' => $commissionAmount,   // From CommissionSetting (owner-side)
                'commission_type' => $commissionType,     // 'percentage' or 'fixed' (from CommissionSetting)
                'commission_value' => $commissionValue,    // Raw value (from CommissionSetting)
                'owner_payout' => $ownerPayout,        // subtotal - commission_amount

                // Service fee audit trail (recorded at booking time)
                'service_fee_type' => $serviceFeeType,     // 'percentage' or 'fixed' (from ServiceFeeSetting)
                'service_fee_value' => $serviceFeeValue,    // Raw value (from ServiceFeeSetting)

                // Status
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Attach extra services to booking pivot table
            if (! empty($selectedServices)) {
                foreach ($selectedServices as $service) {
                    DB::table('booking_extra_services')->insert([
                        'booking_id' => $booking->id,
                        'extra_service_id' => $service['id'],
                        'service_name' => json_encode($service['name']),
                        'unit_price' => $service['price'],
                        'quantity' => 1,
                        'total_price' => $service['price'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Record the day-by-day breakdown (1 row for ordinary bookings, up to 3 for multi-day)
            foreach ($days as $index => $day) {
                $booking->bookingDays()->create([
                    'hall_id' => $hall->id,
                    'date' => $day['date'],
                    'time_slot' => $day['time_slot'],
                    'price' => $dayPrices[$index],
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

            Log::error('Booking creation failed in service: '.$e->getMessage(), [
                'data' => $data,
                'exception' => $e,
            ]);

            throw new Exception('Failed to create booking: '.$e->getMessage());
        }
    }

    /**
     * Generate unique booking number.
     *
     * Format: BK-YYYY-NNNNN (e.g., BK-2026-00042)
     */
    protected function generateBookingNumber(): string
    {
        $year = date('Y');

        // Get the last booking number for this year
        // withTrashed: soft-deleted bookings keep their (unique) number.
        // lockForUpdate: createBooking() runs in a transaction, so concurrent bookings wait here.
        $lastBooking = Booking::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastBooking) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastBooking->booking_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: BK-2026-00001
        return sprintf('BK-%s-%05d', $year, $newNumber);
    }

    /**
     * Price a set of days and extra services exactly as createBooking() charges them.
     *
     * Extra services must be active and belong to the hall; any other id is ignored.
     *
     * @param  array<int, array{date: string, time_slot: string}>  $days
     * @param  array<int, int>  $serviceIds
     * @return array<string, mixed>
     */
    public function priceBreakdown(Hall $hall, array $days, array $serviceIds = []): array
    {
        $dayPrices = [];
        $hallPrice = 0.0;
        foreach ($days as $day) {
            $dayPrice = $hall->getPriceForDate($day['date'], $day['time_slot']);
            $dayPrices[] = $dayPrice;
            $hallPrice += $dayPrice;
        }

        $services = empty($serviceIds)
            ? collect()
            : ExtraService::whereIn('id', $serviceIds)
                ->where('hall_id', $hall->id)
                ->where('is_active', true)
                ->get();

        $selectedServices = $services->map(fn (ExtraService $service) => [
            'id' => $service->id,
            'name' => $service->getTranslations('name'),
            'price' => (float) $service->price,
        ])->values()->all();

        $servicesPrice = (float) $services->sum(fn (ExtraService $service) => (float) $service->price);
        $subtotal = round($hallPrice + $servicesPrice, 2);

        // Commission: owner-side, invisible to the customer (Hall > Owner > Global)
        $feeData = $this->commissionService->calculateFees($hall, $subtotal);
        $commissionAmount = (float) $feeData['commission_amount'];

        // Service fee: customer-facing, optional (Hall > Owner > Global)
        $platformFee = 0.00;
        $serviceFeeType = null;
        $serviceFeeValue = null;

        /** @var ServiceFeeSetting|null $serviceFee */
        $serviceFee = ServiceFeeSetting::resolveForHall($hall);
        if ($serviceFee) {
            $serviceFeeType = $serviceFee->fee_type->value;
            $serviceFeeValue = (float) $serviceFee->fee_value;
            $platformFee = $serviceFee->calculateFee($subtotal);
        }

        return [
            'day_prices' => $dayPrices,
            'hall_price' => $hallPrice,
            'services' => $selectedServices,
            'services_price' => $servicesPrice,
            'subtotal' => $subtotal,
            'platform_fee' => (float) $platformFee,
            'service_fee_type' => $serviceFeeType,
            'service_fee_value' => $serviceFeeValue,
            'commission_amount' => $commissionAmount,
            'commission_type' => $feeData['commission_type'],
            'commission_value' => $feeData['commission_value'],
            'total_amount' => round($subtotal + $platformFee, 3),
            'owner_payout' => round($subtotal - $commissionAmount, 3),
        ];
    }

    /**
     * Whether one day + slot can be booked right now: inside the booking window
     * (config majalis.booking), opened by the owner in hall_availabilities, and
     * not held by another booking (day 1 or an extra day of a multi-day booking).
     */
    public function isDayBookable(Hall $hall, string $date, string $timeSlot): bool
    {
        $start = Carbon::parse($date.' '.TimeSlot::from($timeSlot)->startTime(), config('app.timezone'));

        if ($start->lt(now()->addHours((int) config('majalis.booking.min_advance_hours')))) {
            return false;
        }

        if ($start->copy()->startOfDay()->gt(today()->addDays((int) config('majalis.booking.advance_days')))) {
            return false;
        }

        return app(AvailabilityService::class)->isSlotAvailable($hall, $date, $timeSlot)
            && $this->checkAvailability($hall->id, $date, $timeSlot);
    }

    /**
     * Everything the customer sees before paying: prices, availability per day,
     * promo result and the amount due now. Writes nothing.
     *
     * @param  array<int, array{date: string, time_slot: string}>  $days
     * @param  array<int, int>  $serviceIds
     * @param  string  $paymentType  'full' or 'advance' (falls back to full when the hall has no advance option)
     * @return array<string, mixed>
     */
    public function quote(Hall $hall, array $days, array $serviceIds = [], ?string $promoCode = null, string $paymentType = 'full'): array
    {
        $pricing = $this->priceBreakdown($hall, $days, $serviceIds);

        $unavailable = array_values(array_filter(
            $days,
            fn (array $day) => ! $this->isDayBookable($hall, $day['date'], $day['time_slot']),
        ));

        $promo = null;
        $discount = 0.0;
        if ($promoCode !== null && trim($promoCode) !== '') {
            $result = app(PromoCodeService::class)->validate($promoCode, $hall->id, $pricing['subtotal']);
            $promo = [
                'code' => strtoupper(trim($promoCode)),
                'valid' => (bool) $result['valid'],
                'message' => $result['message'],
                'promo_code_id' => $result['promo_code_id'] ?? null,
            ];
            if ($result['valid']) {
                $discount = (float) $result['discount_amount'];
            }
        }

        $total = round(max(0.0, $pricing['subtotal'] + $pricing['platform_fee'] - $discount), 3);

        return [
            'pricing' => $pricing,
            'discount_amount' => round($discount, 3),
            'promo' => $promo,
            'total_amount' => $total,
            'payment' => $this->paymentPlan($hall, $pricing['subtotal'] - $discount, $pricing['platform_fee'], $total, $paymentType),
            'unavailable_days' => $unavailable,
        ];
    }

    /**
     * Amount due now for a payment choice. Same formula as the guest checkout:
     * advance = hall advance on the discounted subtotal + the full service fee,
     * capped at the total; the balance is paid to the hall directly.
     *
     * @return array{type: string, amount_due_now: float, advance_amount: ?float, balance_due: ?float}
     */
    public function paymentPlan(Hall $hall, float $discountedSubtotal, float $platformFee, float $total, string $requested): array
    {
        if ($requested !== 'advance' || ! $hall->requiresAdvancePayment()) {
            return ['type' => 'full', 'amount_due_now' => $total, 'advance_amount' => null, 'balance_due' => null];
        }

        $advance = min($total, $hall->calculateAdvanceAmount(max(0.0, $discountedSubtotal)) + $platformFee);

        return [
            'type' => 'advance',
            'amount_due_now' => round($advance, 3),
            'advance_amount' => round($advance, 3),
            'balance_due' => round(max(0.0, $total - $advance), 3),
        ];
    }

    /**
     * Store the payment choice on a booking (after any promo is applied).
     *
     * For an advance, commission and owner payout cover the advance only: the
     * balance is collected by the hall owner and marked received in the Owner
     * panel (same as GuestBookingController).
     */
    public function applyPaymentPlan(Booking $booking, string $requested): Booking
    {
        $booking->loadMissing('hall');

        $platformFee = (float) ($booking->platform_fee ?? 0);
        $plan = $this->paymentPlan(
            $booking->hall,
            (float) $booking->subtotal - (float) ($booking->discount_amount ?? 0),
            $platformFee,
            (float) $booking->total_amount,
            $requested,
        );

        $data = [
            'payment_type' => $plan['type'],
            'advance_amount' => $plan['advance_amount'],
            'balance_due' => $plan['balance_due'],
        ];

        if ($plan['type'] === 'advance') {
            $advanceBase = max(0.0, $plan['advance_amount'] - $platformFee);
            $commType = (string) ($booking->commission_type ?? '');
            $commValue = (float) ($booking->commission_value ?? 0);
            $commOnAdvance = 0.0;

            if ($commType === 'percentage' && $commValue > 0) {
                $commOnAdvance = round(($advanceBase * $commValue) / 100, 3);
            } elseif ($commType === 'fixed' && $commValue > 0) {
                $commOnAdvance = round(min($commValue, $advanceBase), 3);
            }

            $data['commission_amount'] = $commOnAdvance;
            $data['owner_payout'] = round(max(0.0, $advanceBase - $commOnAdvance), 3);
        }

        $booking->update($data);

        return $booking;
    }

    /**
     * Calculate booking pricing with dual fee system.
     *
     * Resolves both commission (owner-side) and service fee (customer-facing)
     * using the same logic as createBooking(). Used for pricing preview
     * and advance payment calculations.
     *
     * @param  Hall  $hall  The hall being booked
     * @param  array  $serviceIds  Array of extra service IDs
     * @return array Pricing breakdown with both fee types
     */
    public function calculatePricing(Hall $hall, array $serviceIds = []): array
    {
        $hallPrice = (float) $hall->price_per_slot;
        $servicesPrice = 0.0;

        if (! empty($serviceIds)) {
            $servicesPrice = (float) ExtraService::whereIn('id', $serviceIds)
                ->where('is_active', true)
                ->sum('price');
        }

        $subtotal = round($hallPrice + $servicesPrice, 2);

        // 1. Commission — owner-side (invisible to customer)
        $feeData = $this->commissionService->calculateFees($hall, $subtotal);

        $commissionAmount = (float) $feeData['commission_amount'];
        $commissionType = $feeData['commission_type'];
        $commissionValue = $feeData['commission_value'];

        // 2. Service Fee — customer-facing (added to total)
        $platformFee = 0.00;
        $serviceFeeType = null;
        $serviceFeeValue = null;

        /** @var ServiceFeeSetting|null $serviceFee */
        $serviceFee = ServiceFeeSetting::resolveForHall($hall);

        if ($serviceFee) {
            $serviceFeeType = $serviceFee->fee_type->value;
            $serviceFeeValue = (float) $serviceFee->fee_value;
            $platformFee = $serviceFee->calculateFee($subtotal);
        }

        // 3. Final totals
        $totalAmount = round($subtotal + $platformFee, 3);
        $ownerPayout = round($subtotal - $commissionAmount, 3);

        return [
            'hall_price' => $hallPrice,
            'services_price' => $servicesPrice,
            'subtotal' => $subtotal,
            'platform_fee' => $platformFee,        // From ServiceFeeSetting
            'commission_amount' => $commissionAmount,    // From CommissionSetting
            'commission_type' => $commissionType,
            'commission_value' => $commissionValue,
            'service_fee_type' => $serviceFeeType,
            'service_fee_value' => $serviceFeeValue,
            'total_amount' => $totalAmount,
            'owner_payout' => $ownerPayout,
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

                // Dispatch review request 2 hours after completion — only while the
                // review window is still open (avoids mailing a stale backlog)
                if ($booking->booking_date >= now()->subDays(14)->startOfDay()) {
                    SendReviewRequest::dispatch($booking)
                        ->delay(now()->addHours(2));
                }

                $count++;
            } catch (Exception $e) {
                Log::error('autoCompletePastBookings: Failed to complete booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }
}
