<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Guest Booking Confirmation Notification
 *
 * Sends booking confirmation email to guest after successful payment.
 * Includes booking details, hall information, and access link.
 *
 * @package App\Notifications
 * @version 1.0.0
 */
class GuestBookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The confirmed booking.
     *
     * @var Booking
     */
    protected Booking $booking;

    /**
     * Create a new notification instance.
     *
     * @param Booking $booking The confirmed booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
        $booking = $this->booking;

        // Load relationships if not loaded
        $booking->loadMissing(['hall.city.region']);

        $hallName = $booking->hall->getTranslation('name', $locale);
        $cityName = $booking->hall->city?->getTranslation('name', $locale) ?? '';

        // Format time slot for display
        $timeSlotLabels = [
            'morning' => __('halls.time_slot_morning'),
            'afternoon' => __('halls.time_slot_afternoon'),
            'evening' => __('halls.time_slot_evening'),
            'full_day' => __('halls.time_slot_full_day'),
        ];
        $timeSlot = $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot;

        // Build booking details URL
        $bookingUrl = route('guest.booking.show', ['guest_token' => $booking->guest_token]);

        return (new MailMessage())
            ->subject(__('guest.confirmation_email_subject', [
                'booking_number' => $booking->booking_number,
            ]))
            ->greeting(__('guest.confirmation_email_greeting', ['name' => $booking->customer_name]))
            ->line(__('guest.confirmation_email_intro'))
            ->line('---')
            ->line("**" . __('Booking Number') . ":** {$booking->booking_number}")
            ->line("**" . __('Hall') . ":** {$hallName}")
            ->line("**" . __('Location') . ":** {$cityName}")
            ->line("**" . __('Date') . ":** {$booking->booking_date->format('l, F j, Y')}")
            ->line("**" . __('Time Slot') . ":** {$timeSlot}")
            ->line("**" . __('Guests') . ":** {$booking->number_of_guests}")
            ->line("**" . __('Total Amount') . ":** {$booking->total_amount} " . __('OMR'))
            ->line('---')
            ->action(__('guest.view_booking_details'), $bookingUrl)
            ->line(__('guest.confirmation_email_access_info'))
            ->line(__('guest.confirmation_email_create_account_hint'))
            ->salutation(__('guest.confirmation_email_salutation', ['app' => config('app.name')]));
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
            'type' => 'guest_booking_confirmation',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'hall_id' => $this->booking->hall_id,
            'total_amount' => $this->booking->total_amount,
        ];
    }
}
