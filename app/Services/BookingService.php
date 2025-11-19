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

            // Calculate pricing
            $subtotal = $hallPrice + $servicesPrice;
            $platformFee = 0; // You can add platform fee calculation here
            $totalAmount = $subtotal + $platformFee;

            // Calculate commission (if hall has commission settings)
            $commissionAmount = 0;
            $commissionType = null;
            $commissionValue = null;

            if ($hall->commission_type && $hall->commission_value) {
                $commissionType = $hall->commission_type;
                $commissionValue = $hall->commission_value;

                if ($commissionType === 'percentage') {
                    $commissionAmount = ($subtotal * $commissionValue) / 100;
                } else {
                    $commissionAmount = $commissionValue;
                }
            }

            // Calculate owner payout
            $ownerPayout = $totalAmount - $commissionAmount;

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
                'commission_amount' => $commissionAmount,
                'commission_type' => $commissionType,
                'commission_value' => $commissionValue,
                'owner_payout' => $ownerPayout,
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
     * Calculate booking pricing
     *
     * @param Hall $hall
     * @param array $serviceIds
     * @return array
     */
    public function calculatePricing(Hall $hall, array $serviceIds = []): array
    {
        $hallPrice = $hall->price_per_slot;
        $servicesPrice = 0;

        if (!empty($serviceIds)) {
            $servicesPrice = ExtraService::whereIn('id', $serviceIds)
                ->where('is_active', true)
                ->sum('price');
        }

        $subtotal = $hallPrice + $servicesPrice;
        $platformFee = 0; // Add your platform fee logic here
        $totalAmount = $subtotal + $platformFee;

        return [
            'hall_price' => $hallPrice,
            'services_price' => $servicesPrice,
            'subtotal' => $subtotal,
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
        ];
    }
}
