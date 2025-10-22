<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $publishableKey;

    public function __construct()
    {
        $this->baseUrl = config('thawani.base_url');
        $this->apiKey = config('thawani.secret_key');
        $this->publishableKey = config('thawani.publishable_key');
    }

    /**
     * Create payment session for booking
     */
    public function createPaymentSession(Booking $booking): array
    {
        try {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'currency' => 'OMR',
                'status' => PaymentStatus::PENDING,
                'customer_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Prepare Thawani checkout session
            $response = Http::withHeaders([
                'thawani-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/checkout/session', [
                'client_reference_id' => $payment->payment_reference,
                'mode' => 'payment',
                'products' => [
                    [
                        'name' => 'Hall Booking - ' . $booking->hall->name,
                        'quantity' => 1,
                        'unit_amount' => $booking->total_amount * 1000, // Convert to Baisa
                    ]
                ],
                'success_url' => route('booking.payment.success', ['booking' => $booking->id]),
                'cancel_url' => route('booking.payment.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];

                $payment->update([
                    'invoice_id' => $data['invoice']['id'] ?? null,
                    'payment_url' => $data['session_id'] ? $this->baseUrl . '/pay/' . $data['session_id'] : null,
                    'gateway_response' => $response->json(),
                ]);

                Log::info('Payment session created', [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'session_id' => $data['session_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'session_id' => $data['session_id'],
                    'payment_url' => $payment->payment_url,
                ];
            }

            throw new Exception('Failed to create payment session: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Payment session creation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Verify payment status from Thawani
     */
    public function verifyPayment(string $sessionId): array
    {
        try {
            $response = Http::withHeaders([
                'thawani-api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/checkout/session/' . $sessionId);

            if ($response->successful()) {
                $data = $response->json()['data'];

                Log::info('Payment verification', [
                    'session_id' => $sessionId,
                    'status' => $data['payment_status'] ?? 'unknown'
                ]);

                return $data;
            }

            throw new Exception('Failed to verify payment: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Payment verification failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle payment webhook from Thawani
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            $eventType = $payload['event_type'] ?? null;
            $sessionData = $payload['data'] ?? [];

            Log::info('Payment webhook received', [
                'event_type' => $eventType,
                'session_id' => $sessionData['session_id'] ?? null
            ]);

            if ($eventType === 'payment_intent.success') {
                $this->handleSuccessfulPayment($sessionData);
                return true;
            }

            if ($eventType === 'payment_intent.failed') {
                $this->handleFailedPayment($sessionData);
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error('Webhook handling failed', ['error' => $e->getMessage(), 'payload' => $payload]);
            throw $e;
        }
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessfulPayment(array $data): void
    {
        $clientReferenceId = $data['client_reference_id'] ?? null;

        if (!$clientReferenceId) {
            throw new Exception('Missing client_reference_id in payment data');
        }

        $payment = Payment::where('payment_reference', $clientReferenceId)->firstOrFail();

        $payment->markAsPaid(
            $data['invoice']['id'] ?? null,
            $data
        );

        // Update booking status
        $booking = $payment->booking;
        if ($booking->status->value === 'pending') {
            $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        }

        Log::info('Payment processed successfully', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id
        ]);
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment(array $data): void
    {
        $clientReferenceId = $data['client_reference_id'] ?? null;

        if (!$clientReferenceId) {
            return;
        }

        $payment = Payment::where('payment_reference', $clientReferenceId)->first();

        if ($payment) {
            $payment->markAsFailed(
                $data['error']['message'] ?? 'Payment failed',
                $data
            );

            Log::warning('Payment failed', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id
            ]);
        }
    }

    /**
     * Process refund
     */
    public function refundPayment(Payment $payment, float $amount, string $reason = null): bool
    {
        try {
            if (!$payment->canBeRefunded()) {
                throw new Exception('Payment cannot be refunded');
            }

            // Call Thawani refund API
            $response = Http::withHeaders([
                'thawani-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/refunds', [
                'payment_intent_id' => $payment->transaction_id,
                'amount' => $amount * 1000, // Convert to Baisa
                'reason' => $reason ?? 'Customer requested refund',
                'metadata' => [
                    'booking_id' => $payment->booking_id,
                    'payment_reference' => $payment->payment_reference,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];

                $payment->refund($amount, $reason);

                Log::info('Refund processed', [
                    'payment_id' => $payment->id,
                    'amount' => $amount,
                    'refund_id' => $data['id'] ?? null
                ]);

                return true;
            }

            throw new Exception('Refund failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Payment $payment): string
    {
        if ($payment->invoice_id) {
            try {
                $data = $this->verifyPayment($payment->invoice_id);
                return $data['payment_status'] ?? 'unknown';
            } catch (Exception $e) {
                Log::error('Failed to get payment status', ['payment_id' => $payment->id]);
            }
        }

        return $payment->status->value;
    }

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Payment $payment): bool
    {
        if ($payment->isPending()) {
            $payment->update(['status' => PaymentStatus::FAILED]);

            Log::info('Payment cancelled', ['payment_id' => $payment->id]);

            return true;
        }

        return false;
    }
}
