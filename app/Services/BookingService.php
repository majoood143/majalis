<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\ExtraService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling booking operations.
 *
 * ✅ FIX: Now integrates CommissionService to calculate platform fees
 *    from CommissionSetting (Hall-specific > Owner-specific > Global).
 *    Previously, platform_fee was hardcoded as 0.
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
     * @param int    $hallId      The hall ID to check
     * @param string $bookingDate The date to check (Y-m-d format)
     * @param string $timeSlot    The time slot to check
     * @return bool True if the slot is available
     */
    public function checkAvailability(int $hallId, string $bookingDate, string $timeSlot): bool
    {
        // Check for existing bookings with same date and time slot
        $existingBooking = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->exists();

        if ($existingBooking) {
            return false;
        }

        // If requesting full_day, check if any other slot is booked
        if ($timeSlot === 'full_day') {
            $anySlotBooked = Booking::where('hall_id', $hallId)
                ->where('booking_date', $bookingDate)
                ->whereIn('status', ['pending', 'confirmed', 'paid'])
                ->exists();

            return !$anySlotBooked;
        }

        // Check if full_day is already booked
        $fullDayBooked = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->where('time_slot', 'full_day')
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->exists();

        return !$fullDayBooked;
    }

    /**
     * Create a new booking with proper commission/platform fee calculation.
     *
     * ✅ FIX: Now resolves commission from CommissionSetting model
     *    using priority: Hall-specific > Owner-specific > Global.
     *    Platform fee is added to customer's total and shown on payment page.
     *    For advance payments, the fee is included in the total before
     *    advance amount calculation.
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

            // Calculate hall price
            $hallPrice = (float) $hall->price_per_slot;

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

            // ✅ FIX: Calculate platform fee using CommissionService
            // Previously: $platformFee = 0;
            // Now: resolves from CommissionSetting (Hall > Owner > Global)
            $feeData = $this->commissionService->calculateFees($hall, $subtotal);

            $platformFee      = (float) $feeData['platform_fee'];
            $commissionAmount = (float) $feeData['commission_amount'];
            $commissionType   = $feeData['commission_type'];
            $commissionValue  = $feeData['commission_value'];
            $totalAmount      = (float) $feeData['total_amount'];
            $ownerPayout      = (float) $feeData['owner_payout'];

            Log::info('Booking pricing calculated with platform fee', [
                'hall_id'           => $hall->id,
                'hall_price'        => $hallPrice,
                'services_price'    => $servicesPrice,
                'subtotal'          => $subtotal,
                'platform_fee'      => $platformFee,
                'commission_type'   => $commissionType,
                'commission_value'  => $commissionValue,
                'total_amount'      => $totalAmount,
                'owner_payout'      => $ownerPayout,
            ]);

            // Generate unique booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking record
            $booking = Booking::create([
                'booking_number'    => $bookingNumber,
                'hall_id'           => $data['hall_id'],
                'user_id'           => $data['user_id'],
                'booking_date'      => $data['booking_date'],
                'time_slot'         => $data['time_slot'],
                'number_of_guests'  => $data['number_of_guests'],
                'customer_name'     => $data['customer_name'],
                'customer_email'    => $data['customer_email'],
                'customer_phone'    => $data['customer_phone'],
                'customer_notes'    => $data['customer_notes'] ?? null,
                'event_type'        => $data['event_type'] ?? null,
                'event_details'     => isset($data['event_details'])
                    ? json_encode($data['event_details'])
                    : null,

                // ✅ FIX: Pricing now includes platform fee
                'hall_price'        => $hallPrice,
                'services_price'    => $servicesPrice,
                'subtotal'          => $subtotal,
                'platform_fee'      => $platformFee,       // Was: 0
                'total_amount'      => $totalAmount,        // Was: $subtotal + 0
                'commission_amount' => $commissionAmount,   // Now: same as platform_fee
                'commission_type'   => $commissionType,     // Now: from CommissionSetting
                'commission_value'  => $commissionValue,    // Now: from CommissionSetting
                'owner_payout'      => $ownerPayout,        // Now: total - commission

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
     * Calculate booking pricing including platform fee.
     *
     * ✅ FIX: Now includes commission-based platform fee in the total.
     *    For advance payments, the fee is part of total_amount before
     *    advance calculation, so it's automatically included.
     *
     * @param Hall  $hall       The hall being booked
     * @param array $serviceIds Array of extra service IDs
     * @return array Pricing breakdown with platform fee
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

        // ✅ FIX: Calculate platform fee from CommissionSetting
        // Previously: $platformFee = 0;
        $feeData = $this->commissionService->calculateFees($hall, $subtotal);

        return [
            'hall_price'        => $hallPrice,
            'services_price'    => $servicesPrice,
            'subtotal'          => $subtotal,
            'platform_fee'      => (float) $feeData['platform_fee'],
            'commission_amount' => (float) $feeData['commission_amount'],
            'commission_type'   => $feeData['commission_type'],
            'commission_value'  => $feeData['commission_value'],
            'total_amount'      => (float) $feeData['total_amount'],
            'owner_payout'      => (float) $feeData['owner_payout'],
        ];
    }
}
