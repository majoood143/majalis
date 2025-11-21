<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Payment Receipt Email
 *
 * Sends payment receipt PDF to customer via email.
 *
 * @package App\Mail
 */
class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The payment instance
     *
     * @var Payment
     */
    public Payment $payment;

    /**
     * Create a new message instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Receipt - ' . $this->payment->payment_reference,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        // Load relationships
        $this->payment->load('booking.hall');

        return new Content(
            view: 'emails.payment-receipt',
            with: [
                'payment' => $this->payment,
                'booking' => $this->payment->booking,
                'hall' => $this->payment->booking?->hall,
                'customerName' => $this->payment->booking?->customer_name ?? 'Valued Customer',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Load relationships if not loaded
        $this->payment->load('booking.hall');

        // Generate PDF content at send time (not in constructor)
        $pdfContent = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $this->payment,
            'booking' => $this->payment->booking,
            'hall' => $this->payment->booking?->hall,
        ])->output();

        $filename = 'receipt_' . $this->payment->payment_reference . '.pdf';

        return [
            Attachment::fromData(fn() => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
