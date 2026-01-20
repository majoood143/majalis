<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * BookingReminderService
 *
 * Handles all booking reminder email operations.
 * Supports bilingual content, custom messages, and comprehensive error handling.
 * Integrates with Laravel's mail system and logging for production reliability.
 *
 * Features:
 * - Send individual booking reminders
 * - Batch send reminders for upcoming bookings
 * - Automatic retry with fallback
 * - Comprehensive logging for debugging
 * - Bilingual support (English/Arabic)
 *
 * IMPORTANT: This service uses Booking model fields directly:
 * - $booking->customer_email (NOT $booking->customer->email)
 * - $booking->customer_name (NOT $booking->customer->name)
 * - $booking->user (optional relationship for registered users)
 *
 * @package App\Services
 * @version 1.1.0
 */
class BookingReminderService
{
    /**
     * Maximum number of retry attempts for failed emails
     */
    private const MAX_RETRIES = 3;

    /**
     * Delay in seconds between retry attempts
     */
    private const RETRY_DELAY_SECONDS = 5;

    /**
     * Send a booking reminder email to a customer
     *
     * Sends the booking reminder mailable with optional custom message.
     * Includes automatic retry logic and comprehensive error logging.
     *
     * @param Booking $booking The booking instance to send reminder for
     * @param string|null $customMessage Optional custom message from admin/system
     * @param string $locale The locale for email content ('en' or 'ar')
     * @param bool $retryOnFailure Whether to retry on delivery failure
     *
     * @return bool True if email was sent successfully, false otherwise
     *
     * @throws Exception Only if internal error occurs (booking not found, etc)
     *
     * @example
     * ```php
     * $service = app(BookingReminderService::class);
     * $success = $service->sendReminder($booking, null, 'en');
     *
     * if ($success) {
     *     Log::info('Reminder sent successfully');
     * } else {
     *     // Handle failure - user could be notified via dashboard
     * }
     * ```
     */
    public function sendReminder(
        Booking $booking,
        ?string $customMessage = null,
        string $locale = 'en',
        bool $retryOnFailure = true
    ): bool {
        try {
            // Validate booking state before sending
            $this->validateBooking($booking);

            // Get the customer email from booking (direct field, not relationship)
            $customerEmail = $this->getCustomerEmail($booking);

            // Set application locale for this email
            $originalLocale = app()->getLocale();
            app()->setLocale($locale);

            // Create the mailable instance
            $mailable = new BookingReminderMail($booking, $customMessage);

            // Send the email with automatic retry if needed
            if ($retryOnFailure) {
                $result = $this->sendWithRetry($mailable, $booking, $customerEmail, $locale);
            } else {
                // FIX: Use customer_email field directly from booking
                Mail::to($customerEmail)->send($mailable);
                $result = true;
            }

            // Restore original locale
            app()->setLocale($originalLocale);

            if ($result) {
                // Log successful delivery
                $this->logSuccess($booking, $customerEmail, $locale, $customMessage !== null);

                return true;
            }

            return false;
        } catch (Exception $e) {
            // Log the error with full context
            $this->logError($booking, $e);

            return false;
        }
    }

