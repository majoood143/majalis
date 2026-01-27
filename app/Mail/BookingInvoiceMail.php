<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Booking Invoice Email Mailable
 *
 * @package App\Mail
 */
class BookingInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Custom message for the email body.
     */
    public ?string $customMessage;

    /**
     * PDF content for attachment.
     */
    public ?string $pdfContent;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     * @param string $emailSubject  // ← Renamed to avoid conflict
     * @param string|null $customMessage
     * @param string|null $pdfContent
     */
    public function __construct(
        public Booking $booking,
        string $emailSubject,
        ?string $customMessage = null,
        ?string $pdfContent = null,
    ) {
        // Assign to parent's subject property
        $this->subject = $emailSubject;
        $this->customMessage = $customMessage;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.invoice',
            with: [
                'booking' => $this->booking,
                'customMessage' => $this->customMessage,
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
        if (!$this->pdfContent) {
            return [];
        }

        return [
            Attachment::fromData(
                fn() => $this->pdfContent,
                "invoice-{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
