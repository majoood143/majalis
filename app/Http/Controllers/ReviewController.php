<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ReviewController
 *
 * Handles the tokenised review submission flow.
 *
 * Routes (public — no auth required):
 *   GET  /reviews/submit  → show()   — landing page with booking details + form
 *   POST /reviews/submit  → store()  — validate & persist review
 *
 * Security model:
 *   - Token is SHA-256( booking_id | booking_number | customer_email | APP_KEY )
 *   - Deterministic → no DB storage needed; verified by re-computing on every request
 *   - Link expires after 14 days post-event (checked via Booking::canReceiveReview())
 */
class ReviewController extends Controller
{
    // -------------------------------------------------------------------------
    // SHOW — Review landing page
    // -------------------------------------------------------------------------

    public function show(Request $request)
    {
        [$booking, $error] = $this->resolveBooking($request);

        if ($error) {
            return view('reviews.expired', compact('error'));
        }

        return view('reviews.submit', [
            'booking'  => $booking,
            'token'    => $request->query('token'),
            'isLate'   => $booking->isInGracePeriodReviewWindow(),
        ]);
    }

    // -------------------------------------------------------------------------
    // STORE — Review submission
    // -------------------------------------------------------------------------

    public function store(Request $request)
    {
        [$booking, $error] = $this->resolveBooking($request);

        if ($error) {
            return view('reviews.expired', compact('error'));
        }

        // One-review-per-booking enforcement
        if ($booking->review()->exists()) {
            return redirect()
                ->route('reviews.submit', ['booking' => $booking->id, 'token' => $request->input('token')])
                ->with('info', __('review.messages.already_submitted'));
        }

        $rating = (int) $request->input('rating', 0);

        $rules = [
            'rating'             => 'required|integer|min:1|max:5',
            'comment'            => [
                'nullable',
                'string',
                'max:2000',
                // Require text context for low ratings so moderation has signal
                $rating > 0 && $rating <= 3 ? 'required' : 'nullable',
                $rating > 0 && $rating <= 3 ? 'min:10'   : '',
            ],
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'service_rating'     => 'nullable|integer|min:1|max:5',
            'value_rating'       => 'nullable|integer|min:1|max:5',
            'location_rating'    => 'nullable|integer|min:1|max:5',
            'photos'             => 'nullable|array|max:5',
            'photos.*'           => 'image|mimes:jpeg,png,webp|max:4096',
            'marketing_consent'  => 'nullable|boolean',
        ];

        // Clean up empty rules
        $rules['comment'] = array_filter($rules['comment']);

        $validated = $request->validate($rules);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews/' . $booking->id, 'public');
                $photoPaths[] = $path;
            }
        }

        $isLate = $booking->isInGracePeriodReviewWindow();

        Review::create([
            'hall_id'            => $booking->hall_id,
            'booking_id'         => $booking->id,
            'user_id'            => $booking->user_id ?? 0,
            'rating'             => $validated['rating'],
            'comment'            => $validated['comment'] ?? null,
            'photos'             => $photoPaths ?: null,
            'cleanliness_rating' => $validated['cleanliness_rating'] ?? null,
            'service_rating'     => $validated['service_rating'] ?? null,
            'value_rating'       => $validated['value_rating'] ?? null,
            'location_rating'    => $validated['location_rating'] ?? null,
            'is_approved'        => true,
            'is_late_review'     => $isLate,
            'marketing_consent'  => (bool) ($validated['marketing_consent'] ?? false),
        ]);

        return view('reviews.thankyou', [
            'booking' => $booking,
            'isLate'  => $isLate,
        ]);
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Resolve and validate the booking + token from the request.
     *
     * @return array{Booking|null, string|null}  [$booking, $errorKey]
     */
    private function resolveBooking(Request $request): array
    {
        $bookingId = $request->query('booking') ?? $request->input('booking');
        $token     = $request->query('token')   ?? $request->input('token');

        if (!$bookingId || !$token) {
            return [null, __('review.messages.invalid_link')];
        }

        $booking = Booking::with(['hall', 'user'])->find($bookingId);

        if (!$booking) {
            return [null, __('review.messages.booking_not_found')];
        }

        // Verify token
        $expected = hash('sha256', implode('|', [
            $booking->id,
            $booking->booking_number,
            $booking->customer_email,
            config('app.key'),
        ]));

        if (!hash_equals($expected, (string) $token)) {
            return [null, __('review.messages.invalid_token')];
        }

        // Booking must be completed
        if (!$booking->isCompleted()) {
            return [null, __('review.messages.booking_not_completed')];
        }

        // Check review window (14 days)
        if (!$booking->canReceiveReview()) {
            return [null, __('review.messages.window_expired')];
        }

        return [$booking, null];
    }
}
