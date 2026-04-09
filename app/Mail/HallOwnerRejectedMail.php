<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\HallOwner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HallOwnerRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HallOwner $hallOwner,
        public ?string $reason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.owner.rejected.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.owner.rejected',
            with: [
                'owner'  => $this->hallOwner->user,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
