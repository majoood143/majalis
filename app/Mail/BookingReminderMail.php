<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BookingReminderMail Mailable
 *
 * Email reminder sent to customers before their booking date.
 * Includes booking details and preparation information.
 *
 * @package App\Mail
 */
class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking The booking to remind about
     * @param string|null $customMessage Optional custom message from admin
     */
    public function __construct(
        public Booking $booking,
        public ?string $customMessage = null
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $hallName = $this->getHallName();
        $daysUntil = $this->booking->getDaysUntilBooking();

        return new Envelope(
            subject: __('booking.email.reminder_subject', [
                'hall' => $hallName,
                'days' => $daysUntil,
            ]),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.reminder',
            with: [
                'booking' => $this->booking,
                'hallName' => $this->getHallName(),
                'hallAddress' => $this->getHallAddress(),
                'customerName' => $this->booking->customer_name,
                'bookingDate' => $this->booking->booking_date->format('l, d M Y'),
                'timeSlot' => __('booking.time_slots.' . $this->booking->time_slot),
                'daysUntil' => $this->booking->getDaysUntilBooking(),
                'customMessage' => $this->customMessage,
                'hasBalanceDue' => $this->booking->hasBalanceDue(),
                'balanceDue' => $this->booking->balance_due,
            ],
        );
    }

    /**
     * Get the hall name in the current locale.
     *
     * @return string
     */
    protected function getHallName(): string
    {
        $name = $this->booking->hall->name ?? 'Hall';

        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? 'Hall';
        }

        return $name;
    }

    /**
     * Get the hall address/location.
     *
     * @return string
     */
    protected function getHallAddress(): string
    {
        $city = $this->booking->hall->city ?? null;

        if ($city) {
            $cityName = is_array($city->name)
                ? ($city->name[app()->getLocale()] ?? $city->name['en'] ?? '')
                : $city->name;

            return $cityName;
        }

        return '';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
