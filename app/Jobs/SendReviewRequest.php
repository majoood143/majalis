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
 * Dispatched with a 2-hour delay after the booking event date.
 * Sends a personalised review-request email containing a secure
 * tokenised link valid for 14 days post-event.
 *
 * Review windows (relative to booking_date):
 *   Primary  : days  0–7   → standard review
 *   Grace    : days  8–14  → is_late_review flagged in analytics
 *   Expired  : day  >14   → link rejected at controller level
 */
class SendReviewRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 120, 300];

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public Booking $booking
    ) {}

    public function handle(): void
    {
        $this->booking->load(['hall', 'user']);

        // Guard: must have an email to send to
        if (empty($this->booking->customer_email)) {
            Log::warning('SendReviewRequest: No customer email', [
                'booking_id' => $this->booking->id,
            ]);
            return;
        }

        // Guard: booking must still be completed
        if ($this->booking->status !== 'completed') {
            Log::warning('SendReviewRequest: Booking not completed', [
                'booking_id' => $this->booking->id,
                'status'     => $this->booking->status,
            ]);
            return;
        }

        // Guard: review already submitted — no need to send
        if ($this->booking->review()->exists()) {
            Log::info('SendReviewRequest: Review already exists', [
                'booking_id' => $this->booking->id,
            ]);
            return;
        }

        // Guard: outside the 14-day review window
        if (!$this->booking->canReceiveReview()) {
            Log::info('SendReviewRequest: Outside review window', [
                'booking_id'       => $this->booking->id,
                'days_since_event' => $this->booking->daysSinceEvent(),
            ]);
            return;
        }

        $reviewToken = $this->generateReviewToken();

        Mail::to($this->booking->customer_email)
            ->send(new ReviewRequestMail($this->booking, $reviewToken));

        Log::info('SendReviewRequest: Email sent', [
            'booking_id'     => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'customer_email' => $this->booking->customer_email,
            'days_since_event' => $this->booking->daysSinceEvent(),
        ]);
    }

    /**
     * Deterministic SHA-256 token — reproducible from booking data + app key.
     * No DB storage needed; verified at submission by regenerating and comparing.
     */
    protected function generateReviewToken(): string
    {
        return hash('sha256', implode('|', [
            $this->booking->id,
            $this->booking->booking_number,
            $this->booking->customer_email,
            config('app.key'),
        ]));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendReviewRequest: Job failed', [
            'booking_id' => $this->booking->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
