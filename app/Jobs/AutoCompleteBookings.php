<?php

namespace App\Jobs;

use App\Services\BookingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoCompleteBookings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(BookingService $bookingService): void
    {
        try {
            $completed = $bookingService->autoCompletePastBookings();

            Log::info('Auto-completed past bookings', ['count' => $completed]);
        } catch (\Exception $e) {
            Log::error('Auto-complete bookings job failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
