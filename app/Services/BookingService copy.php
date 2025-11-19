<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\ExtraService;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BookingService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Create a new booking
     */
    public function createBooking(array $data): Booking
    {
        try {
            DB::beginTransaction();

            // Validate availability
            if (!$this->checkAvailability($data['hall_id'], $data['booking_date'], $data['time_slot'])) {
                throw new Exception('The selected time slot is not available.');
            }

            // Get hall
            $hall = Hall::findOrFail($data['hall_id']);

            // Validate capacity
            if ($data['number_of_guests'] < $hall->capacity_min || $data['number_of_guests'] > $hall->capacity_max) {
                throw new Exception("Guest count must be between {$hall->capacity_min} and {$hall->capacity_max}.");
            }

            // Create booking
            $booking = Booking::create([
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
                'event_details' => $data['event_details'] ?? null,
                'status' => BookingStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
            ]);

            // Calculate pricing
            $this->calculateBookingPricing($booking, $data['extra_services'] ?? []);

            // Attach extra services
            if (!empty($data['extra_services'])) {
                $this->attachExtraServices($booking, $data['extra_services']);
            }

            // Increment hall booking count
            $hall->incrementBookings();

            DB::commit();

            // Send notifications
            $this->notificationService->sendBookingCreatedNotification($booking);

            Log::info('Booking created successfully', ['booking_id' => $booking->id]);

            return $booking->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Check if slot is available
     */
    public function checkAvailability(int $hallId, string $date, string $timeSlot): bool
    {
        $hall = Hall::find($hallId);

        if (!$hall || !$hall->is_active) {
            return false;
        }

        return $hall->isAvailableOn($date, $timeSlot);
    }

    /**
     * Calculate booking pricing
     */
    protected function calculateBookingPricing(Booking $booking, array $extraServices = []): void
    {
        $extraServiceData = [];

        // Calculate extra services total
        foreach ($extraServices as $serviceData) {
            $service = ExtraService::find($serviceData['service_id']);
            if ($service) {
                $quantity = $serviceData['quantity'] ?? 1;
                $totalPrice = $service->calculatePrice($quantity);

                $extraServiceData[] = [
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ];
            }
        }

        $booking->calculateTotals($extraServiceData);
        $booking->save();
    }

    /**
     * Attach extra services to booking
     */
    protected function attachExtraServices(Booking $booking, array $extraServices): void
    {
        foreach ($extraServices as $serviceData) {
            $service = ExtraService::find($serviceData['service_id']);
            if ($service) {
                $quantity = $serviceData['quantity'] ?? 1;

                $booking->extraServices()->attach($service->id, [
                    'service_name' => $service->name,
                    'unit_price' => $service->price,
                    'quantity' => $quantity,
                    'total_price' => $service->calculatePrice($quantity),
                ]);
            }
        }
    }

    /**
     * Confirm a booking
     */
    public function confirmBooking(Booking $booking): bool
    {
        try {
            DB::beginTransaction();

            $booking->confirm();

            // Send confirmation notifications
            $this->notificationService->sendBookingConfirmedNotification($booking);

            DB::commit();

            Log::info('Booking confirmed', ['booking_id' => $booking->id]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking confirmation failed', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking, string $reason = null, int $userId = null): bool
    {
        try {
            DB::beginTransaction();

            if (!$booking->canBeCancelled()) {
                throw new Exception('This booking cannot be cancelled. Cancellation deadline has passed.');
            }

            // Process refund if payment was made
            if ($booking->isPaid()) {
                $refundAmount = $booking->total_amount;

                // Apply cancellation fee if applicable
                if ($booking->hall->cancellation_fee_percentage > 0) {
                    $cancellationFee = ($booking->total_amount * $booking->hall->cancellation_fee_percentage) / 100;
                    $refundAmount = $booking->total_amount - $cancellationFee;
                }

                // Process refund through payment service
                if ($booking->payment) {
                    $this->paymentService->refundPayment($booking->payment, $refundAmount, $reason);
                }
            }

            // Cancel booking
            $booking->cancel($reason);

            // Send cancellation notifications
            $this->notificationService->sendBookingCancelledNotification($booking);

            DB::commit();

            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'cancelled_by' => $userId,
                'reason' => $reason
            ]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Complete a booking (after event date)
     */
    public function completeBooking(Booking $booking): bool
    {
        try {
            DB::beginTransaction();

            if ($booking->booking_date->isFuture()) {
                throw new Exception('Cannot complete a booking that is in the future.');
            }

            if (!$booking->isConfirmed()) {
                throw new Exception('Only confirmed bookings can be completed.');
            }

            $booking->complete();

            // Send completion notification (request review)
            $this->notificationService->sendBookingCompletedNotification($booking);

            DB::commit();

            Log::info('Booking completed', ['booking_id' => $booking->id]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking completion failed', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get available slots for a hall on a specific date
     */
    public function getAvailableSlots(int $hallId, string $date): array
    {
        $hall = Hall::find($hallId);

        if (!$hall) {
            return [];
        }

        return $hall->getAvailableSlots($date);
    }

    /**
     * Get upcoming bookings for a user
     */
    public function getUserUpcomingBookings(int $userId, int $limit = 10)
    {
        return Booking::where('user_id', $userId)
            ->whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get bookings for a hall owner
     */
    public function getOwnerBookings(int $ownerId, array $filters = [])
    {
        $query = Booking::whereHas('hall', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('booking_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('booking_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['hall_id'])) {
            $query->where('hall_id', $filters['hall_id']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Send booking reminder
     */
    public function sendBookingReminder(Booking $booking): void
    {
        if ($booking->booking_date->isFuture() && $booking->isConfirmed()) {
            $this->notificationService->sendBookingReminderNotification($booking);
        }
    }

    /**
     * Auto-complete past bookings
     */
    public function autoCompletePastBookings(): int
    {
        $bookings = Booking::where('status', BookingStatus::CONFIRMED)
            ->where('booking_date', '<', now()->toDateString())
            ->get();

        $completed = 0;

        foreach ($bookings as $booking) {
            try {
                $this->completeBooking($booking);
                $completed++;
            } catch (Exception $e) {
                Log::error('Auto-complete failed', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            }
        }

        return $completed;
    }
}
