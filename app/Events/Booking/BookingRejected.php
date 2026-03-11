<?php

declare(strict_types=1);

namespace App\Events\Booking;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BookingRejected Event
 * 
 * Dispatched when a hall owner rejects a pending booking.
 * Triggers customer notification via email/SMS with rejection reason.
 * 
 * @package App\Events\Booking
 */
class BookingRejected
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Booking $booking The rejected booking
     * @param string $reason Reason for rejection (required)
     * @param int|null $rejectedBy User ID who rejected (owner)
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly string $reason,
        public readonly ?int $rejectedBy = null
    ) {
    }

    /**
     * Get the booking instance.
     *
     * @return Booking
     */
    public function getBooking(): Booking
    {
        return $this->booking;
    }

    /**
     * Get the rejection reason.
     *
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Get additional data for notification.
     *
     * @return array
     */
    public function getNotificationData(): array
    {
        return [
            'rejection_reason' => $this->reason,
            'rejected_by' => $this->rejectedBy,
            'rejected_at' => now()->toIso8601String(),
        ];
    }
}
