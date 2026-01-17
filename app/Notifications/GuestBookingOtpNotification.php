<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Hall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Guest Booking OTP Notification
 *
 * Sends a 6-digit OTP code to guest email for verification
 * before proceeding with the booking process.
 *
 * @package App\Notifications
 * @version 1.0.0
 */
class GuestBookingOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The OTP code to send.
     *
     * @var string
     */
    protected string $otp;

    /**
     * The guest's name.
     *
     * @var string
     */
    protected string $guestName;

    /**
     * The hall being booked.
     *
     * @var Hall
     */
    protected Hall $hall;

    /**
     * Create a new notification instance.
     *
     * @param string $otp The 6-digit OTP code
     * @param string $guestName The guest's name
     * @param Hall $hall The hall being booked
     */
    public function __construct(string $otp, string $guestName, Hall $hall)
    {
        $this->otp = $otp;
        $this->guestName = $guestName;
        $this->hall = $hall;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $locale = app()->getLocale();
        $hallName = $this->hall->getTranslation('name', $locale);

        return (new MailMessage())
            ->subject(__('guest.otp_email_subject', ['app' => config('app.name')]))
            ->greeting(__('guest.otp_email_greeting', ['name' => $this->guestName]))
            ->line(__('guest.otp_email_intro', ['hall' => $hallName]))
            ->line(__('guest.otp_email_code_label'))
            ->line("**{$this->otp}**")
            ->line(__('guest.otp_email_expires', ['minutes' => 10]))
            ->line(__('guest.otp_email_warning'))
            ->salutation(__('guest.otp_email_salutation', ['app' => config('app.name')]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'guest_booking_otp',
            'otp' => $this->otp,
            'hall_id' => $this->hall->id,
            'hall_name' => $this->hall->name,
        ];
    }
}
