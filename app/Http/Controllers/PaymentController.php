<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Payment Controller - Handles Thawani gateway callbacks
 *
 * Used for admin-created booking payment links (no customer auth required).
 *
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle successful payment redirect from Thawani.
     *
     * Robust flow (modelled after BookingController::paymentSuccess):
     * 1. Log all incoming params for debugging
     * 2. Find latest payment for this booking (not strict session_id match)
     * 3. Short-circuit if already paid
     * 4. Use session_id from URL, or fall back to stored transaction_id
     * 5. Verify with Thawani API — but NEVER redirect to cancel on failure;
     *    show the success page with a "pending" warning instead, because the
     *    customer's money may have already left their account.
     */
    public function success(Booking $booking, Request $request)
    {
        $sessionIdFromCallback = $request->query('session_id');

        Log::info('Payment callback success received', [
            'booking_id'          => $booking->id,
            'booking_number'      => $booking->booking_number,
            'session_id_callback' => $sessionIdFromCallback,
            'all_query_params'    => $request->query(),
            'full_url'            => $request->fullUrl(),
        ]);

        try {
            // 1. Get the most recent payment for this booking
            $payment = Payment::where('booking_id', $booking->id)
                ->latest()
                ->first();

            if (!$payment) {
                Log::error('No payment record found for booking', ['booking_id' => $booking->id]);
                return view('booking.payment-success', [
                    'booking' => $booking->fresh(['hall']),
                    'status'  => 'pending',
                ]);
            }

            Log::info('Payment record found', [
                'payment_id'             => $payment->id,
                'payment_status'         => $payment->status,
                'stored_transaction_id'  => $payment->transaction_id,
            ]);

            // 2. Already paid — just show success
            if ($payment->status === Payment::STATUS_PAID) {
                Log::info('Payment already marked paid, showing success', ['payment_id' => $payment->id]);
                return view('booking.payment-success', ['booking' => $booking->fresh(['hall'])]);
            }

            // 3. Use session_id from URL or fall back to stored transaction_id
            $sessionId = $sessionIdFromCallback ?? $payment->transaction_id;

            Log::info('Using session ID for verification', [
                'session_id' => $sessionId,
                'source'     => $sessionIdFromCallback ? 'thawani_callback' : 'stored_transaction_id',
            ]);

            if (!$sessionId) {
                Log::warning('No session ID available — cannot verify', ['booking_id' => $booking->id]);
                return view('booking.payment-success', [
                    'booking' => $booking->fresh(['hall']),
                    'status'  => 'pending',
                ]);
            }

            // 4. Verify with Thawani and update records
            try {
                $result = $this->paymentService->handlePaymentSuccess($sessionId, $booking);

                Log::info('Thawani verification result', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'result'     => $result,
                ]);

                // Success or pending — always show the success page (never cancel)
                return view('booking.payment-success', [
                    'booking' => $booking->fresh(['hall']),
                    'status'  => ($result['success'] ?? false) ? 'paid' : 'pending',
                ]);
            } catch (\Exception $e) {
                Log::error('Thawani verification threw exception', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'error'      => $e->getMessage(),
                ]);

                // Non-fatal — show success page with pending status
                return view('booking.payment-success', [
                    'booking' => $booking->fresh(['hall']),
                    'status'  => 'pending',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment success handler failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);

            return view('booking.payment-success', [
                'booking' => $booking->fresh(['hall']),
                'status'  => 'pending',
            ]);
        }
    }

    /**
     * Handle cancelled payment redirect from Thawani.
     */
    public function cancel(Booking $booking)
    {
        Log::info('Payment callback cancel received', [
            'booking_id'     => $booking->id,
            'booking_number' => $booking->booking_number,
        ]);

        return view('booking.payment-cancelled', compact('booking'));
    }

    /**
     * Handle Thawani webhook notifications.
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Thawani webhook received', ['payload' => $payload]);

            $sessionId       = $payload['session_id'] ?? null;
            $clientReference = $payload['client_reference_id'] ?? null;

            if (!$sessionId) {
                Log::warning('Webhook missing session_id', ['payload' => $payload]);
                return response()->json(['error' => 'Missing session_id'], 400);
            }

            $payment = Payment::where('transaction_id', $sessionId)->first();

            if (!$payment) {
                Log::warning('Webhook: Payment not found', [
                    'session_id'       => $sessionId,
                    'client_reference' => $clientReference,
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $booking = $payment->booking;

            if (!$booking) {
                Log::error('Webhook: Booking not found for payment', ['payment_id' => $payment->id]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            $result = $this->paymentService->handlePaymentSuccess($sessionId, $booking);

            Log::info('Webhook processed', [
                'session_id' => $sessionId,
                'booking_id' => $booking->id,
                'result'     => $result,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
