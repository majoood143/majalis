<?php

declare(strict_types=1);

namespace App\Mail\Booking;

use App\Enums\NotificationEvent;
use App\Models\Booking;
use App\Models\BookingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * BookingNotificationMail
 * 
 * Mailable class for all booking-related email notifications.
 * Supports bilingual (EN/AR) email templates with RTL support.
 * 
 * @package App\Mail\Booking
 */
class BookingNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The locale for this email.
     *
     * @var string
     */
    protected string $emailLocale;

    /**
     * Create a new message instance.
     *
     * @param BookingNotification $notification
     * @param Booking $booking
     */
    public function __construct(
        public readonly BookingNotification $notification,
        public readonly Booking $booking
    ) {
        $this->emailLocale = $notification->data['locale'] ?? 'en';
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@majalis.om'),
                config('mail.from.name', 'Majalis')
            ),
            subject: $this->notification->subject ?? $this->getDefaultSubject(),
            tags: [
                'booking-notification',
                'event-' . $this->notification->event,
            ],
            metadata: [
                'booking_id' => $this->booking->id,
                'booking_number' => $this->booking->booking_number,
                'notification_id' => $this->notification->id,
            ]
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        $event = NotificationEvent::tryFrom($this->notification->event);
        $template = $this->getTemplateName($event);

        return new Content(
            view: $template,
            with: $this->getTemplateData()
        );
    }

    /**
     * Get template name based on event.
     *
     * @param NotificationEvent|null $event
     * @return string
     */
    protected function getTemplateName(?NotificationEvent $event): string
    {
        if (!$event) {
            return 'emails.booking.generic';
        }

        $templateMap = [
            NotificationEvent::BOOKING_APPROVED->value => 'emails.booking.approved',
            NotificationEvent::BOOKING_REJECTED->value => 'emails.booking.rejected',
            NotificationEvent::BOOKING_CONFIRMED->value => 'emails.booking.confirmed',
            NotificationEvent::BOOKING_CANCELLED->value => 'emails.booking.cancelled',
            NotificationEvent::PAYMENT_RECEIVED->value => 'emails.booking.payment-received',
            NotificationEvent::BALANCE_RECEIVED->value => 'emails.booking.balance-received',
            NotificationEvent::EVENT_REMINDER->value => 'emails.booking.reminder',
        ];

        return $templateMap[$event->value] ?? 'emails.booking.generic';
    }

    /**
     * Get template data for the email.
     *
     * @return array
     */
    protected function getTemplateData(): array
    {
        $hall = $this->booking->hall;
        $hallName = $this->getHallName($hall);

        return [
            // Locale
            'locale' => $this->emailLocale,
            'isRtl' => $this->emailLocale === 'ar',
            'direction' => $this->emailLocale === 'ar' ? 'rtl' : 'ltr',

            // Booking Details
            'bookingNumber' => $this->booking->booking_number,
            'customerName' => $this->booking->customer_name,
            'hallName' => $hallName,
            'bookingDate' => $this->booking->booking_date?->format('l, d F Y'),
            'bookingDateShort' => $this->booking->booking_date?->format('d M Y'),
            'timeSlot' => $this->getTimeSlotLabel($this->booking->time_slot),
            'numberOfGuests' => $this->booking->number_of_guests,
            'eventType' => ucfirst($this->booking->event_type ?? 'Event'),

            // Financial
            'hallPrice' => 'OMR ' . number_format((float) $this->booking->hall_price, 3),
            'servicesPrice' => 'OMR ' . number_format((float) $this->booking->services_price, 3),
            'totalAmount' => 'OMR ' . number_format((float) $this->booking->total_amount, 3),
            'advanceAmount' => $this->booking->advance_amount 
                ? 'OMR ' . number_format((float) $this->booking->advance_amount, 3) 
                : null,
            'balanceDue' => $this->booking->balance_due 
                ? 'OMR ' . number_format((float) $this->booking->balance_due, 3) 
                : null,

            // Status
            'status' => $this->booking->status,
            'paymentStatus' => $this->booking->payment_status,

            // Notification specific data
            'notificationData' => $this->notification->data ?? [],
            'rejectionReason' => $this->notification->data['rejection_reason'] ?? null,

            // Contact Info
            'hallPhone' => $hall?->phone,
            'hallEmail' => $hall?->email,
            'hallAddress' => $hall?->address,

            // Links
            'viewBookingUrl' => $this->getViewBookingUrl(),
            'supportUrl' => config('app.url') . '/contact',
            'logoUrl' => config('app.url') . '/images/logo.png',

            // Branding
            'appName' => config('app.name', 'Majalis'),
            'appUrl' => config('app.url'),
            'supportEmail' => config('mail.from.address', 'support@majalis.om'),

            // Localized strings
            'strings' => $this->getLocalizedStrings(),
        ];
    }

    /**
     * Get localized hall name.
     *
     * @param mixed $hall
     * @return string
     */
    protected function getHallName($hall): string
    {
        if (!$hall) {
            return 'N/A';
        }

        $name = $hall->name;
        if (is_array($name)) {
            return $name[$this->emailLocale] ?? $name['en'] ?? 'N/A';
        }

        return (string) $name;
    }

    /**
     * Get localized time slot label.
     *
     * @param string|null $slot
     * @return string
     */
    protected function getTimeSlotLabel(?string $slot): string
    {
        if (!$slot) {
            return 'N/A';
        }

        $labels = [
            'en' => [
                'morning' => 'Morning (8:00 AM - 12:00 PM)',
                'afternoon' => 'Afternoon (1:00 PM - 5:00 PM)',
                'evening' => 'Evening (6:00 PM - 11:00 PM)',
                'full_day' => 'Full Day',
            ],
            'ar' => [
                'morning' => 'صباحي (8:00 ص - 12:00 م)',
                'afternoon' => 'ظهري (1:00 م - 5:00 م)',
                'evening' => 'مسائي (6:00 م - 11:00 م)',
                'full_day' => 'يوم كامل',
            ],
        ];

        return $labels[$this->emailLocale][$slot] ?? ucfirst(str_replace('_', ' ', $slot));
    }

    /**
     * Get URL for viewing booking.
     *
     * @return string
     */
    protected function getViewBookingUrl(): string
    {
        return config('app.url') . '/bookings/' . $this->booking->id;
    }

    /**
     * Get localized strings for email template.
     *
     * @return array
     */
    protected function getLocalizedStrings(): array
    {
        if ($this->emailLocale === 'ar') {
            return [
                'greeting' => 'مرحباً',
                'booking_details' => 'تفاصيل الحجز',
                'booking_number' => 'رقم الحجز',
                'hall' => 'القاعة',
                'date' => 'التاريخ',
                'time' => 'الوقت',
                'guests' => 'عدد الضيوف',
                'total' => 'الإجمالي',
                'view_booking' => 'عرض الحجز',
                'contact_us' => 'تواصل معنا',
                'thank_you' => 'شكراً لاختيارك مجالس',
                'support_text' => 'إذا كان لديك أي استفسار، لا تتردد في التواصل معنا',
                'approved_message' => 'يسعدنا إبلاغك بأن حجزك قد تمت الموافقة عليه!',
                'rejected_message' => 'نأسف لإبلاغك بأن حجزك قد تم رفضه.',
                'rejection_reason' => 'سبب الرفض',
                'what_next' => 'الخطوات التالية',
                'approved_next_steps' => 'يمكنك الآن المضي قدماً في تجهيزات فعاليتك. سيتم تأكيد حجزك بشكل نهائي بعد استلام الدفعة.',
                'rejected_next_steps' => 'يمكنك تصفح قاعات أخرى متاحة أو التواصل معنا للمساعدة في إيجاد بديل مناسب.',
            ];
        }

        return [
            'greeting' => 'Hello',
            'booking_details' => 'Booking Details',
            'booking_number' => 'Booking Number',
            'hall' => 'Hall',
            'date' => 'Date',
            'time' => 'Time',
            'guests' => 'Number of Guests',
            'total' => 'Total',
            'view_booking' => 'View Booking',
            'contact_us' => 'Contact Us',
            'thank_you' => 'Thank you for choosing Majalis',
            'support_text' => 'If you have any questions, please don\'t hesitate to contact us',
            'approved_message' => 'We\'re pleased to inform you that your booking has been approved!',
            'rejected_message' => 'We\'re sorry to inform you that your booking has been declined.',
            'rejection_reason' => 'Reason',
            'what_next' => 'What\'s Next?',
            'approved_next_steps' => 'You can now proceed with your event preparations. Your booking will be fully confirmed once payment is received.',
            'rejected_next_steps' => 'You can browse other available halls or contact us for assistance in finding a suitable alternative.',
        ];
    }

    /**
     * Get default subject if not set.
     *
     * @return string
     */
    protected function getDefaultSubject(): string
    {
        $event = NotificationEvent::tryFrom($this->notification->event);
        return $event?->subject($this->emailLocale) ?? 'Booking Update - Majalis';
    }
}