    /**
     * Send reminder emails for upcoming bookings
     *
     * Finds all confirmed bookings within the specified time window
     * and sends reminder emails to their customers.
     *
     * Useful for scheduled jobs that run periodically (e.g., daily reminder check).
     *
     * @param int $hoursAhead Number of hours ahead to check for bookings (default: 24)
     * @param string $locale Locale for email content ('en' or 'ar')
     *
     * @return int Number of reminder emails sent successfully
     *
     * @example
     * ```php
     * // In app/Console/Commands/SendBookingReminders.php
     * $service = app(BookingReminderService::class);
     * $count = $service->sendUpcomingBookingReminders(24, 'en');
     * $this->info("Sent $count booking reminders");
     * ```
     */
    public function sendUpcomingBookingReminders(int $hoursAhead = 24, string $locale = 'en'): int
    {
        try {
            // Validate hours parameter
            if ($hoursAhead <= 0) {
                Log::warning('Invalid hoursAhead value for booking reminders', [
                    'hours_ahead' => $hoursAhead,
                ]);

                return 0;
            }

            // Get bookings that are confirmed and start within the specified hours
            // FIX: Load 'user' relationship instead of 'customer' (customer doesn't exist)
            $bookings = Booking::whereStatus('confirmed')
                ->whereBetween('booking_date', [
                    now(),
                    now()->addHours($hoursAhead),
                ])
                ->whereNull('reminder_sent_at') // Don't send duplicate reminders
                ->whereNotNull('customer_email') // Must have email to send
                ->where('customer_email', '!=', '') // Email must not be empty
                ->with(['user', 'hall', 'hall.city']) // Load user (not customer)
                ->limit(100) // Process in chunks to avoid timeout
                ->get();

            $sentCount = 0;
            $failedCount = 0;

            foreach ($bookings as $booking) {
                // Use user's preferred language if available, otherwise default locale
                $emailLocale = $this->getPreferredLocale($booking, $locale);

                // Send the reminder
                if ($this->sendReminder($booking, null, $emailLocale)) {
                    // Mark reminder as sent
                    $booking->update([
                        'reminder_sent_at' => now(),
                    ]);

                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            // Log batch operation
            Log::info('Booking reminders batch completed', [
                'total_bookings_found' => $bookings->count(),
                'reminders_sent' => $sentCount,
                'reminders_failed' => $failedCount,
                'hours_ahead' => $hoursAhead,
                'locale' => $locale,
            ]);

            return $sentCount;
        } catch (Exception $e) {
            Log::error('Error in sendUpcomingBookingReminders', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'hours_ahead' => $hoursAhead,
            ]);

            return 0;
        }
    }

    /**
     * Send reminders for overdue bookings
     *
     * Sends urgent reminders for bookings that are scheduled for today or tomorrow.
     * Can be used to create more aggressive reminder strategies.
     *
     * @param string $locale Locale for email content
     * @param int|null $limit Maximum number of reminders to send
     *
     * @return int Number of reminders sent
     */
    public function sendUrgentReminders(string $locale = 'en', ?int $limit = null): int
    {
        try {
            $query = Booking::whereStatus('confirmed')
                ->whereBetween('booking_date', [
                    now(),
                    now()->addDay(),
                ])
                ->whereNull('reminder_sent_at')
                ->whereNotNull('customer_email')
                ->where('customer_email', '!=', '')
                ->with(['user', 'hall', 'hall.city']); // Load user (not customer)

            if ($limit) {
                $query->limit($limit);
            }

            $bookings = $query->get();
            $sentCount = 0;

            foreach ($bookings as $booking) {
                $emailLocale = $this->getPreferredLocale($booking, $locale);

                if ($this->sendReminder($booking, null, $emailLocale)) {
                    $booking->update(['reminder_sent_at' => now()]);
                    $sentCount++;
                }
            }

            Log::info('Urgent booking reminders sent', [
                'count' => $sentCount,
                'total_found' => $bookings->count(),
            ]);

            return $sentCount;
        } catch (Exception $e) {
            Log::error('Error sending urgent reminders', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Send reminders for bookings with pending payments
     *
     * Targets customers with unpaid or partially paid bookings.
     * Can include custom payment reminder message.
     *
     * @param string $locale Locale for email content
     *
     * @return int Number of reminders sent
     */
    public function sendPaymentReminders(string $locale = 'en'): int
    {
        try {
            $bookings = Booking::whereStatus('confirmed')
                ->where('balance_due', '>', 0)
                ->whereNull('payment_reminder_sent_at')
                ->whereNotNull('customer_email')
                ->where('customer_email', '!=', '')
                ->with(['user', 'hall', 'hall.city']) // Load user (not customer)
                ->get();

            $sentCount = 0;

            foreach ($bookings as $booking) {
                $emailLocale = $this->getPreferredLocale($booking, $locale);

                $customMessage = __('mail.payment_reminder', [
                    'amount' => $booking->balance_due,
                    'currency' => 'O.R.',
                ]);

                if ($this->sendReminder($booking, $customMessage, $emailLocale)) {
                    $booking->update(['payment_reminder_sent_at' => now()]);
                    $sentCount++;
                }
            }

            Log::info('Payment reminders sent', ['count' => $sentCount]);

            return $sentCount;
        } catch (Exception $e) {
            Log::error('Error sending payment reminders', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get the customer email from the booking
     *
     * The Booking model stores customer info directly as fields,
     * NOT as a relationship. This method safely retrieves the email.
     *
     * @param Booking $booking The booking instance
     *
     * @return string The customer's email address
     *
     * @throws Exception If no email is available
     */
    private function getCustomerEmail(Booking $booking): string
    {
        // Primary: Use customer_email field directly from booking
        if (!empty($booking->customer_email)) {
            return $booking->customer_email;
        }

        // Fallback: Check if user relationship exists and has email
        if ($booking->user && !empty($booking->user->email)) {
            return $booking->user->email;
        }

        throw new Exception(
            "Booking {$booking->id} has no customer email address"
        );
    }

    /**
     * Get preferred locale for email
     *
     * Checks if the booking has an associated user with language preference.
     * Falls back to the provided default locale.
     *
     * @param Booking $booking The booking instance
     * @param string $defaultLocale Default locale to use
     *
     * @return string The preferred locale ('en' or 'ar')
     */
    private function getPreferredLocale(Booking $booking, string $defaultLocale): string
    {
        // Check if booking has associated user with preferred language
        if ($booking->user && isset($booking->user->preferred_language)) {
            return $booking->user->preferred_language;
        }

        // Check if booking has associated user with locale setting
        if ($booking->user && isset($booking->user->locale)) {
            return $booking->user->locale;
        }

        return $defaultLocale;
    }

    /**
     * Validate booking before sending reminder
     *
     * Checks that the booking has all necessary data for email delivery.
     * Throws exception if validation fails.
     *
     * FIX: Uses customer_email field directly instead of customer relationship
     *
     * @param Booking $booking The booking to validate
     *
     * @return void
     *
     * @throws Exception If booking is invalid
     */
    private function validateBooking(Booking $booking): void
    {
        // Check booking exists and is loaded
        if (!$booking->exists) {
            throw new Exception('Booking does not exist');
        }

        // FIX: Check customer_email field directly (NOT customer relationship)
        if (empty($booking->customer_email)) {
            // Fallback: Check user relationship
            if (!$booking->user || empty($booking->user->email)) {
                throw new Exception(
                    "Booking {$booking->id} has no customer email address (customer_email field is empty and no user relationship)"
                );
            }
        }

        // Check hall relationship
        if (!$booking->hall) {
            throw new Exception("Booking {$booking->id} has no associated hall");
        }

        // Check booking date
        if (!$booking->booking_date) {
            throw new Exception("Booking {$booking->id} has no booking date");
        }
    }

    /**
     * Send mailable with automatic retry logic
     *
     * Attempts to send email multiple times with delay between attempts.
     * Returns false only after all retries are exhausted.
     *
     * @param BookingReminderMail $mailable The mailable to send
     * @param Booking $booking Booking instance for logging
     * @param string $customerEmail The recipient email address
     * @param string $locale Email locale
     *
     * @return bool True if successfully sent, false if all retries failed
     */
    private function sendWithRetry(
        BookingReminderMail $mailable,
        Booking $booking,
        string $customerEmail,
        string $locale
    ): bool {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                // FIX: Use customer_email directly passed as parameter
                Mail::to($customerEmail)->send($mailable);

                if ($attempt > 1) {
                    Log::info('Email sent successfully on retry', [
                        'booking_id' => $booking->id,
                        'attempt' => $attempt,
                    ]);
                }

                return true;
            } catch (Exception $e) {
                Log::warning('Email send attempt failed', [
                    'booking_id' => $booking->id,
                    'customer_email' => $customerEmail,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                // Don't retry on last attempt
                if ($attempt < self::MAX_RETRIES) {
                    sleep(self::RETRY_DELAY_SECONDS);
                }
            }
        }

        return false;
    }

    /**
     * Log successful email delivery
     *
     * @param Booking $booking The booking
     * @param string $customerEmail The recipient email
     * @param string $locale Email locale
     * @param bool $hasCustomMessage Whether custom message was included
     *
     * @return void
     */
    private function logSuccess(
        Booking $booking,
        string $customerEmail,
        string $locale,
        bool $hasCustomMessage
    ): void {
        Log::info('Booking reminder sent successfully', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'customer_name' => $booking->customer_name,
            'customer_email' => $customerEmail,
            'hall_id' => $booking->hall_id,
            'booking_date' => $booking->booking_date->toDateString(),
            'locale' => $locale,
            'custom_message_included' => $hasCustomMessage,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log email delivery error
     *
     * @param Booking $booking The booking
     * @param Exception $exception The exception that occurred
     *
     * @return void
     */
    private function logError(Booking $booking, Exception $exception): void
    {
        Log::error('Failed to send booking reminder', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number ?? 'N/A',
            'customer_name' => $booking->customer_name ?? 'N/A',
            'customer_email' => $booking->customer_email ?? 'N/A',
            'user_id' => $booking->user_id ?? 'N/A',
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
