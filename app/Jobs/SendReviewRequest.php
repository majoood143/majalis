<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Booking;
use App\Mail\ReviewRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * SendReviewRequest Job
 *
 * Sends a review request email to the customer after a completed booking.
 * Includes a unique link for the customer to submit their review.
 *
 * Features:
 * - Queued for async processing
 * - Automatic retry on failure
 * - Generates secure review token
 * - Logs email dispatch for tracking
 *
 * @package App\Jobs
 */
class SendReviewRequest implements ShouldQueue
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
    public array $backoff = [60, 120, 300];

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param Booking $booking The completed booking
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Ensure booking is loaded with required relationships
        $this->booking->load(['hall', 'user']);

        // Skip if no customer email
        if (empty($this->booking->customer_email)) {
            Log::warning('SendReviewRequest: No customer email for booking', [
                'booking_id' => $this->booking->id,
                'booking_number' => $this->booking->booking_number,
            ]);
            return;
        }

        // Skip if review already exists
        if ($this->booking->review()->exists()) {
            Log::info('SendReviewRequest: Review already exists for booking', [
                'booking_id' => $this->booking->id,
                'booking_number' => $this->booking->booking_number,
            ]);
            return;
        }

        // Skip if booking is not completed
        if ($this->booking->status !== 'completed') {
            Log::warning('SendReviewRequest: Booking is not completed', [
                'booking_id' => $this->booking->id,
                'booking_number' => $this->booking->booking_number,
                'status' => $this->booking->status,
            ]);
            return;
        }

        // Generate review token (for secure review submission)
        $reviewToken = $this->generateReviewToken();

        // Send the review request email
        Mail::to($this->booking->customer_email)
            ->send(new ReviewRequestMail($this->booking, $reviewToken));

        // Log successful dispatch
        Log::info('SendReviewRequest: Email sent successfully', [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'customer_email' => $this->booking->customer_email,
        ]);
    }

    /**
     * Generate a secure review token for the booking.
     *
     * This token is used to verify the review submission
     * and prevent unauthorized reviews.
     *
     * @return string
     */
    protected function generateReviewToken(): string
    {
        // Create a hash based on booking details
        $tokenData = implode('|', [
            $this->booking->id,
            $this->booking->booking_number,
            $this->booking->customer_email,
            config('app.key'),
        ]);

        return hash('sha256', $tokenData);
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendReviewRequest: Job failed', [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
