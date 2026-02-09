<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Payment Controller - Handles Thawani gateway callbacks
 *
 * Processes payment success redirects, cancellations, and webhooks.
 *
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle successful payment redirect from Thawani
     *
     * ✅ FIX: Changed from verifyPayment() (non-existent method)
     *    to handlePaymentSuccess() which actually exists in PaymentService
     *    and correctly updates BOTH payment record AND booking payment_status.
     *
     * Flow:
     * 1. Thawani redirects here with ?session_id=xxx after successful payment
     * 2. handlePaymentSuccess() verifies with Thawani API
     * 3. Updates payment.status → 'paid' and booking.payment_status → 'paid'/'partial'
     *
     * @param Booking $booking The booking being paid for
     * @param Request $request Contains session_id from Thawani redirect
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success(Booking $booking, Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            /**
             * ✅ FIX: Was calling $this->paymentService->verifyPayment($sessionId)
             *    which does NOT exist in PaymentService.
             *
             *    Now calls handlePaymentSuccess($sessionId, $booking) which:
             *    - Finds the Payment record by booking_id + transaction_id (session_id)
             *    - Calls Thawani API to verify session status
             *    - If paid: updates Payment status to 'paid', sets paid_at
             *    - If full payment: sets booking.payment_status = 'paid'
             *    - If advance payment: sets booking.payment_status = 'partial'
             *    - Sets booking.status = 'confirmed' and confirmed_at
             */
            $data = $this->paymentService->handlePaymentSuccess($sessionId, $booking);

            // handlePaymentSuccess returns ['success' => true, 'payment_status' => 'paid', ...]
            if (isset($data['success']) && $data['success'] === true) {
                return view('booking.payment-success', compact('booking'));
            }

            // Log failed verification for debugging
            Log::warning('Payment verification failed on success callback', [
                'booking_id' => $booking->id,
                'session_id' => $sessionId,
                'response' => $data,
            ]);
        }

        return redirect()->route('booking.show', $booking)
            ->with('error', __('Payment verification failed'));
    }

    /**
     * Handle cancelled payment redirect from Thawani
     *
     * @param Booking $booking The booking whose payment was cancelled
     * @return \Illuminate\View\View
     */
    public function cancel(Booking $booking)
    {
        return view('booking.payment-cancelled', compact('booking'));
    }

    /**
     * Handle Thawani webhook notifications
     *
     * ✅ FIX: Was calling $this->paymentService->handleWebhook() which
     *    does NOT exist in PaymentService.
     *
     *    Now uses getSessionStatus() + handlePaymentSuccess() to verify
     *    and process the payment from webhook data.
     *
     * @param Request $request Webhook payload from Thawani
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Thawani webhook received', [
                'payload' => $payload,
            ]);

            // Extract session_id from webhook payload
            // Thawani sends: { "session_id": "xxx", "client_reference_id": "PAY-xxx", ... }
            $sessionId = $payload['session_id'] ?? null;
            $clientReference = $payload['client_reference_id'] ?? null;

            if (!$sessionId) {
                Log::warning('Webhook missing session_id', ['payload' => $payload]);
                return response()->json(['error' => 'Missing session_id'], 400);
            }

            // Find the payment by transaction_id (which stores the Thawani session_id)
            $payment = \App\Models\Payment::where('transaction_id', $sessionId)->first();

            if (!$payment) {
                Log::warning('Webhook: Payment not found for session', [
                    'session_id' => $sessionId,
                    'client_reference' => $clientReference,
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Load the booking relationship
            $booking = $payment->booking;

            if (!$booking) {
                Log::error('Webhook: Booking not found for payment', [
                    'payment_id' => $payment->id,
                    'session_id' => $sessionId,
                ]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // Use the existing handlePaymentSuccess method to verify and update
            $result = $this->paymentService->handlePaymentSuccess($sessionId, $booking);

            Log::info('Webhook processed', [
                'session_id' => $sessionId,
                'booking_id' => $booking->id,
                'result' => $result,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
