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
 * ReviewRequestMail Mailable
 *
 * Email sent to customers after a completed booking
 * requesting them to leave a review for the hall.
 *
 * @package App\Mail
 */
class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking The completed booking
     * @param string $reviewToken Secure token for review submission
     */
    public function __construct(
        public Booking $booking,
        public string $reviewToken
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('booking.email.review_request_subject', [
                'hall' => $this->getHallName(),
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
        $daysElapsed   = max(0, (int) $this->booking->booking_date->startOfDay()->diffInDays(now()->startOfDay()));
        $daysRemaining = max(0, 7 - $daysElapsed);

        $timeSlot = match ($this->booking->time_slot) {
            'morning'   => __('booking.time_slots.morning'),
            'afternoon' => __('booking.time_slots.afternoon'),
            'evening'   => __('booking.time_slots.evening'),
            'full_day'  => __('booking.time_slots.full_day'),
            default     => ucfirst(str_replace('_', ' ', $this->booking->time_slot)),
        };

        return new Content(
            markdown: 'emails.booking.review-request',
            with: [
                'booking'       => $this->booking,
                'hallName'      => $this->getHallName(),
                'reviewUrl'     => $this->getReviewUrl(),
                'customerName'  => $this->booking->customer_name,
                'bookingDate'   => $this->booking->booking_date->format('l, d M Y'),
                'timeSlot'      => $timeSlot,
                'daysRemaining' => $daysRemaining,
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
     * Generate the review submission URL with token.
     *
     * @return string
     */
    protected function getReviewUrl(): string
    {
        return url('/reviews/submit?' . http_build_query([
            'booking' => $this->booking->id,
            'token' => $this->reviewToken,
        ]));
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
