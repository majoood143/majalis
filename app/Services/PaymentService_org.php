<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * Payment Service for Thawani Gateway Integration
 *
 * Uses native PHP cURL for maximum compatibility with Thawani UAT
 *
 * @package App\Services
 */
class PaymentService
{
    /**
     * Thawani API Configuration
     */
    protected string $apiKey;
    protected string $baseUrl;
    protected string $publishableKey;

    /**
     * Constructor - Initialize Thawani API configuration
     */
    public function __construct()
    {
        $this->apiKey = config('services.thawani.secret_key');
        $this->publishableKey = config('services.thawani.publishable_key');
        $this->baseUrl = config('services.thawani.base_url', 'https://uatcheckout.thawani.om/api/v1');
    }

    /**
     * Initiate payment session with Thawani
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return array
     */
    public function initiatePayment(Booking $booking, string $paymentMethod = 'online'): array
    {
        DB::beginTransaction();

        try {
            $payment = $this->createPaymentRecord($booking, $paymentMethod);

            if ($paymentMethod === 'cash') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'cash',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            if ($paymentMethod === 'bank_transfer') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'bank_transfer',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            if (empty($this->apiKey) || $this->apiKey === 'your_secret_key_here') {
                Log::warning('Thawani API credentials not configured', [
                    'booking_id' => $booking->id,
                ]);

                $payment->update([
                    'status' => Payment::STATUS_PENDING,
                    'failure_reason' => 'Payment gateway not configured',
                ]);

                DB::commit();

                return [
                    'success' => false,
                    'payment_method' => 'online',
                    'payment_id' => $payment->id,
                    'message' => 'Payment gateway temporarily unavailable. Please contact support.',
                ];
            }

            $sessionData = $this->createThawaniSession($booking, $payment);

            if (!$sessionData['success']) {
                Log::error('Thawani session creation failed', [
                    'booking_id' => $booking->id,
                    'error' => $sessionData['message'] ?? 'Unknown error',
                ]);

                $payment->update([
                    'status' => Payment::STATUS_PENDING,
                    'failure_reason' => $sessionData['message'] ?? 'Gateway error',
                ]);

                DB::commit();

                return [
                    'success' => false,
                    'payment_method' => 'online',
                    'payment_id' => $payment->id,
                    'message' => $sessionData['message'] ?? 'Payment gateway error. Please try again.',
                ];
            }

            $payment->update([
                'transaction_id' => $sessionData['session_id'],
                'invoice_id' => $sessionData['invoice'] ?? null,
                'payment_url' => $sessionData['redirect_url'],
                'gateway_response' => $sessionData,
                'status' => Payment::STATUS_PROCESSING,
            ]);

            DB::commit();

            return [
                'success' => true,
                'payment_method' => 'online',
                'payment_id' => $payment->id,
                'session_id' => $sessionData['session_id'],
                'redirect_url' => $sessionData['redirect_url'],
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment initiation failed', [
                'booking_id' => $booking->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'payment_method' => $paymentMethod,
                'message' => 'Payment processing error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create Thawani checkout session using native cURL
     *
     * @param Booking $booking
     * @param Payment $payment
     * @return array
     */
    protected function createThawaniSession(Booking $booking, Payment $payment): array
    {
        try {
            if (empty($this->apiKey)) {
                throw new Exception('Thawani API key is not configured');
            }

            $amountInBaisa = (int) round($booking->total_amount * 1000);

            if ($amountInBaisa <= 0) {
                throw new Exception('Invalid payment amount: ' . $booking->total_amount);
            }

            $productName = 'Hall Booking - ' . $booking->booking_number;
            $successUrl = str_replace('127.0.0.1', 'localhost', route('customer.payment.success', ['booking' => $booking->id]));
            $cancelUrl = str_replace('127.0.0.1', 'localhost', route('customer.payment.cancel', ['booking' => $booking->id]));

            $paymentData = [
                'client_reference_id' => $payment->payment_reference,
                'mode' => 'payment',
                'products' => [
                    [
                        'name' => $productName,
                        'quantity' => 1,
                        'unit_amount' => $amountInBaisa,
                    ]
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ];

            $jsonPayload = json_encode($paymentData);

            Log::info('Creating Thawani session with native cURL', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount_baisa' => $amountInBaisa,
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseUrl . '/checkout/session',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => [
                    'thawani-api-key: ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $responseBody = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('cURL error creating Thawani session', [
                    'error' => $curlError,
                    'booking_id' => $booking->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ];
            }

            if (empty($responseBody)) {
                return [
                    'success' => false,
                    'message' => 'Payment gateway returned empty response'
                ];
            }

            $result = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'message' => 'Invalid response format from payment gateway'
                ];
            }

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                $sessionId = $result['data']['session_id'];
                $redirectUrl = 'https://uatcheckout.thawani.om/pay/' . $sessionId . '?key=' . $this->publishableKey;

                Log::info('Thawani session created successfully', [
                    'session_id' => $sessionId,
                    'invoice' => $result['data']['invoice'] ?? null,
                ]);

                return [
                    'success' => true,
                    'session_id' => $sessionId,
                    'invoice' => $result['data']['invoice'] ?? null,
                    'redirect_url' => $redirectUrl,
                    'data' => $result['data']
                ];
            }

            return [
                'success' => false,
                'message' => $result['description'] ?? 'Unknown error from payment gateway'
            ];
        } catch (Exception $e) {
            Log::error('Thawani session creation exception', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error creating payment session: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment status with Thawani using session ID
     *
     * @param string $sessionId
     * @return array
     * @throws Exception
     */
    public function verifyPaymentBySessionId(string $sessionId): array
    {
        try {
            Log::info('Verifying payment with Thawani', [
                'session_id' => $sessionId,
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseUrl . '/checkout/session/' . $sessionId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'thawani-api-key: ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $responseBody = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('cURL error during payment verification', [
                    'session_id' => $sessionId,
                    'error' => $curlError,
                ]);

                throw new Exception('Connection error: ' . $curlError);
            }

            if (empty($responseBody)) {
                throw new Exception('Empty response from payment gateway');
            }

            $result = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid response format from payment gateway');
            }

            Log::info('Thawani verification response', [
                'session_id' => $sessionId,
                'status_code' => $statusCode,
                'payment_status' => $result['data']['payment_status'] ?? 'unknown',
            ]);

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                $paymentStatus = $result['data']['payment_status'] ?? 'unknown';

                return [
                    'success' => true,
                    'status' => $paymentStatus,
                    'is_paid' => $paymentStatus === 'paid',
                    'data' => $result['data']
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'is_paid' => false,
                'message' => $result['description'] ?? 'Verification failed'
            ];
        } catch (Exception $e) {
            Log::error('Payment verification exception', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment with Thawani using Payment model
     *
     * @param Payment $payment
     * @return array
     * @throws Exception
     */
    public function verifyPayment(Payment $payment): array
    {
        if (!$payment->transaction_id) {
            throw new Exception('No transaction ID found for payment verification');
        }

        return $this->verifyPaymentBySessionId($payment->transaction_id);
    }

    /**
     * Process successful payment
     *
     * @param Payment $payment
     * @param array $gatewayData
     * @return void
     */
    /**
     * Process successful payment
     *
     * Updates payment and booking status after successful payment verification
     *
     * @param Payment $payment Payment record to update
     * @param array $gatewayData Gateway response data
     * @return void
     * @throws Exception
     */
    public function processSuccessfulPayment(Payment $payment, array $gatewayData = []): void
    {
        DB::beginTransaction();

        try {
            // Extract invoice ID from gateway data
            $invoiceId = $gatewayData['invoice'] ?? null;
            $transactionId = $gatewayData['session_id'] ?? $payment->transaction_id;

            Log::info('Processing successful payment', [
                'payment_id' => $payment->id,
                'current_status' => $payment->status,
                'invoice_id' => $invoiceId,
                'transaction_id' => $transactionId,
            ]);

            // Mark payment as paid
            $success = $payment->markAsPaid(
                $transactionId,
                $gatewayData,
                $invoiceId
            );

            if (!$success) {
                throw new Exception('Failed to mark payment as paid');
            }

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->payment_reference,
                'booking_id' => $payment->booking_id,
                'new_status' => $payment->fresh()->status,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to process successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Create payment record in database
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return Payment
     */
    protected function createPaymentRecord(Booking $booking, string $paymentMethod): Payment
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => $this->generatePaymentReference(),
            'amount' => $booking->total_amount,
            'currency' => 'OMR',
            'status' => Payment::STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'customer_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Generate unique payment reference
     *
     * @return string Generated reference number
     */
    protected function generatePaymentReference(): string
    {
        // Shorter format: PAY-XXXXXXXXXXXX (16 characters)
        $timestamp = substr((string) time(), -6);
        $random = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));

        return 'PAY-' . $timestamp . $random;
    }

    /**
     * Process refund through Thawani gateway
     *
     * Creates a refund request with Thawani for a paid payment
     *
     * @param Payment $payment Payment to refund
     * @param float $amount Refund amount in OMR
     * @param string $reason Refund reason
     * @param array $metadata Additional metadata
     * @return array Refund result
     * @throws Exception
     */
    public function processRefund(Payment $payment, float $amount, string $reason, array $metadata = []): array
    {
        DB::beginTransaction();

        try {
            // Validate payment can be refunded
            if (!$payment->isPaid()) {
                throw new Exception('Only paid payments can be refunded');
            }

            if (!$payment->transaction_id) {
                throw new Exception('No transaction ID found for refund');
            }

            // Validate refund amount
            $remainingAmount = $payment->getRemainingRefundableAmount();

            if ($amount > $remainingAmount) {
                throw new Exception("Refund amount ({$amount} OMR) exceeds refundable amount ({$remainingAmount} OMR)");
            }

            if ($amount <= 0) {
                throw new Exception('Refund amount must be greater than zero');
            }

            // Convert amount to baisa
            $amountInBaisa = (int) round($amount * 1000);

            // Prepare refund data
            $refundData = [
                'session_id' => $payment->transaction_id,
                'reason' => $this->mapRefundReason($reason),
                'metadata' => array_merge([
                    'payment_id' => (string) $payment->id,
                    'payment_reference' => $payment->payment_reference,
                    'booking_id' => (string) $payment->booking_id,
                    'booking_number' => $payment->booking->booking_number ?? 'N/A',
                    'refund_amount_omr' => (string) $amount,
                    'refund_requested_by' => Auth::user()?->name ?? 'System',
                ], $metadata),
            ];

            // Add amount for partial refund (Thawani API supports this)
            if ($amount < $payment->amount) {
                $refundData['amount'] = $amountInBaisa;
            }

            Log::info('Processing Thawani refund', [
                'payment_id' => $payment->id,
                'refund_amount' => $amount,
                'refund_amount_baisa' => $amountInBaisa,
                'reason' => $reason,
            ]);

            // Call Thawani Refund API
            $response = $this->createThawaniRefund($refundData);

            if (!$response['success']) {
                throw new Exception($response['message'] ?? 'Refund request failed');
            }

            // Determine refund type
            $isFullRefund = ($amount >= $payment->amount);
            $refundStatus = $isFullRefund ? Payment::STATUS_REFUNDED : Payment::STATUS_PARTIALLY_REFUNDED;

            // Update payment record
            $payment->update([
                'status' => $refundStatus,
                'refund_amount' => DB::raw("COALESCE(refund_amount, 0) + {$amount}"),
                'refund_reason' => $reason,
                'refunded_at' => now(),
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['refund' => $response['data']]
                ),
            ]);

            // Update booking if full refund
            if ($isFullRefund && $payment->booking) {
                $payment->booking->update([
                    'status' => 'cancelled',
                    'payment_status' => 'refunded',
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Full refund processed: ' . $reason,
                ]);

                Log::info('Booking cancelled due to full refund', [
                    'booking_id' => $payment->booking_id,
                ]);
            }

            Log::info('Refund processed successfully', [
                'payment_id' => $payment->id,
                'refund_id' => $response['data']['refund_id'] ?? null,
                'refund_amount' => $amount,
                'refund_type' => $isFullRefund ? 'full' : 'partial',
            ]);

            DB::commit();

            return [
                'success' => true,
                'refund_id' => $response['data']['refund_id'] ?? null,
                'amount' => $amount,
                'is_full_refund' => $isFullRefund,
                'data' => $response['data'],
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create refund through Thawani API using native cURL
     *
     * @param array $refundData Refund request data
     * @return array API response
     */
    protected function createThawaniRefund(array $refundData): array
    {
        try {
            $jsonPayload = json_encode($refundData);

            Log::info('Creating Thawani refund', [
                'endpoint' => '/refunds',
                'payload' => $refundData,
            ]);

            // Use native cURL for Thawani API
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseUrl . '/refunds',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => [
                    'thawani-api-key: ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $responseBody = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Check for cURL errors
            if ($curlError) {
                Log::error('cURL error creating refund', [
                    'error' => $curlError,
                ]);

                return [
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ];
            }

            if (empty($responseBody)) {
                return [
                    'success' => false,
                    'message' => 'Empty response from payment gateway'
                ];
            }

            // Decode response
            $result = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'message' => 'Invalid response format from payment gateway'
                ];
            }

            Log::info('Thawani refund API response', [
                'status_code' => $statusCode,
                'response' => $result,
            ]);

            // Check if refund was successful
            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'data' => $result['data']
                ];
            }

            // Handle error
            return [
                'success' => false,
                'message' => $result['description'] ?? $result['message'] ?? 'Refund request failed'
            ];
        } catch (Exception $e) {
            Log::error('Thawani refund exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error creating refund: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Map internal refund reason to Thawani-accepted reason
     *
     * Thawani accepts: requested_by_customer, fraudulent, duplicate, other
     *
     * @param string $reason Internal reason
     * @return string Thawani-compatible reason
     */
    protected function mapRefundReason(string $reason): string
    {
        $reasonMap = [
            'customer_request' => 'requested_by_customer',
            'Customer Request' => 'requested_by_customer',
            'Customer requested refund' => 'requested_by_customer',
            'duplicate' => 'duplicate',
            'Duplicate Payment' => 'duplicate',
            'fraudulent' => 'fraudulent',
            'Fraudulent Transaction' => 'fraudulent',
            'Fraud' => 'fraudulent',
        ];

        // Return mapped reason or default to 'other'
        return $reasonMap[$reason] ?? 'other';
    }

    /**
     * Get refund status from Thawani
     *
     * @param string $refundId Thawani refund ID
     * @return array Refund status
     */
    public function getRefundStatus(string $refundId): array
    {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseUrl . '/refunds/' . $refundId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'thawani-api-key: ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $responseBody = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError || empty($responseBody)) {
                throw new Exception('Failed to get refund status');
            }

            $result = json_decode($responseBody, true);

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'status' => $result['data']['status'] ?? 'unknown',
                    'data' => $result['data']
                ];
            }

            return [
                'success' => false,
                'message' => $result['description'] ?? 'Failed to get refund status'
            ];
        } catch (Exception $e) {
            Log::error('Failed to get refund status', [
                'refund_id' => $refundId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}




