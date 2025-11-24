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
 * @package App\Services
 */
class PaymentService_copy
{
    /**
     * Thawani API Configuration
     */
    protected string $apiKey;
    protected string $baseUrl;
    protected string $publishableKey;
    protected Client $client;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Thawani API credentials from .env
        $this->apiKey = config('services.thawani.secret_key');
        $this->publishableKey = config('services.thawani.publishable_key');
        $this->baseUrl = config('services.thawani.base_url', 'https://uatcheckout.thawani.om/api/v1');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'thawani-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Initiate payment session with Thawani
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return array
     * @throws Exception
     */
    // public function initiatePayment(Booking $booking, string $paymentMethod = 'online'): array
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Create payment record
    //         $payment = $this->createPaymentRecord($booking, $paymentMethod);

    //         // For cash payment, no gateway needed
    //         if ($paymentMethod === 'cash') {
    //             DB::commit();
    //             return [
    //                 'success' => true,
    //                 'payment_method' => 'cash',
    //                 'payment_id' => $payment->id,
    //                 'redirect_url' => null,
    //             ];
    //         }

    //         // For bank transfer, no gateway needed
    //         if ($paymentMethod === 'bank_transfer') {
    //             DB::commit();
    //             return [
    //                 'success' => true,
    //                 'payment_method' => 'bank_transfer',
    //                 'payment_id' => $payment->id,
    //                 'redirect_url' => null,
    //             ];
    //         }

    //         // For online payment, integrate with Thawani
    //         $sessionData = $this->createThawaniSession($booking, $payment);

    //         if (!$sessionData['success']) {
    //             throw new Exception($sessionData['message'] ?? 'Failed to create payment session');
    //         }

    //         // Update payment with gateway details
    //         $payment->update([
    //             'transaction_id' => $sessionData['session_id'],
    //             'payment_url' => $sessionData['redirect_url'],
    //             'gateway_response' => $sessionData,
    //             'status' => Payment::STATUS_PROCESSING,
    //         ]);

    //         DB::commit();

    //         return [
    //             'success' => true,
    //             'payment_method' => 'online',
    //             'payment_id' => $payment->id,
    //             'session_id' => $sessionData['session_id'],
    //             'redirect_url' => $sessionData['redirect_url'],
    //         ];
    //     } catch (Exception $e) {
    //         DB::rollBack();

    //         Log::error('Payment initiation failed', [
    //             'booking_id' => $booking->id,
    //             'error' => $e->getMessage()
    //         ]);

    //         throw $e;
    //     }
    // }

