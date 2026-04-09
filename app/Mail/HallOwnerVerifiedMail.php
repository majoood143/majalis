<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HallOwner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HallOwnerVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public HallOwner $hallOwner) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.owner.verified.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.owner.verified',
            with: [
                'owner' => $this->hallOwner->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
