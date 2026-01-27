<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingInvoiceMail;

/**
 * Job to send booking invoice via email
 *
 * NOTE: Removed ShouldQueue to send immediately.
 * Add it back once queue worker is running in production.
 *
 * @package App\Jobs
 */
class SendBookingInvoiceEmail
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking,
        public string $email,
        public string $subject,
        public ?string $message = null,
        public bool $attachPdf = true,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(InvoiceService $invoiceService): void
    {
        // Load relationships for invoice generation
        $this->booking->load(['hall', 'extraServices', 'user']);

        // Generate PDF if needed
        $pdfContent = null;
        if ($this->attachPdf) {
            try {
                $pdf = $invoiceService->generateFullReceipt($this->booking);
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                Log::error('PDF generation failed for invoice email', [
                    'booking_id' => $this->booking->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue without PDF attachment
            }
        }

        // Send email - use positional arguments (not named)
        Mail::to($this->email)->send(new BookingInvoiceMail(
            $this->booking,      // booking
            $this->subject,      // emailSubject
            $this->message,      // customMessage
            $pdfContent          // pdfContent
        ));

        Log::info('Invoice email sent', [
            'booking_id' => $this->booking->id,
            'email' => $this->email,
        ]);
    }
}
