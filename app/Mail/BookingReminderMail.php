<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BookingReminderMail Mailable
 *
 * Email reminder sent to customers before their booking date.
 * Includes booking details and preparation information.
 *
 * Supports bilingual content (English/Arabic) with proper view resolution.
 *
 * IMPORTANT FIXES:
 * - Uses 'markdown:' parameter because template uses <x-mail::message> components
 * - Requires vendor mail views to be published: php artisan vendor:publish --tag=laravel-mail
 *
 * @package App\Mail
 * @version 2.1.0
 */
class BookingReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

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
     * Defines the email's metadata including sender, subject, and recipients.
     * Subject is translated based on the application locale.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $hallName = $this->getHallName();
        $daysUntil = $this->booking->getDaysUntilBooking();

        return new Envelope(
            // Explicitly set the from address to ensure proper delivery
            from: new Address(
                address: (string) config('mail.from.address'),
                name: (string) config('mail.from.name')
            ),
            // Translate the subject based on the current application locale
            subject: __('booking.email.reminder_subject', [
                'hall' => $hallName,
                'days' => $daysUntil,
            ]),
        );
    }

    /**
     * Get the message content definition.
     *
     * Specifies the view files and data to be used for rendering the email.
     *
     * CRITICAL: Uses 'markdown:' because the template uses <x-mail::message> components.
     * If you get "No hint path defined for [mail]" error, run:
     *   php artisan vendor:publish --tag=laravel-mail
     *
     * @return Content
     */
    public function content(): Content
    {
        // Prepare view data for email template
        $viewData = [
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
        ];

        /**
         * FIX: Use 'markdown:' because the template uses <x-mail::message> components
         *
         * The <x-mail::message>, <x-mail::panel>, <x-mail::button> components
         * are Blade components that ONLY work when using the markdown: parameter.
         *
         * If you see "No hint path defined for [mail]" error:
         * Run: php artisan vendor:publish --tag=laravel-mail
         */
        return new Content(
            markdown: 'emails.booking.reminder',
            with: $viewData,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * Currently returns an empty array. Extend this method to add:
     * - PDF confirmations
     * - Booking contracts
     * - Invoice documents
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the hall name in the current locale.
     *
     * Handles both translated (array) and regular string hall names.
     * Supports JSON translations from database.
     *
     * @return string The hall name in the current application locale
     */
    protected function getHallName(): string
    {
        $name = $this->booking->hall->name ?? 'Hall';

        // Handle translated names stored as JSON arrays
        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? 'Hall';
        }

        return (string) $name;
    }

    /**
     * Get the hall address/location in the current locale.
     *
     * Resolves the city relationship and returns the localized city name.
     * Handles both array (JSON) and string city names.
     *
     * @return string The hall location/city name
     */
    protected function getHallAddress(): string
    {
        $city = $this->booking->hall->city ?? null;

        if ($city) {
            // Handle both string and array (JSON) city names
            $cityName = is_array($city->name)
                ? ($city->name[app()->getLocale()] ?? $city->name['en'] ?? '')
                : (string) $city->name;

            return $cityName;
        }

        return '';
    }
}
