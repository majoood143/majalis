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
     * Create a new message instance.
     *
     * @param Booking $booking
     * @param string $subject
     * @param string|null $customMessage
     * @param string|null $pdfContent
     */
    public function __construct(
        public Booking $booking,
        public string $subject,
        public ?string $customMessage = null,
        public ?string $pdfContent = null,
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
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
