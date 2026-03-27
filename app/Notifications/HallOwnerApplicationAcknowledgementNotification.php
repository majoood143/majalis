<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HallOwnerApplicationAcknowledgementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $businessName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('hall-owner.registration.email.ack_subject', ['business' => $this->businessName]))
            ->view('emails.hall-owner.acknowledgement', [
                'applicant'    => $notifiable,
                'businessName' => $this->businessName,
            ]);
    }
}
