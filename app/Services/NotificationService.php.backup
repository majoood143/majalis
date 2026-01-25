<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class NotificationService
{
    /**
     * Send booking created notification
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
     * Send booking reminder (24 hours before)
     */
    public function sendBookingReminderNotification(Booking $booking): void
    {
        try {
            $daysUntil = $booking->getDaysUntilBooking();

            if ($daysUntil === 1) {
                // Send email
                $this->sendEmail(
                    $booking->customer_email,
                    'Reminder: Your booking is tomorrow!',
                    'emails.booking.reminder',
                    ['booking' => $booking]
                );

                // Send SMS
                $this->sendSMS(
                    $booking->customer_phone,
                    "Reminder: Your booking {$booking->booking_number} at {$booking->hall->name} is tomorrow at {$booking->time_slot}. We look forward to seeing you!"
                );

                Log::info('Booking reminder sent', ['booking_id' => $booking->id]);
            }
        } catch (Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send payment successful notification
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
     * Send email
     */
    protected function sendEmail(string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
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
     * Send SMS (using Oman SMS provider)
     */
    protected function sendSMS(string $phone, string $message): void
    {
        try {
            $smsProvider = config('services.sms.provider');
            $apiKey = config('services.sms.api_key');
            $apiUrl = config('services.sms.api_url');
            $sender = config('services.sms.sender_name', 'Majalis');

            if (!$apiKey || !$apiUrl) {
                Log::warning('SMS configuration missing, skipping SMS', ['phone' => $phone]);
                return;
            }

            // Format phone number (Oman format)
            $formattedPhone = $this->formatOmanPhone($phone);

            // Send via configured SMS provider
            $response = Http::post($apiUrl, [
                'api_key' => $apiKey,
                'sender' => $sender,
                'recipient' => $formattedPhone,
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
            Log::error('SMS sending exception', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format phone number for Oman
     */
    protected function formatOmanPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with +968, remove it
        if (str_starts_with($phone, '968')) {
            $phone = substr($phone, 3);
        }

        // Ensure it starts with country code
        return '968' . $phone;
    }

    /**
     * Send WhatsApp message (if configured)
     */
    public function sendWhatsAppMessage(string $phone, string $message): void
    {
        try {
            $whatsappApiKey = config('services.whatsapp.api_key');
            $whatsappApiUrl = config('services.whatsapp.api_url');

            if (!$whatsappApiKey || !$whatsappApiUrl) {
                Log::info('WhatsApp not configured, skipping');
                return;
            }

            $formattedPhone = $this->formatOmanPhone($phone);

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
     * Send notification to admin
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

    /**
     * Send review received notification to owner
     */
    public function sendReviewReceivedNotification($review): void
    {
        try {
            $owner = $review->hall->owner;

            $this->sendEmail(
                $owner->email,
                'New Review Received',
                'emails.review.received',
                ['review' => $review]
            );
        } catch (Exception $e) {
            Log::error('Review notification failed', ['error' => $e->getMessage()]);
        }
    }
}
