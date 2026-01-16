<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingInvoiceMail;

/**
 * Job to send booking invoice via email
 *
 * Handles PDF generation and email dispatch asynchronously
 * to prevent blocking the admin interface.
 *
 * @package App\Jobs
 */
class SendBookingInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retry attempts
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Backoff intervals in seconds
     *
     * @var array<int>
     */
    public array $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param Booking $booking The booking record
     * @param string $email Recipient email address
     * @param string $subject Email subject line
     * @param string|null $message Custom message body
     * @param bool $attachPdf Whether to attach PDF invoice
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
     *
     * @param InvoiceService $invoiceService
     * @return void
     */
    public function handle(InvoiceService $invoiceService): void
    {
        // Load relationships for invoice generation
        $this->booking->load(['hall', 'extraServices', 'user']);

        // Generate PDF if needed
        $pdfContent = null;
        if ($this->attachPdf) {
            $pdf = $invoiceService->generateInvoice($this->booking);
            $pdfContent = $pdf->output();
        }

        // Send email
        Mail::to($this->email)->send(new BookingInvoiceMail(
            booking: $this->booking,
            subject: $this->subject,
            customMessage: $this->message,
            pdfContent: $pdfContent,
        ));
    }
}
