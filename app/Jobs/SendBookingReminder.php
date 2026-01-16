<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Mail\BookingReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendBookingReminder Job
 *
 * Sends a booking reminder to the customer via email and/or SMS.
 * Typically triggered a few days before the booking date.
 *
 * Features:
 * - Multi-channel support (email, SMS)
 * - Custom message support
 * - Queued for async processing
 * - Automatic retry on failure
 *
 * @package App\Jobs
 */
class SendBookingReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [30, 60, 120];

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param Booking $booking The booking to remind about
     * @param array<string> $channels Notification channels ('email', 'sms')
     * @param string|null $customMessage Optional custom message
     */
    public function __construct(
        public Booking $booking,
        public array $channels = ['email'],
        public ?string $customMessage = null
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Load required relationships
        $this->booking->load(['hall.city', 'user']);

        // Send email reminder
        if (in_array('email', $this->channels) && !empty($this->booking->customer_email)) {
            $this->sendEmailReminder();
        }

        // Send SMS reminder
        if (in_array('sms', $this->channels) && !empty($this->booking->customer_phone)) {
            $this->sendSmsReminder();
        }

        Log::info('SendBookingReminder: Reminders sent', [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'channels' => $this->channels,
        ]);
    }

    /**
     * Send email reminder to the customer.
     *
     * @return void
     */
    protected function sendEmailReminder(): void
    {
        try {
            Mail::to($this->booking->customer_email)
                ->send(new BookingReminderMail($this->booking, $this->customMessage));

            Log::info('SendBookingReminder: Email sent', [
                'booking_id' => $this->booking->id,
                'email' => $this->booking->customer_email,
            ]);
        } catch (\Exception $e) {
            Log::error('SendBookingReminder: Email failed', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send SMS reminder to the customer.
     *
     * Uses the configured SMS gateway (Twilio, Nexmo, etc.)
     * Falls back to logging if no SMS service is configured.
     *
     * @return void
     */
    protected function sendSmsReminder(): void
    {
        // Get hall name
        $hallName = is_array($this->booking->hall->name)
            ? ($this->booking->hall->name['en'] ?? 'Hall')
            : $this->booking->hall->name;

        // Build SMS message
        $message = $this->customMessage ?? __('booking.sms.reminder_default', [
            'name' => $this->booking->customer_name,
            'hall' => $hallName,
            'date' => $this->booking->booking_date->format('d M Y'),
            'time' => __('booking.time_slots.' . $this->booking->time_slot),
            'booking_number' => $this->booking->booking_number,
        ]);

        // Check if SMS service is configured
        if (config('services.sms.enabled', false)) {
            try {
                // Use your SMS service here
                // Example with a hypothetical SMS facade:
                // Sms::to($this->booking->customer_phone)->send($message);

                // For now, we'll use a notification or log it
                $this->sendViaSmsGateway($this->booking->customer_phone, $message);

                Log::info('SendBookingReminder: SMS sent', [
                    'booking_id' => $this->booking->id,
                    'phone' => $this->booking->customer_phone,
                ]);
            } catch (\Exception $e) {
                Log::error('SendBookingReminder: SMS failed', [
                    'booking_id' => $this->booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Log the SMS content for debugging/manual sending
            Log::info('SendBookingReminder: SMS service not configured', [
                'booking_id' => $this->booking->id,
                'phone' => $this->booking->customer_phone,
                'message' => $message,
            ]);
        }
    }

    /**
     * Send SMS via the configured gateway.
     *
     * Implement this method based on your SMS provider:
     * - Twilio
     * - Nexmo/Vonage
     * - Local Omani SMS gateway
     *
     * @param string $phone
     * @param string $message
     * @return void
     */
    protected function sendViaSmsGateway(string $phone, string $message): void
    {
        // Example Twilio implementation:
        // $twilio = new \Twilio\Rest\Client(
        //     config('services.twilio.sid'),
        //     config('services.twilio.token')
        // );
        // $twilio->messages->create($phone, [
        //     'from' => config('services.twilio.from'),
        //     'body' => $message,
        // ]);

        // For now, just log
        Log::channel('sms')->info('SMS Reminder', [
            'to' => $phone,
            'message' => $message,
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendBookingReminder: Job failed', [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'channels' => $this->channels,
            'error' => $exception->getMessage(),
        ]);
    }
}