    /**
     * Initiate payment session with Thawani
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return array
     * @throws Exception
     */
    public function initiatePayment(Booking $booking, string $paymentMethod = 'online'): array
    {
        DB::beginTransaction();

        try {
            // Create payment record
            $payment = $this->createPaymentRecord($booking, $paymentMethod);

            // For cash payment, no gateway needed
            if ($paymentMethod === 'cash') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'cash',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            // For bank transfer, no gateway needed
            if ($paymentMethod === 'bank_transfer') {
                DB::commit();
                return [
                    'success' => true,
                    'payment_method' => 'bank_transfer',
                    'payment_id' => $payment->id,
                    'redirect_url' => null,
                ];
            }

            // For online payment, integrate with Thawani
            // Check if API credentials are configured
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
                    'message' => 'Payment gateway temporarily unavailable',
                ];
            }

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
                    'message' => $sessionData['message'] ?? 'Payment gateway error',
                ];
            }

            // Update payment with gateway details
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
                'error' => $e->getMessage()
            ]);

            // Don't throw exception, return error response
            return [
                'success' => false,
                'payment_method' => $paymentMethod,
                'message' => 'Payment processing error: ' . $e->getMessage(),
            ];
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
     * Create Thawani checkout session
     *
     * @param Booking $booking
     * @param Payment $payment
     * @return array
     */
    // protected function createThawaniSession(Booking $booking, Payment $payment): array
    // {
    //     try {
    //         // Convert amount to baisa (1 OMR = 1000 baisa)
    //         $amountInBaisa = (int) ($booking->total_amount * 1000);

    //         // Prepare payment data
    //         $paymentData = [
    //             'client_reference_id' => $payment->payment_reference,
    //             'mode' => 'payment',
    //             'products' => [
    //                 [
    //                     'name' => $this->getHallName($booking->hall->name),
    //                     'quantity' => 1,
    //                     'unit_amount' => $amountInBaisa,
    //                 ]
    //             ],
    //             'success_url' => route('customer.payment.success', ['booking' => $booking->id]),
    //             'cancel_url' => route('customer.payment.cancel', ['booking' => $booking->id]),
    //             'metadata' => [
    //                 'booking_id' => $booking->id,
    //                 'booking_number' => $booking->booking_number,
    //                 'payment_id' => $payment->id,
    //                 'payment_reference' => $payment->payment_reference,
    //             ]
    //         ];

    //         Log::info('Creating Thawani session', [
    //             'booking_id' => $booking->id,
    //             'payment_id' => $payment->id,
    //             'amount' => $amountInBaisa,
    //         ]);

    //         // Create checkout session
    //         $response = $this->client->post('/checkout/session', [
    //             'json' => $paymentData
    //         ]);

    //         $result = json_decode($response->getBody()->getContents(), true);

    //         if ($result['success'] ?? false) {
    //             $sessionId = $result['data']['session_id'];

    //             return [
    //                 'success' => true,
    //                 'session_id' => $sessionId,
    //                 'redirect_url' => $this->baseUrl . '/checkout/' . $sessionId . '?key=' . $this->publishableKey,
    //                 'data' => $result['data']
    //             ];
    //         }

    //         return [
    //             'success' => false,
    //             'message' => $result['description'] ?? 'Unknown error'
    //         ];
    //     } catch (GuzzleException $e) {
    //         Log::error('Thawani API error', [
    //             'booking_id' => $booking->id,
    //             'error' => $e->getMessage()
    //         ]);

    //         return [
    //             'success' => false,
    //             'message' => 'Payment gateway error: ' . $e->getMessage()
    //         ];
    //     }
    // }

    /**
     * Create Thawani checkout session
     *
     * @param Booking $booking
     * @param Payment $payment
     * @return array
     */
    protected function createThawaniSession(Booking $booking, Payment $payment): array
    {
        try {
            // Check if API key is set
            if (empty($this->apiKey)) {
                throw new Exception('Thawani API key is not configured');
            }

            // Convert amount to baisa (1 OMR = 1000 baisa)
            $amountInBaisa = (int) ($booking->total_amount * 1000);

            if ($amountInBaisa <= 0) {
                throw new Exception('Invalid payment amount: ' . $booking->total_amount);
            }

            // Prepare payment data
            $paymentData = [
                'client_reference_id' => $payment->payment_reference,
                'mode' => 'payment',
                'products' => [
                    [
                        'name' => $this->getHallName($booking->hall->name),
                        'quantity' => 1,
                        'unit_amount' => $amountInBaisa,
                    ]
                ],
                'success_url' => route('customer.payment.success', ['booking' => $booking->id]),
                'cancel_url' => route('customer.payment.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'payment_id' => $payment->id,
                    'payment_reference' => $payment->payment_reference,
                ]
            ];

            Log::info('Creating Thawani session', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $amountInBaisa,
                'amount_omr' => $booking->total_amount,
                'api_endpoint' => $this->baseUrl . '/checkout/session',
                'payload' => $paymentData,
            ]);

            // Create checkout session
            $response = $this->client->post('/checkout/session', [
                'json' => $paymentData
            ]);

            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);

            Log::info('Thawani API response', [
                'status_code' => $response->getStatusCode(),
                'response' => $result,
            ]);

            if ($result['success'] ?? false) {
                $sessionId = $result['data']['session_id'];
                $redirectUrl = $this->baseUrl . '/checkout/' . $sessionId . '?key=' . $this->publishableKey;

                Log::info('Thawani session created successfully', [
                    'session_id' => $sessionId,
                    'redirect_url' => $redirectUrl,
                ]);

                return [
                    'success' => true,
                    'session_id' => $sessionId,
                    'redirect_url' => $redirectUrl,
                    'data' => $result['data']
                ];
            }

            $errorMessage = $result['description'] ?? $result['message'] ?? 'Unknown error';
            Log::error('Thawani session creation failed', [
                'error' => $errorMessage,
                'full_response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        } catch (GuzzleException $e) {
            Log::error('Thawani API GuzzleException', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            // Try to get response body for more details
            if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                Log::error('Thawani API error response', [
                    'response_body' => $responseBody,
                ]);
            }

            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            Log::error('Thawani session creation exception', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment with Thawani
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

        try {
            $response = $this->client->get('/checkout/session/' . $payment->transaction_id);
            $result = json_decode($response->getBody()->getContents(), true);

            if ($result['success'] ?? false) {
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
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Process successful payment
     *
     * @param Payment $payment
     * @param array $gatewayData
     * @return void
     */
    public function processSuccessfulPayment(Payment $payment, array $gatewayData = []): void
    {
        DB::beginTransaction();

        try {
            // Mark payment as paid
            $payment->markAsPaid(
                $gatewayData['id'] ?? $payment->transaction_id,
                $gatewayData
            );

            // Update booking
            $payment->booking->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            DB::commit();

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Process failed payment
     *
     * @param Payment $payment
     * @param string $reason
     * @param array $gatewayData
     * @return void
     */
    public function processFailedPayment(Payment $payment, string $reason, array $gatewayData = []): void
    {
        $payment->markAsFailed($reason, $gatewayData);

        Log::warning('Payment failed', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'reason' => $reason,
        ]);
    }

    /**
     * Process refund
     *
     * @param Payment $payment
     * @param float $amount
     * @param string $reason
     * @return array
     * @throws Exception
     */
    public function processRefund(Payment $payment, float $amount, string $reason): array
    {
        if (!$payment->isPaid()) {
            throw new Exception('Cannot refund unpaid payment');
        }

        try {
            $amountInBaisa = (int) ($amount * 1000);

            $response = $this->client->post('/refunds', [
                'json' => [
                    'payment_id' => $payment->transaction_id,
                    'amount' => $amountInBaisa,
                    'reason' => $reason,
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->booking_id,
                    ]
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($result['success'] ?? false) {
                $payment->update([
                    'status' => Payment::STATUS_REFUNDED,
                    'refund_amount' => $amount,
                    'refund_reason' => $reason,
                    'refunded_at' => now(),
                ]);

                return [
                    'success' => true,
                    'refund_id' => $result['data']['id'],
                    'data' => $result['data']
                ];
            }

            throw new Exception('Refund failed: ' . ($result['description'] ?? 'Unknown error'));
        } catch (GuzzleException $e) {
            Log::error('Refund error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw new Exception('Refund processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique payment reference
     *
     * @return string
     */
    protected function generatePaymentReference(): string
    {
        return 'PAY-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Get hall name in proper format
     *
     * @param mixed $name
     * @return string
     */
    protected function getHallName($name): string
    {
        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? 'Hall Booking';
        }
        return $name ?? 'Hall Booking';
    }
}
