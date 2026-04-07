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
 * TicketSubmittedAdminMail
 *
 * Notification email sent to admins when a new support ticket is submitted.
 * Includes ticket details and a link to view and assign the ticket.
 */
class TicketSubmittedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.ticket.submitted_admin_subject', [
                'ticket_number' => $this->ticket->ticket_number,
            ]),
        );
    }

    public function content(): Content
    {
        $customerName = $this->ticket->user?->name
            ?? $this->ticket->metadata['guest_name']
            ?? 'Guest';

        $customerEmail = $this->ticket->user?->email
            ?? $this->ticket->metadata['guest_email']
            ?? 'N/A';

        return new Content(
            markdown: 'emails.ticket.submitted-admin',
            with: [
                'ticket'         => $this->ticket,
                'ticketUrl'      => route('filament.admin.resources.tickets.view', $this->ticket),
                'customerName'   => $customerName,
                'customerEmail'  => $customerEmail,
                'isGuestTicket'  => $this->ticket->isGuestTicket(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
