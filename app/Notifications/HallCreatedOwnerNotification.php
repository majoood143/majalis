<?php

namespace App\Notifications;

use App\Models\Hall;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HallCreatedOwnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Hall $hall,
        public readonly User $owner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hallName = $this->hall->getTranslation('name', app()->getLocale(), false)
            ?: $this->hall->getTranslation('name', 'en', false)
            ?: '';

        $publicUrl = config('app.url') . 'halls/' . $this->hall->slug;

        return (new MailMessage)
            ->subject(__('hall-owner.hall.created.email.owner_subject', ['hall' => $hallName]))
            ->view('emails.hall.created-owner', [
                'owner'      => $this->owner,
                'hall'       => $this->hall,
                'hallName'   => $hallName,
                'publicUrl'  => $publicUrl,
            ]);
    }
}
