<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\PDFService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PDFService $pdfService): void
    {
        try {
            $pdfService->generateBookingInvoice($this->booking);

            Log::info('Invoice generated', ['booking_id' => $this->booking->id]);
        } catch (\Exception $e) {
            Log::error('Invoice generation job failed', [
                'booking_id' => $this->booking->id,
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
        Log::error('Invoice generation job failed permanently', [
            'booking_id' => $this->booking->id,
            'error' => $exception->getMessage()
        ]);
    }
}
