<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * TicketSubmittedCustomerMail
 *
 * Confirmation email sent to customers/guests after submitting a support ticket.
 * Includes the ticket number and instructions for tracking.
 */
class TicketSubmittedCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?string $customerName = null,
        public ?string $customerEmail = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.ticket.submitted_customer_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    public function content(): Content
    {
        $customerName = $this->customerName
            ?? $this->ticket->user?->name
            ?? $this->ticket->metadata['guest_name']
            ?? 'Customer';

        $customerEmail = $this->customerEmail
            ?? $this->ticket->user?->email
            ?? $this->ticket->metadata['guest_email']
            ?? null;

        return new Content(
            markdown: 'emails.ticket.submitted-customer',
            with: [
                'ticket'    => $this->ticket,
                'ticketUrl' => $this->ticket->user_id
                    ? route('customer.tickets.show', $this->ticket)
                    : null,
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
