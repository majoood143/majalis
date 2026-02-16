<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\ExtraService;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\CommissionSetting;
use App\Models\ServiceFeeSetting;

/**
 * Service for handling booking operations
 *
 * @package App\Services
 */
class BookingService
{
    /**
     * Check if a hall is available for a specific date and time slot
     *
     * @param int $hallId
     * @param string $bookingDate
     * @param string $timeSlot
     * @return bool
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
     * Create a new booking
     *
     * @param array $data
     * @return Booking
     * @throws Exception
     */
    public function createBooking(array $data): Booking
    {
        DB::beginTransaction();

        try {
            // Get hall details
            $hall = Hall::findOrFail($data['hall_id']);

            // Calculate hall price
            $hallPrice = $hall->price_per_slot;

            // Calculate services total
            $servicesPrice = 0;
            $selectedServices = [];

            if (!empty($data['extra_services'])) {
                $services = ExtraService::whereIn('id', $data['extra_services'])
                    ->where('is_active', true)
                    ->get();

                foreach ($services as $service) {
                    $servicesPrice += $service->price;
                    $selectedServices[] = [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price
                    ];
                }
            }


            // Calculate subtotal
            $subtotal = $hallPrice + $servicesPrice;

            // ==========================================================
            // 1. SERVICE FEE — Customer-facing (added to total_amount)
            // ==========================================================
            $platformFee = 0.0;
            $serviceFeeType = null;
            $serviceFeeValue = null;

            /** @var ServiceFeeSetting|null $serviceFee */
            $serviceFee = ServiceFeeSetting::resolveForHall($hall);

            if ($serviceFee) {
                $serviceFeeType  = $serviceFee->fee_type->value;
                $serviceFeeValue = (float) $serviceFee->fee_value;
                $platformFee     = $serviceFee->calculateFee($subtotal);

                Log::info('Service fee applied to booking', [
                    'fee_id'     => $serviceFee->id,
                    'fee_type'   => $serviceFeeType,
                    'fee_value'  => $serviceFeeValue,
                    'subtotal'   => $subtotal,
                    'fee_amount' => $platformFee,
                ]);
            }

            // Total = subtotal + service fee (what the customer pays)
            $totalAmount = $subtotal + $platformFee;

            // ==========================================================
            // 2. COMMISSION — Owner-side (deducted from payout)
            // ==========================================================
            $commissionAmount = 0;
            $commissionType = null;
            $commissionValue = null;

            /** @var CommissionSetting|null $commission */
            $commission = CommissionSetting::query()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('effective_from')
                        ->orWhere('effective_from', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now()->toDateString());
                })
                ->where(function ($q) use ($hall) {
                    $q->where('hall_id', $hall->id)
                        ->orWhere(function ($q2) use ($hall) {
                            $q2->whereNull('hall_id')
                                ->where('owner_id', $hall->owner_id);
                        })
                        ->orWhere(function ($q2) {
                            $q2->whereNull('hall_id')
                                ->whereNull('owner_id');
                        });
                })
                ->orderByRaw('CASE
                    WHEN hall_id IS NOT NULL THEN 1
                    WHEN owner_id IS NOT NULL THEN 2
                    ELSE 3
                END')
                ->first();

            if ($commission) {
                $commissionType  = $commission->commission_type->value;
                $commissionValue = (float) $commission->commission_value;

                if ($commissionType === 'percentage') {
                    $commissionAmount = round(($subtotal * $commissionValue) / 100, 2);
                } else {
                    $commissionAmount = round($commissionValue, 2);
                }

                Log::info('Commission applied to booking', [
                    'commission_id'     => $commission->id,
                    'commission_type'   => $commissionType,
                    'commission_value'  => $commissionValue,
                    'subtotal'          => $subtotal,
                    'commission_amount' => $commissionAmount,
                ]);
            }

            // ==========================================================
            // 3. OWNER PAYOUT = subtotal - commission
            // ==========================================================
            $ownerPayout = $subtotal - $commissionAmount;


            // Generate unique booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'hall_id' => $data['hall_id'],
                'user_id' => $data['user_id'],
                'booking_date' => $data['booking_date'],
                'time_slot' => $data['time_slot'],
                'number_of_guests' => $data['number_of_guests'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'event_type' => $data['event_type'] ?? null,
                'event_details' => isset($data['event_details']) ? json_encode($data['event_details']) : null,
                'hall_price' => $hallPrice,
                'services_price' => $servicesPrice,
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,
                'total_amount' => $totalAmount,
                'commission_amount'  => $commissionAmount,
                'commission_type'    => $commissionType,
                'commission_value'   => $commissionValue,
                'service_fee_type'   => $serviceFeeType,
                'service_fee_value'  => $serviceFeeValue,
                'owner_payout'       => $ownerPayout,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Attach extra services to booking
            if (!empty($selectedServices)) {
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

            DB::commit();

            // Load relationships for return
            $booking->load(['hall', 'user', 'extraServices']);

            return $booking;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed in service: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);

            throw new Exception('Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique booking number
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
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: BK-2025-00001
        return sprintf('BK-%s-%05d', $year, $newNumber);
    }

    /**
     * Calculate full booking pricing including service fee and commission.
     *
     * Returns all financial components needed for booking creation
     * and frontend price preview.
     *
     * @param Hall  $hall       The hall being booked
     * @param array $serviceIds Selected extra service IDs
     * @return array{
     *     hall_price: float,
     *     services_price: float,
     *     subtotal: float,
     *     platform_fee: float,
     *     service_fee_type: string|null,
     *     service_fee_value: float|null,
     *     total_amount: float,
     *     commission_amount: float,
     *     commission_type: string|null,
     *     commission_value: float|null,
     *     owner_payout: float,
     * }
     */
    public function calculatePricing(Hall $hall, array $serviceIds = []): array
    {
        $hallPrice = (float) $hall->price_per_slot;
        $servicesPrice = 0.0;

        if (!empty($serviceIds)) {
            $servicesPrice = (float) ExtraService::whereIn('id', $serviceIds)
                ->where('is_active', true)
                ->sum('price');
        }

        $subtotal = $hallPrice + $servicesPrice;

        // ── Service Fee (customer-facing) ──
        $platformFee = 0.0;
        $serviceFeeType = null;
        $serviceFeeValue = null;

        $serviceFee = ServiceFeeSetting::resolveForHall($hall);
        if ($serviceFee) {
            $serviceFeeType  = $serviceFee->fee_type->value;
            $serviceFeeValue = (float) $serviceFee->fee_value;
            $platformFee     = $serviceFee->calculateFee($subtotal);
        }

        $totalAmount = $subtotal + $platformFee;

        // ── Commission (owner-side) ──
        $commissionAmount = 0.0;
        $commissionType = null;
        $commissionValue = null;

        $commission = CommissionSetting::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->where(function ($q) use ($hall) {
                $q->where('hall_id', $hall->id)
                    ->orWhere(function ($q2) use ($hall) {
                        $q2->whereNull('hall_id')
                            ->where('owner_id', $hall->owner_id);
                    })
                    ->orWhere(function ($q2) {
                        $q2->whereNull('hall_id')
                            ->whereNull('owner_id');
                    });
            })
            ->orderByRaw('CASE
                WHEN hall_id IS NOT NULL THEN 1
                WHEN owner_id IS NOT NULL THEN 2
                ELSE 3
            END')
            ->first();

        if ($commission) {
            $commissionType  = $commission->commission_type->value;
            $commissionValue = (float) $commission->commission_value;
            $commissionAmount = ($commissionType === 'percentage')
                ? round(($subtotal * $commissionValue) / 100, 2)
                : round($commissionValue, 2);
        }

        $ownerPayout = $subtotal - $commissionAmount;

        return [
            'hall_price'        => $hallPrice,
            'services_price'    => $servicesPrice,
            'subtotal'          => $subtotal,
            'platform_fee'      => $platformFee,
            'service_fee_type'  => $serviceFeeType,
            'service_fee_value' => $serviceFeeValue,
            'total_amount'      => $totalAmount,
            'commission_amount' => $commissionAmount,
            'commission_type'   => $commissionType,
            'commission_value'  => $commissionValue,
            'owner_payout'      => $ownerPayout,
        ];
    }
}
