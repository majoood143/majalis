<?php

declare(strict_types=1);

namespace App\Jobs\Notification;

use App\Enums\NotificationType;
use App\Mail\Booking\BookingNotificationMail;
use App\Models\BookingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendNotificationJob
 * 
 * Queue job that handles the actual sending of notifications.
 * Supports multiple channels (email, SMS, etc.) and handles retries.
 * 
 * @package App\Jobs\Notification
 */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     *
     * @var array<int>
     */
    public array $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @param BookingNotification $notification
     */
    public function __construct(
        public BookingNotification $notification
    ) {
    }

    /**
     * Get the unique ID for the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return 'notification-' . $this->notification->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Refresh notification from database
        $this->notification->refresh();

        // Skip if already processed
        if ($this->notification->isSent() || $this->notification->status === 'cancelled') {
            Log::info('Notification already processed, skipping', [
                'notification_id' => $this->notification->id,
                'status' => $this->notification->status,
            ]);
            return;
        }

        // Mark as processing
        $this->notification->markAsProcessing();

        Log::info('Processing notification', [
            'notification_id' => $this->notification->id,
            'type' => $this->notification->type,
            'event' => $this->notification->event,
            'recipient_email' => $this->notification->recipient_email,
        ]);

        try {
            // Send based on notification type
            match ($this->notification->type) {
                NotificationType::EMAIL->value => $this->sendEmail(),
                NotificationType::SMS->value => $this->sendSms(),
                NotificationType::PUSH->value => $this->sendPush(),
                NotificationType::WHATSAPP->value => $this->sendWhatsApp(),
                default => throw new \InvalidArgumentException("Unknown notification type: {$this->notification->type}"),
            };

            // Mark as sent
            $this->notification->markAsSent();

            Log::info('Notification sent successfully', [
                'notification_id' => $this->notification->id,
            ]);
        } catch (\Throwable $e) {
            $this->handleFailure($e);
            throw $e; // Re-throw to trigger queue retry
        }
    }

    /**
     * Send email notification.
     *
     * @return void
     */
    protected function sendEmail(): void
    {
        if (empty($this->notification->recipient_email)) {
            throw new \InvalidArgumentException('No recipient email address');
        }

        // Load booking relationship
        $booking = $this->notification->booking;
        if (!$booking) {
            throw new \RuntimeException('Booking not found for notification');
        }

        // Send the email
        Mail::to($this->notification->recipient_email)
            ->send(new BookingNotificationMail(
                notification: $this->notification,
                booking: $booking
            ));
    }

    /**
     * Send SMS notification.
     *
     * @return void
     */
    protected function sendSms(): void
    {
        if (empty($this->notification->recipient_phone)) {
            throw new \InvalidArgumentException('No recipient phone number');
        }

        // TODO: Implement SMS sending via configured provider
        // For now, log that SMS would be sent
        Log::info('SMS notification would be sent (not implemented)', [
            'notification_id' => $this->notification->id,
            'phone' => $this->notification->recipient_phone,
            'message' => $this->notification->message,
        ]);

        // Uncomment when SMS provider is configured:
        // app(SmsChannel::class)->send(
        //     $this->notification->recipient_phone,
        //     $this->notification->message
        // );
    }

    /**
     * Send push notification.
     *
     * @return void
     */
    protected function sendPush(): void
    {
        // TODO: Implement push notification via FCM
        Log::info('Push notification would be sent (not implemented)', [
            'notification_id' => $this->notification->id,
            'user_id' => $this->notification->user_id,
        ]);
    }

    /**
     * Send WhatsApp notification.
     *
     * @return void
     */
    protected function sendWhatsApp(): void
    {
        // TODO: Implement WhatsApp Business API
        Log::info('WhatsApp notification would be sent (not implemented)', [
            'notification_id' => $this->notification->id,
            'phone' => $this->notification->recipient_phone,
        ]);
    }

    /**
     * Handle job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    protected function handleFailure(\Throwable $exception): void
    {
        $this->notification->markAsFailed($exception->getMessage());

        Log::error('Notification sending failed', [
            'notification_id' => $this->notification->id,
            'type' => $this->notification->type,
            'error' => $exception->getMessage(),
            'retry_count' => $this->notification->retry_count,
        ]);
    }

    /**
     * Handle a job failure after all retries.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Notification failed permanently after all retries', [
            'notification_id' => $this->notification->id,
            'type' => $this->notification->type,
            'event' => $this->notification->event,
            'error' => $exception->getMessage(),
        ]);

        // Ensure notification is marked as failed
        $this->notification->markAsFailed(
            'Permanent failure: ' . $exception->getMessage()
        );
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'notification',
            'notification:' . $this->notification->id,
            'type:' . $this->notification->type,
            'event:' . $this->notification->event,
            'booking:' . $this->notification->booking_id,
        ];
    }
}
