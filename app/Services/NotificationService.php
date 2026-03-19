<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * NotificationService
 *
 * Handles sending notifications to customers, owners, and admins.
 * Supports email, SMS, and WhatsApp channels.
 *
 * @package App\Services
 */
class NotificationService
{
    /**
     * Send booking created notification
     *
     * Notifies both customer and hall owner when a new booking is created.
     *
     * @param Booking $booking The newly created booking
     * @return void
     */
    public function sendBookingCreatedNotification(Booking $booking): void
    {
        try {
            // Send email to customer
            $this->sendEmail(
                $booking->customer_email,
                'Booking Confirmation - ' . $booking->booking_number,
                'emails.booking.created',
                ['booking' => $booking]
            );

            // Send email to hall owner
            $this->sendEmail(
                $booking->hall->owner->email,
                'New Booking Received - ' . $booking->booking_number,
                'emails.booking.new-owner',
                ['booking' => $booking]
            );

            // Send SMS to customer
            $this->sendSMS(
                $booking->customer_phone,
                "Your booking {$booking->booking_number} has been created. Hall: {$booking->hall->name}, Date: {$booking->booking_date->format('d M Y')}"
            );

            Log::info('Booking created notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking created notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking confirmed notification
     *
     * Notifies customer when their booking is confirmed.
     *
     * @param Booking $booking The confirmed booking
     * @return void
     */
    public function sendBookingConfirmedNotification(Booking $booking): void
    {
        try {
            // Send email to customer
            $this->sendEmail(
                $booking->customer_email,
                'Booking Confirmed - ' . $booking->booking_number,
                'emails.booking.confirmed',
                ['booking' => $booking]
            );

            // Send SMS
            $this->sendSMS(
                $booking->customer_phone,
                "Your booking {$booking->booking_number} is confirmed! Hall: {$booking->hall->name}, Date: {$booking->booking_date->format('d M Y')}, Time: {$booking->time_slot}"
            );

            Log::info('Booking confirmed notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking confirmed notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking cancelled notification
     *
     * Notifies customer and hall owner when a booking is cancelled.
     *
     * @param Booking $booking The cancelled booking
     * @return void
     */
    public function sendBookingCancelledNotification(Booking $booking): void
    {
        try {
            // Send email to customer
            $this->sendEmail(
                $booking->customer_email,
                'Booking Cancelled - ' . $booking->booking_number,
                'emails.booking.cancelled',
                ['booking' => $booking]
            );

            // Send email to hall owner
            $this->sendEmail(
                $booking->hall->owner->email,
                'Booking Cancelled - ' . $booking->booking_number,
                'emails.booking.cancelled-owner',
                ['booking' => $booking]
            );

            // Send SMS
            $this->sendSMS(
                $booking->customer_phone,
                "Your booking {$booking->booking_number} has been cancelled. " .
                    ($booking->refund_amount ? "Refund amount: {$booking->refund_amount} OMR" : "")
            );

            Log::info('Booking cancelled notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking cancelled notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking completed notification
     *
     * Notifies customer when their booking is completed with a review request.
     *
     * @param Booking $booking The completed booking
     * @return void
     */
    public function sendBookingCompletedNotification(Booking $booking): void
    {
        try {
            // Send email with review request
            $this->sendEmail(
                $booking->customer_email,
                'How was your experience? - ' . $booking->booking_number,
                'emails.booking.completed',
                ['booking' => $booking]
            );

            // Send SMS
            $this->sendSMS(
                $booking->customer_phone,
                "Thank you for booking with Majalis! Please share your experience by leaving a review."
            );

            Log::info('Booking completed notifications sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send booking completed notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send booking reminder notification
     *
     * Sends a reminder to the customer about their upcoming booking.
     * Can be triggered automatically (24 hours before) or manually by admin.
     *
     * @param Booking $booking The booking to remind about
     * @param bool $manual Whether this is a manual reminder (bypasses day check)
     * @param string|null $customMessage Optional custom message from admin
     * @return bool True if notification was sent, false otherwise
     * @throws Exception If the booking is not eligible for reminder
     */
    public function sendBookingReminderNotification(
        Booking $booking,
        bool $manual = false,
        ?string $customMessage = null
    ): bool {
        try {
            $daysUntil = $booking->getDaysUntilBooking();

            // Validate booking is eligible for reminder
            if (!$booking->isConfirmed()) {
                throw new Exception(__('booking.errors.reminder_not_confirmed'));
            }

            if ($daysUntil < 0) {
                throw new Exception(__('booking.errors.reminder_past_booking'));
            }

            // For automatic reminders, only send if booking is tomorrow
            // For manual reminders, allow sending regardless of days
            if (!$manual && $daysUntil !== 1) {
                Log::info('Skipping automatic reminder - booking not tomorrow', [
                    'booking_id' => $booking->id,
                    'days_until' => $daysUntil
                ]);
                return false;
            }

            // Validate customer email exists
            if (empty($booking->customer_email)) {
                throw new Exception(__('booking.errors.reminder_no_email'));
            }

            // Load required relationships
            $booking->loadMissing(['hall', 'hall.city']);

            // Send email using BookingReminderMail Mailable
            Mail::to($booking->customer_email)
                ->send(new BookingReminderMail($booking, $customMessage));

            // Send SMS reminder
            $hallName = $this->getHallName($booking);
            $smsMessage = $this->buildReminderSmsMessage($booking, $hallName, $daysUntil);
            $this->sendSMS($booking->customer_phone, $smsMessage);

            Log::info('Booking reminder sent', [
                'booking_id' => $booking->id,
                'manual' => $manual,
                'days_until' => $daysUntil,
                'email' => $booking->customer_email
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'manual' => $manual,
                'error' => $e->getMessage()
            ]);

            // Re-throw exception for manual reminders so UI can show error
            if ($manual) {
                throw $e;
            }

            return false;
        }
    }

    /**
     * Build SMS message for booking reminder
     *
     * @param Booking $booking The booking
     * @param string $hallName The hall name
     * @param int $daysUntil Days until booking
     * @return string The SMS message
     */
    protected function buildReminderSmsMessage(Booking $booking, string $hallName, int $daysUntil): string
    {
        $timeLabel = match ($booking->time_slot) {
            'morning' => __('booking.time_slots.morning'),
            'afternoon' => __('booking.time_slots.afternoon'),
            'evening' => __('booking.time_slots.evening'),
            'full_day' => __('booking.time_slots.full_day'),
            default => $booking->time_slot
        };

        if ($daysUntil === 0) {
            return __('booking.sms.reminder_today', [
                'booking_number' => $booking->booking_number,
                'hall' => $hallName,
                'time' => $timeLabel
            ]);
        } elseif ($daysUntil === 1) {
            return __('booking.sms.reminder_tomorrow', [
                'booking_number' => $booking->booking_number,
                'hall' => $hallName,
                'time' => $timeLabel
            ]);
        } else {
            return __('booking.sms.reminder_days', [
                'booking_number' => $booking->booking_number,
                'hall' => $hallName,
                'date' => $booking->booking_date->format('d M Y'),
                'time' => $timeLabel,
                'days' => $daysUntil
            ]);
        }
    }

    /**
     * Get the hall name in the current locale
     *
     * @param Booking $booking The booking
     * @return string The hall name
     */
    protected function getHallName(Booking $booking): string
    {
        $name = $booking->hall->name ?? 'Hall';

        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? 'Hall';
        }

        return (string) $name;
    }

    /**
     * Send payment link email to customer
     *
     * @param Booking $booking
     * @param string $paymentUrl Thawani checkout URL
     * @return void
     */
    public function sendPaymentLinkNotification(Booking $booking, string $paymentUrl): void
    {
        try {
            $this->sendEmail(
                $booking->customer_email,
                'Complete Your Payment - ' . $booking->booking_number,
                'emails.booking.payment-link',
                ['booking' => $booking, 'paymentUrl' => $paymentUrl]
            );

            Log::info('Payment link email sent', [
                'booking_id' => $booking->id,
                'email'      => $booking->customer_email,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send payment link email', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment successful notification
     *
     * @param Booking $booking The booking with successful payment
     * @return void
     */
    public function sendPaymentSuccessNotification(Booking $booking): void
    {
        try {
            $this->sendEmail(
                $booking->customer_email,
                'Payment Successful - ' . $booking->booking_number,
                'emails.payment.success',
                ['booking' => $booking]
            );

            $this->sendSMS(
                $booking->customer_phone,
                "Payment received! Your booking {$booking->booking_number} is confirmed. Amount: {$booking->total_amount} OMR"
            );

            Log::info('Payment success notification sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send payment success notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send payment failed notification
     *
     * @param Booking $booking The booking with failed payment
     * @return void
     */
    public function sendPaymentFailedNotification(Booking $booking): void
    {
        try {
            $this->sendEmail(
                $booking->customer_email,
                'Payment Failed - ' . $booking->booking_number,
                'emails.payment.failed',
                ['booking' => $booking]
            );

            Log::info('Payment failed notification sent', ['booking_id' => $booking->id]);
        } catch (Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email using a blade view
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $view Blade view path
     * @param array<string, mixed> $data Data to pass to view
     * @return void
     * @throws Exception If email sending fails
     */
    protected function sendEmail(string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address', 'noreply@majalis.om'),
                        config('mail.from.name', 'Majalis')
                    );
            });

            Log::info('Email sent', ['to' => $to, 'subject' => $subject]);
        } catch (Exception $e) {
            Log::error('Email sending failed', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send SMS message
     *
     * @param string|null $phone Recipient phone number
     * @param string $message SMS message content
     * @return void
     */
    protected function sendSMS(?string $phone, string $message): void
    {
        if (empty($phone)) {
            Log::warning('SMS skipped - no phone number provided');
            return;
        }

        try {
            // Format phone number (ensure it has country code)
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Get SMS API configuration
            $smsApiUrl = config('services.sms.api_url');
            $smsApiKey = config('services.sms.api_key');

            if (empty($smsApiUrl) || empty($smsApiKey)) {
                Log::warning('SMS not configured - skipping', ['phone' => $formattedPhone]);
                return;
            }

            // Send SMS via API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $smsApiKey,
                'Content-Type' => 'application/json',
            ])->post($smsApiUrl, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent', ['phone' => $formattedPhone]);
            } else {
                Log::error('SMS sending failed', [
                    'phone' => $formattedPhone,
                    'response' => $response->body()
                ]);
            }
        } catch (Exception $e) {
            Log::error('SMS sending failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp message
     *
     * @param string|null $phone Recipient phone number
     * @param string $message Message content
     * @return void
     */
    protected function sendWhatsApp(?string $phone, string $message): void
    {
        if (empty($phone)) {
            return;
        }

        try {
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Get WhatsApp API configuration
            $whatsappApiUrl = config('services.whatsapp.api_url');
            $whatsappApiKey = config('services.whatsapp.api_key');

            if (empty($whatsappApiUrl) || empty($whatsappApiKey)) {
                Log::warning('WhatsApp not configured - skipping', ['phone' => $formattedPhone]);
                return;
            }

            // Send via WhatsApp Business API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $whatsappApiKey,
            ])->post($whatsappApiUrl, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', ['phone' => $formattedPhone]);
            }
        } catch (Exception $e) {
            Log::error('WhatsApp message failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format phone number with country code
     *
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If phone doesn't start with + or country code, add Oman code
        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '968')) {
            $phone = '+968' . $phone;
        } elseif (str_starts_with($phone, '968')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Send notification to admin
     *
     * @param string $subject Email subject
     * @param string $message Message content
     * @param array<string, mixed> $data Additional data
     * @return void
     */
    public function sendAdminNotification(string $subject, string $message, array $data = []): void
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@majalis.om');

            $this->sendEmail(
                $adminEmail,
                $subject,
                'emails.admin.notification',
                array_merge(['message' => $message], $data)
            );
        } catch (Exception $e) {
            Log::error('Admin notification failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send new owner verification request notification
     *
     * @param User $owner The new owner
     * @return void
     */
    public function sendOwnerVerificationRequestNotification(User $owner): void
    {
        try {
            $this->sendAdminNotification(
                'New Hall Owner Verification Request',
                "A new hall owner has registered and requires verification.\nName: {$owner->name}\nEmail: {$owner->email}",
                ['owner' => $owner]
            );

            // Send email to owner
            $this->sendEmail(
                $owner->email,
                'Verification Request Received',
                'emails.owner.verification-request',
                ['owner' => $owner]
            );
        } catch (Exception $e) {
            Log::error('Owner verification notification failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send owner verified notification
     *
     * @param User $owner The verified owner
     * @return void
     */
    public function sendOwnerVerifiedNotification(User $owner): void
    {
        try {
            $this->sendEmail(
                $owner->email,
                'Your Account Has Been Verified!',
                'emails.owner.verified',
                ['owner' => $owner]
            );

            $this->sendSMS(
                $owner->phone,
                "Congratulations! Your Majalis hall owner account has been verified. You can now start managing your halls."
            );
        } catch (Exception $e) {
            Log::error('Owner verified notification failed', ['error' => $e->getMessage()]);
        }
    }
}
