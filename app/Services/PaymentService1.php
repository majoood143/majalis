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
 * NOW SUPPORTS ADVANCE PAYMENT FEATURE
 *
 * @package App\Services
 */
class PaymentService1
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
     * ✅ UPDATED: Now supports advance payment
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

            // ✅ NEW: Calculate payment amount based on payment type
            // If booking requires advance, charge advance amount only
            // Otherwise, charge full amount
            $paymentAmount = $booking->isAdvancePayment()
                ? $booking->advance_amount
                : $booking->total_amount;

            // Convert OMR to Baisa (1 OMR = 1000 Baisa)
            $amountInBaisa = (int) round($paymentAmount * 1000);

            if ($amountInBaisa <= 0) {
                throw new Exception('Invalid payment amount: ' . $paymentAmount);
            }

            // ✅ NEW: Enhanced product name to indicate payment type
            $productName = 'Hall Booking - ' . $booking->booking_number;
            if ($booking->isAdvancePayment()) {
                $productName .= ' (Advance Payment)';
            }

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
                'payment_type' => $booking->payment_type,
                'is_advance' => $booking->isAdvancePayment(),
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

            Log::info('Thawani session created successfully', [
                'booking_id' => $booking->id,
                'session_id' => $result['data']['session_id'] ?? 'N/A',
                'status_code' => $statusCode,
            ]);

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                $sessionId = $result['data']['session_id'];
                $redirectUrl = "https://uatcheckout.thawani.om/pay/{$sessionId}?key={$this->publishableKey}";

                return [
                    'success' => true,
                    'session_id' => $sessionId,
                    'redirect_url' => $redirectUrl,
                    'invoice' => $result['data']['invoice'] ?? null,
                    'response' => $result
                ];
            }

            return [
                'success' => false,
                'message' => $result['description'] ?? $result['message'] ?? 'Failed to create payment session'
            ];
        } catch (Exception $e) {
            Log::error('Thawani session creation exception', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id ?? null,
            ]);

            return [
                'success' => false,
                'message' => 'Payment session error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create payment record in database
     *
     * ✅ UPDATED: Now stores correct amount based on payment type
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return Payment
     */
    protected function createPaymentRecord(Booking $booking, string $paymentMethod): Payment
    {
        // ✅ NEW: Determine payment amount based on payment type
        $paymentAmount = $booking->isAdvancePayment()
            ? $booking->advance_amount
            : $booking->total_amount;

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'amount' => $paymentAmount, // ✅ Uses advance or full amount
            'currency' => 'OMR',
            'payment_method' => $paymentMethod,
            'status' => Payment::STATUS_PENDING,
        ]);

        Log::info('Payment record created', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => $paymentAmount,
            'payment_type' => $booking->payment_type,
            'is_advance' => $booking->isAdvancePayment(),
        ]);

        return $payment;
    }

    /**
     * Handle successful payment from Thawani
     *
     * ✅ UPDATED: Now handles advance payments correctly
     *
     * @param string $sessionId Thawani session ID
     * @param Booking $booking
     * @return array
     */
    public function handlePaymentSuccess(string $sessionId, Booking $booking): array
    {
        DB::beginTransaction();

        try {
            $payment = Payment::where('booking_id', $booking->id)
                ->where('transaction_id', $sessionId)
                ->firstOrFail();

            $sessionStatus = $this->getSessionStatus($sessionId);

            if (!$sessionStatus['success']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to verify payment status'
                ];
            }

            $paymentStatus = $sessionStatus['data']['payment_status'] ?? 'unpaid';

            if ($paymentStatus === 'paid') {
                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'gateway_response' => array_merge(
                        $payment->gateway_response ?? [],
                        ['session_status' => $sessionStatus['data']]
                    ),
                ]);

                // ✅ NEW: Handle advance vs full payment
                if ($booking->isAdvancePayment()) {
                    // Customer paid advance only
                    $booking->update([
                        'payment_status' => 'partial', // ✅ NEW STATUS
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);

                    Log::info('Advance payment successful', [
                        'booking_id' => $booking->id,
                        'advance_paid' => $booking->advance_amount,
                        'balance_due' => $booking->balance_due,
                    ]);
                } else {
                    // Customer paid full amount
                    $booking->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);

                    Log::info('Full payment successful', [
                        'booking_id' => $booking->id,
                        'amount_paid' => $booking->total_amount,
                    ]);
                }

                DB::commit();

                return [
                    'success' => true,
                    'payment_status' => $paymentStatus,
                    'booking_status' => $booking->status,
                    'is_advance' => $booking->isAdvancePayment(),
                ];
            }

            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Payment not completed',
                'payment_status' => $paymentStatus
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment success handling failed', [
                'session_id' => $sessionId,
                'booking_id' => $booking->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get session status from Thawani
     *
     * @param string $sessionId
     * @return array
     */
    public function getSessionStatus(string $sessionId): array
    {
        try {
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
                throw new Exception('cURL error: ' . $curlError);
            }

            if (empty($responseBody)) {
                throw new Exception('Empty response from payment gateway');
            }

            $result = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response');
            }

            Log::info('Thawani session status retrieved', [
                'session_id' => $sessionId,
                'status_code' => $statusCode,
                'payment_status' => $result['data']['payment_status'] ?? 'unknown',
            ]);

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'data' => $result['data']
                ];
            }

            return [
                'success' => false,
                'message' => $result['description'] ?? $result['message'] ?? 'Failed to get session status'
            ];
        } catch (Exception $e) {
            Log::error('Failed to get session status', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Process refund for a payment
     *
     * @param Payment $payment
     * @param float $amount Amount to refund in OMR
     * @param string $reason Refund reason
     * @return array
     */
    public function processRefund(Payment $payment, float $amount, string $reason = 'Customer Request'): array
    {
        DB::beginTransaction();

        try {
            if ($payment->status !== Payment::STATUS_PAID) {
                throw new Exception('Only paid payments can be refunded');
            }

            if ($amount > $payment->amount) {
                throw new Exception('Refund amount cannot exceed payment amount');
            }

            $amountInBaisa = (int) round($amount * 1000);

            if (!$payment->transaction_id) {
                throw new Exception('Payment transaction ID not found');
            }

            $mappedReason = $this->mapRefundReason($reason);

            $refundData = [
                'session_id' => $payment->transaction_id,
                'reason' => $mappedReason,
                'metadata' => [
                    'booking_id' => $payment->booking_id,
                    'original_reason' => $reason,
                ]
            ];

            if ($amount < $payment->amount) {
                $refundData['amount'] = $amountInBaisa;
            }

            Log::info('Processing refund', [
                'payment_id' => $payment->id,
                'session_id' => $payment->transaction_id,
                'amount' => $amount,
                'amount_baisa' => $amountInBaisa,
                'reason' => $mappedReason,
            ]);

            $response = $this->createThawaniRefund($refundData);

            if (!$response['success']) {
                throw new Exception($response['message'] ?? 'Refund request failed');
            }

            $isFullRefund = ($amount >= $payment->amount);
            $refundStatus = $isFullRefund ? Payment::STATUS_REFUNDED : Payment::STATUS_PARTIALLY_REFUNDED;

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

            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                return [
                    'success' => true,
                    'data' => $result['data']
                ];
            }

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
