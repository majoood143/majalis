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

class SendBookingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            // Get bookings happening tomorrow
            $tomorrow = now()->addDay()->toDateString();

            $bookings = Booking::where('booking_date', $tomorrow)
                ->where('status', 'confirmed')
                ->with(['hall', 'user'])
                ->get();

            $sent = 0;

            foreach ($bookings as $booking) {
                try {
                    $notificationService->sendBookingReminderNotification($booking);
                    $sent++;
                } catch (\Exception $e) {
                    Log::error('Failed to send reminder', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Booking reminders sent', [
                'total' => $bookings->count(),
                'sent' => $sent
            ]);
        } catch (\Exception $e) {
            Log::error('Send booking reminders job failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
