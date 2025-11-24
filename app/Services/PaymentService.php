<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Payment Service for Thawani Gateway Integration
 *
 * Handles payment processing with Thawani Payment Gateway (UAT Environment)
 * Supports online payments, cash, and bank transfers
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
    protected Client $client;

    /**
     * Constructor - Initialize Thawani API client
     */
    public function __construct()
    {
        // Thawani API credentials from .env via config/services.php
        $this->apiKey = config('services.thawani.secret_key');
        $this->publishableKey = config('services.thawani.publishable_key');
        $this->baseUrl = config('services.thawani.base_url', 'https://uatcheckout.thawani.om/api/v1');

        // Initialize Guzzle HTTP client with Thawani headers
        // IMPORTANT: Don't set default headers - pass them per request
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'http_errors' => false, // Don't throw exceptions on 4xx/5xx
            'verify' => true, // Verify SSL certificates
        ]);
    }

    /**
     * Initiate payment session with Thawani
     *
     * Creates a payment record and initiates the appropriate payment flow
     * based on the selected payment method (online, cash, bank_transfer)
     *
     * @param Booking $booking The booking to process payment for
     * @param string $paymentMethod Payment method: online, cash, or bank_transfer
     * @return array Payment initiation result with success status and redirect URL
     */
    public function initiatePayment(Booking $booking, string $paymentMethod = 'online'): array
    {
        DB::beginTransaction();

        try {
            // Create payment record in database
            $payment = $this->createPaymentRecord($booking, $paymentMethod);

            // Handle cash payment - no gateway needed
            if ($paymentMethod === 'cash') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'cash',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            // Handle bank transfer - no gateway needed
            if ($paymentMethod === 'bank_transfer') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'bank_transfer',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            // Handle online payment - integrate with Thawani gateway
            // Check if API credentials are properly configured
            if (empty($this->apiKey) || $this->apiKey === 'your_secret_key_here') {
                Log::warning('Thawani API credentials not configured', [
                    'booking_id' => $booking->id,
                ]);

                // Mark payment as pending manual verification
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

            // Create Thawani checkout session
            $sessionData = $this->createThawaniSession($booking, $payment);

            if (!$sessionData['success']) {
                Log::error('Thawani session creation failed', [
                    'booking_id' => $booking->id,
                    'error' => $sessionData['message'] ?? 'Unknown error',
                ]);

                // Mark payment as pending for manual processing
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

            // Update payment record with gateway session details
            $payment->update([
                'transaction_id' => $sessionData['session_id'],
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
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response instead of throwing exception
            return [
                'success' => false,
                'payment_method' => $paymentMethod,
                'message' => 'Payment processing error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create Thawani checkout session
     *
     * Calls Thawani API to create a new checkout session for the booking
     * Converts amount to baisa (Omani currency subdivision)
     *
     * @param Booking $booking The booking to create session for
     * @param Payment $payment The payment record
     * @return array Session creation result with session_id and redirect_url
     */


    /**
     * Make HTTP POST request using cURL
     *
     * Wrapper method for easier testing and maintenance
     *
     * @param string $endpoint API endpoint
     * @param array $data Request payload
     * @param array $headers HTTP headers
     * @return array Response with status_code, body, and error
     */
    protected function curlPost(string $endpoint, array $data, array $headers = []): array
    {
        $jsonPayload = json_encode($data);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => array_merge([
                'Content-Type: application/json',
            ], $headers),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [
            'status_code' => $statusCode,
            'body' => $responseBody,
            'error' => $curlError ?: null,
        ];
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
            // Validate API key
            if (empty($this->apiKey)) {
                throw new Exception('Thawani API key is not configured');
            }


            // Convert amount to baisa (1 OMR = 1000 baisa)
            $amountInBaisa = (int) round($booking->total_amount * 1000);

            if ($amountInBaisa <= 0) {
                throw new Exception('Invalid payment amount: ' . $booking->total_amount);
            }

            // Product name (English only to avoid encoding issues)
            $productName = 'Hall Booking - ' . $booking->booking_number;

            // Build URLs and force localhost (Thawani rejects 127.0.0.1)
            $successUrl = route('customer.payment.success', ['booking' => $booking->id]);
            $cancelUrl = route('customer.payment.cancel', ['booking' => $booking->id]);

            $successUrl = str_replace('127.0.0.1', 'localhost', $successUrl);
            $cancelUrl = str_replace('127.0.0.1', 'localhost', $cancelUrl);

            // Prepare payment data according to Thawani API specification
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

            // Use the wrapper method
            $response = $this->curlPost('/checkout/session', $paymentData, [
                'thawani-api-key: ' . $this->apiKey,
            ]);

            if ($response['error']) {
                return ['success' => false, 'message' => $response['error']];
            }

            $jsonPayload = json_encode($paymentData);

            Log::info('Creating Thawani session with native cURL', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount_baisa' => $amountInBaisa,
                'amount_omr' => $booking->total_amount,
                'product_name' => $productName,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            // Use native cURL (exactly like successful terminal cURL)
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

            // Check for cURL errors
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

            Log::info('Thawani API response', [
                'status_code' => $statusCode,
                'has_body' => !empty($responseBody),
                'body_length' => strlen($responseBody),
                'booking_id' => $booking->id,
            ]);

            // Handle empty response
            if (empty($responseBody)) {
                Log::error('Thawani returned empty response', [
                    'status_code' => $statusCode,
                    'booking_id' => $booking->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'Payment gateway returned empty response (Status: ' . $statusCode . ')'
                ];
            }

            // Decode JSON response
            $result = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode Thawani response', [
                    'json_error' => json_last_error_msg(),
                    'raw_response' => $responseBody,
                    'booking_id' => $booking->id,
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid response format from payment gateway'
                ];
            }

            Log::info('Thawani API decoded response', [
                'status_code' => $statusCode,
                'success' => $result['success'] ?? false,
                'code' => $result['code'] ?? null,
                'description' => $result['description'] ?? null,
                'booking_id' => $booking->id,
            ]);

            // Check if session creation was successful
            if ($statusCode === 200 && isset($result['success']) && $result['success'] === true) {
                $sessionId = $result['data']['session_id'];

                // Build redirect URL for Thawani checkout page
                $redirectUrl = 'https://uatcheckout.thawani.om/pay/' . $sessionId . '?key=' . $this->publishableKey;

                Log::info('Thawani session created successfully', [
                    'session_id' => $sessionId,
                    'redirect_url' => $redirectUrl,
                    'invoice' => $result['data']['invoice'] ?? null,
                    'booking_id' => $booking->id,
                ]);

                return [
                    'success' => true,
                    'session_id' => $sessionId,
                    'redirect_url' => $redirectUrl,
                    'data' => $result['data']
                ];
            }

            // Handle API error response
            $errorMessage = $result['description']
                ?? $result['message']
                ?? 'Unknown error from payment gateway';

            Log::error('Thawani session creation failed', [
                'error' => $errorMessage,
                'status_code' => $statusCode,
                'full_response' => $result,
                'booking_id' => $booking->id,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        } catch (Exception $e) {
            Log::error('Thawani session creation exception', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error creating payment session: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment status with Thawani
     *
     * Retrieves the current status of a payment session from Thawani
     *
     * @param string $sessionId Thawani session ID
     * @return array Verification result with payment status
     */
    public function verifyPaymentBySessionId(string $sessionId): array
    {
        try {
            Log::info('Verifying payment with Thawani', [
                'session_id' => $sessionId,
            ]);

            // Call Thawani API to get session details
            $response = $this->client->get('/checkout/session/' . $sessionId);
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);

            Log::info('Thawani verification response', [
                'session_id' => $sessionId,
                'status_code' => $statusCode,
                'response' => $result,
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
        } catch (GuzzleException $e) {
            Log::error('Payment verification error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment with Thawani using Payment model
     *
     * @param Payment $payment Payment record to verify
     * @return array Verification result
     * @throws Exception If no transaction ID found
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
     * Updates payment and booking status after successful payment verification
     *
     * @param Payment $payment Payment record to update
     * @param array $gatewayData Gateway response data
     * @return void
     */
    public function processSuccessfulPayment(Payment $payment, array $gatewayData = []): void
    {
        DB::beginTransaction();

        try {
            // Mark payment as paid
            $payment->markAsPaid(
                $gatewayData['invoice_id'] ?? $payment->transaction_id,
                $gatewayData,
                $gatewayData['invoice_id'] ?? null
            );

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->payment_reference,
                'booking_id' => $payment->booking_id,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to process successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Create payment record in database
     *
     * @param Booking $booking Booking to create payment for
     * @param string $paymentMethod Payment method
     * @return Payment Created payment record
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
        return 'PAY-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Get hall name from translatable field
     *
     * Handles both string and array (translatable) hall names
     *
     * @param mixed $name Hall name (string or translatable array)
     * @return string Processed hall name
     */
    protected function getHallName($name): string
    {
        // Handle translatable fields (array format)
        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? $name['ar'] ?? 'Hall Booking';
        }

        // Handle regular string
        return $name ?? 'Hall Booking';
    }
}
