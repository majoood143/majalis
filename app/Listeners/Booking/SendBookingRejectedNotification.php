<?php

declare(strict_types=1);

namespace App\Listeners\Booking;

use App\Enums\NotificationEvent;
use App\Events\Booking\BookingRejected;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * SendBookingRejectedNotification Listener
 * 
 * Handles the BookingRejected event and triggers customer notifications.
 * Includes the rejection reason in the notification.
 * 
 * @package App\Listeners\Booking
 */
class SendBookingRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The queue connection that should handle the job.
     *
     * @var string
     */
    public string $connection = 'database';

    /**
     * The queue that should handle the job.
     *
     * @var string
     */
    public string $queue = 'notifications';

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     *
     * @var int
     */
    public int $backoff = 60;

    /**
     * Create the event listener.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param BookingRejected $event
     * @return void
     */
    public function handle(BookingRejected $event): void
    {
        $booking = $event->getBooking();

        Log::info('Processing BookingRejected notification', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'reason' => $event->getReason(),
        ]);

        try {
            // Notify customer about rejection
            $this->notificationService->notifyCustomer(
                booking: $booking,
                event: NotificationEvent::BOOKING_REJECTED,
                additionalData: $event->getNotificationData()
            );

            Log::info('BookingRejected notification sent successfully', [
                'booking_id' => $booking->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send BookingRejected notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     *
     * @param BookingRejected $event
     * @param \Throwable $exception
     * @return void
     */
    public function failed(BookingRejected $event, \Throwable $exception): void
    {
        Log::critical('BookingRejected notification failed permanently', [
            'booking_id' => $event->booking->id,
            'booking_number' => $event->booking->booking_number,
            'reason' => $event->reason,
            'error' => $exception->getMessage(),
        ]);
    }
}
