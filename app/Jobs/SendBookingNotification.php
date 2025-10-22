<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking,
        public string $notificationType
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            match ($this->notificationType) {
                'created' => $notificationService->sendBookingCreatedNotification($this->booking),
                'confirmed' => $notificationService->sendBookingConfirmedNotification($this->booking),
                'cancelled' => $notificationService->sendBookingCancelledNotification($this->booking),
                'completed' => $notificationService->sendBookingCompletedNotification($this->booking),
                'reminder' => $notificationService->sendBookingReminderNotification($this->booking),
                default => Log::warning('Unknown notification type', ['type' => $this->notificationType])
            };

            Log::info('Booking notification sent', [
                'booking_id' => $this->booking->id,
                'type' => $this->notificationType
            ]);
        } catch (\Exception $e) {
            Log::error('Booking notification job failed', [
                'booking_id' => $this->booking->id,
                'type' => $this->notificationType,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Booking notification job failed permanently', [
            'booking_id' => $this->booking->id,
            'type' => $this->notificationType,
            'error' => $exception->getMessage()
        ]);
    }
}
