<?php

namespace App\Notifications;

use App\Models\Hall;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HallCreatedAdminNotification extends Notification implements ShouldQueue
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

        $adminUrl = config('app.url') . 'admin/halls/' . $this->hall->id;

        return (new MailMessage)
            ->subject(__('hall-owner.hall.created.email.admin_subject', [
                'hall'  => $hallName,
                'owner' => $this->owner->name,
            ]))
            ->view('emails.hall.created-admin', [
                'admin'    => $notifiable,
                'owner'    => $this->owner,
                'hall'     => $this->hall,
                'hallName' => $hallName,
                'adminUrl' => $adminUrl,
            ]);
    }
}
