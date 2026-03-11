<?php

declare(strict_types=1);

namespace App\Events\Booking;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingApproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly ?int $approvedBy = null
    ) {
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }

    public function getNotificationData(): array
    {
        return [
            'approved_by' => $this->approvedBy,
            'approved_at' => now()->toIso8601String(),
        ];
    }
}
