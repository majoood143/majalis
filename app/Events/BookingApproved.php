<?php

declare(strict_types=1);

namespace App\Events\Booking;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BookingApproved Event
 * 
 * Dispatched when a hall owner approves a pending booking.
 * Triggers customer notification via email/SMS.
 * 
 * @package App\Events\Booking
 */
class BookingApproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Booking $booking The approved booking
     * @param int|null $approvedBy User ID who approved (owner)
     * @param string|null $notes Optional notes from owner
     */
    public function __construct(
        public readonly Booking $booking,
        public readonly ?int $approvedBy = null,
        public readonly ?string $notes = null
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
     * Get additional data for notification.
     *
     * @return array
     */
    public function getNotificationData(): array
    {
        return [
            'approved_by' => $this->approvedBy,
            'approval_notes' => $this->notes,
            'approved_at' => now()->toIso8601String(),
        ];
    }
}
