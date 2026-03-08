<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use App\Services\BookingPdfService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Guest Booking Confirmation Notification
 *
 * Sends booking confirmation email to guest after successful payment.
 * Includes booking details, hall information, and access link.
 *
 * @package App\Notifications
 * @version 1.0.0
 */
class GuestBookingConfirmationNotification extends Notification
{

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

        // Pre-compute all values as strings to prevent "Array to string conversion"
        $hallName     = $this->getHallName($booking, $locale);
        $cityName     = $this->getCityName($booking, $locale);
        $customerName = is_array($booking->customer_name)
            ? ($booking->customer_name[$locale] ?? $booking->customer_name['en'] ?? 'Guest')
            : (string) ($booking->customer_name ?? 'Guest');

        $bookingDate = $booking->booking_date instanceof \Carbon\Carbon
            ? $booking->booking_date->format('l, F j, Y')
            : (string) ($booking->booking_date ?? 'N/A');

        $timeSlotMap = [
            'morning'   => 'Morning',
            'afternoon' => 'Afternoon',
            'evening'   => 'Evening',
            'full_day'  => 'Full Day',
        ];
        $timeSlot = $timeSlotMap[(string) $booking->time_slot] ?? (string) $booking->time_slot;

        $bookingNumber = (string) ($booking->booking_number ?? 'N/A');
        $totalAmount   = (string) ($booking->total_amount ?? '0.000');
        $numGuests     = (string) ($booking->number_of_guests ?? '0');

        $bookingUrl = route('guest.booking.show', ['guest_token' => $booking->guest_token]);

        $subject    = (string) __('guest.confirmation_email_subject', ['booking_number' => $bookingNumber]);
        $greeting   = (string) __('guest.confirmation_email_greeting', ['name' => $customerName]);
        $intro      = (string) __('guest.confirmation_email_intro');
        $viewLabel  = (string) __('guest.view_booking_details');
        $accessInfo = (string) __('guest.confirmation_email_access_info');
        $hintInfo   = (string) __('guest.confirmation_email_create_account_hint');
        $salutation = (string) __('guest.confirmation_email_salutation', ['app' => (string) config('app.name')]);

        $mail = (new MailMessage())
            ->subject($subject)
            ->greeting($greeting)
            ->line($intro)
            ->line('---')
            ->line("**Booking Number:** {$bookingNumber}")
            ->line("**Hall:** {$hallName}")
            ->line("**Location:** {$cityName}")
            ->line("**Date:** {$bookingDate}")
            ->line("**Time Slot:** {$timeSlot}")
            ->line("**Guests:** {$numGuests}")
            ->line("**Total Amount:** {$totalAmount} OMR")
            ->line('---')
            ->action($viewLabel, $bookingUrl)
            ->line($accessInfo)
            ->line($hintInfo)
            ->line('A PDF copy of your booking confirmation is attached to this email.')
            ->salutation($salutation);

        $this->attachPdf($mail, $booking);

        return $mail;
    }

    /**
     * Get localized hall name, safely handling both Spatie Translatable
     * (getTranslation) and plain array/string casts.
     */
    protected function getHallName(Booking $booking, string $locale): string
    {
        if (!$booking->hall) {
            return 'N/A';
        }

        if (method_exists($booking->hall, 'getTranslation')) {
            $name = $booking->hall->getTranslation('name', $locale)
                ?? $booking->hall->getTranslation('name', 'en');
            if (is_string($name) && $name !== '') {
                return $name;
            }
        }

        $name = $booking->hall->name;
        if (is_array($name)) {
            return $name[$locale] ?? $name['en'] ?? 'N/A';
        }

        return (string) ($name ?? 'N/A');
    }

    /**
     * Get localized city name, safely handling both Spatie Translatable
     * and plain array/string casts.
     */
    protected function getCityName(Booking $booking, string $locale): string
    {
        $city = $booking->hall?->city;
        if (!$city) {
            return '';
        }

        if (method_exists($city, 'getTranslation')) {
            $name = $city->getTranslation('name', $locale)
                ?? $city->getTranslation('name', 'en');
            if (is_string($name) && $name !== '') {
                return $name;
            }
        }

        $name = $city->name;
        if (is_array($name)) {
            return $name[$locale] ?? $name['en'] ?? '';
        }

        return (string) ($name ?? '');
    }

    /**
     * Attach the booking confirmation PDF to the email.
     * If already generated (invoice_path set), reuses it; otherwise generates fresh.
     *
     * @param MailMessage $mail
     * @param Booking $booking
     * @return void
     */
    protected function attachPdf(MailMessage $mail, Booking $booking): void
    {
        try {
            $pdfPath = $booking->invoice_path;

            if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
                $pdfPath = app(BookingPdfService::class)->generateConfirmation($booking);
            }

            $fullPath = Storage::disk('public')->path($pdfPath);

            if (file_exists($fullPath)) {
                $mail->attach($fullPath, [
                    'as'   => $booking->booking_number . '.pdf',
                    'mime' => 'application/pdf',
                ]);
                Log::info('PDF attached to guest confirmation email', [
                    'booking_id' => $booking->id,
                    'pdf_path'   => $pdfPath,
                ]);
            } else {
                Log::warning('Guest PDF file not found for attachment', [
                    'booking_id'    => $booking->id,
                    'expected_path' => $fullPath,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to attach PDF to guest confirmation email (email will still be sent)', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }
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
