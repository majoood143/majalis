<?php

namespace App\Notifications;

use App\Models\HallOwner;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HallOwnerApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $applicant,
        public readonly string $businessName,
        public readonly HallOwner $hallOwner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $adminUrl = config('app.url') . 'admin/hall-owners/' . $this->hallOwner->id ;

        return (new MailMessage)
            ->subject(__('hall-owner.registration.email.subject', ['business' => $this->businessName]))
            ->view('emails.hall-owner.application', [
                'admin'        => $notifiable,
                'applicant'    => $this->applicant,
                'businessName' => $this->businessName,
                'adminUrl'     => $adminUrl,
            ]);
    }
}
