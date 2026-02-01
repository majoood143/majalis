<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * =============================================================================
 * THAWANI PAYMENT GATEWAY SERVICE
 * =============================================================================
 * 
 * This service handles all interactions with the Thawani payment gateway.
 * 
 * Place this file at: app/Services/ThawaniService.php
 * 
 * =============================================================================
 * CONFIGURATION REQUIRED IN .env FILE:
 * =============================================================================
 * 
 * THAWANI_MODE=test                                    # 'test' or 'live'
 * THAWANI_SECRET_KEY=your_secret_key_here              # From Thawani dashboard
 * THAWANI_PUBLISHABLE_KEY=your_publishable_key_here    # From Thawani dashboard
 * 
 * =============================================================================
 * OPTIONAL: Add to config/services.php:
 * =============================================================================
 * 
 * 'thawani' => [
 *     'mode' => env('THAWANI_MODE', 'test'),
 *     'secret_key' => env('THAWANI_SECRET_KEY'),
 *     'publishable_key' => env('THAWANI_PUBLISHABLE_KEY'),
 * ],
 * 
 * =============================================================================
 */
class ThawaniService
{
    /**
     * Thawani API base URLs for test and live modes.
     */
    protected const BASE_URL_TEST = 'https://uatcheckout.thawani.om/api/v1';
    protected const BASE_URL_LIVE = 'https://checkout.thawani.om/api/v1';
    
    /**
     * Thawani checkout page URLs for test and live modes.
     */
    protected const CHECKOUT_URL_TEST = 'https://uatcheckout.thawani.om/pay/';
    protected const CHECKOUT_URL_LIVE = 'https://checkout.thawani.om/pay/';

    /**
     * Current mode (test/live).
     *
     * @var string
     */
    protected string $mode;

    /**
     * Secret API key.
     *
     * @var string
     */
    protected string $secretKey;

    /**
     * Publishable key.
     *
     * @var string
     */
    protected string $publishableKey;

    /**
     * API base URL based on mode.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * Checkout URL based on mode.
     *
     * @var string
     */
    protected string $checkoutUrl;

    /**
     * ThawaniService constructor.
     * 
     * Initializes the service with configuration from environment variables
     * or config/services.php.
     */
    public function __construct()
    {
        // Get configuration from config/services.php or environment
        $this->mode = config('services.thawani.mode', env('THAWANI_MODE', 'test'));
        $this->secretKey = config('services.thawani.secret_key', env('THAWANI_SECRET_KEY', ''));
        $this->publishableKey = config('services.thawani.publishable_key', env('THAWANI_PUBLISHABLE_KEY', ''));

        // Set URLs based on mode
        $this->baseUrl = $this->mode === 'live' ? self::BASE_URL_LIVE : self::BASE_URL_TEST;
        $this->checkoutUrl = $this->mode === 'live' ? self::CHECKOUT_URL_LIVE : self::CHECKOUT_URL_TEST;

        // Log warning if keys are not configured
        if (empty($this->secretKey)) {
            Log::warning('ThawaniService: Secret key is not configured');
        }
    }

    /**
     * Create a checkout session for payment.
     *
     * This creates a new payment session with Thawani and returns
     * the session ID and payment URL.
     *
     * @param array $data Payment data including:
     *                    - client_reference_id: Your unique payment reference
     *                    - products: Array of product items
     *                    - success_url: URL to redirect after successful payment
     *                    - cancel_url: URL to redirect if payment is cancelled
     *                    - metadata: Optional additional data
     *                    - customer_email: Customer's email (optional)
     *                    - customer_phone: Customer's phone (optional)
     *
     * @return array Contains 'session_id' and 'payment_url'
     * @throws Exception If API call fails or returns error
     */
    public function createCheckoutSession(array $data): array
    {
        // Validate required fields
        if (empty($data['client_reference_id'])) {
            throw new Exception('client_reference_id is required');
        }
        if (empty($data['products']) || !is_array($data['products'])) {
            throw new Exception('products array is required');
        }
        if (empty($data['success_url'])) {
            throw new Exception('success_url is required');
        }
        if (empty($data['cancel_url'])) {
            throw new Exception('cancel_url is required');
        }

        // Prepare request payload
        $payload = [
            'client_reference_id' => $data['client_reference_id'],
            'mode' => 'payment',
            'products' => $data['products'],
            'success_url' => $data['success_url'],
            'cancel_url' => $data['cancel_url'],
        ];

        // Add optional metadata
        if (!empty($data['metadata'])) {
            $payload['metadata'] = $data['metadata'];
        }

        // Add customer information if provided
        if (!empty($data['customer_email'])) {
            $payload['customer_email'] = $data['customer_email'];
        }

        Log::debug('ThawaniService: Creating checkout session', [
            'client_reference_id' => $data['client_reference_id'],
            'products_count' => count($data['products']),
            'mode' => $this->mode,
        ]);

        try {
            // Make API request to Thawani
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'thawani-api-key' => $this->secretKey,
            ])->post("{$this->baseUrl}/checkout/session", $payload);

            // Parse response
            $responseData = $response->json();

            // Check for errors
            if (!$response->successful()) {
                $errorMessage = $responseData['message'] ?? $responseData['description'] ?? 'Unknown error';
                Log::error('ThawaniService: API error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                    'client_reference_id' => $data['client_reference_id'],
                ]);
                throw new Exception("Thawani API error: {$errorMessage}");
            }

            // Validate response data
            if (!isset($responseData['data']['session_id'])) {
                Log::error('ThawaniService: Invalid response - missing session_id', [
                    'response' => $responseData,
                ]);
                throw new Exception('Invalid Thawani response: missing session_id');
            }

            $sessionId = $responseData['data']['session_id'];
            $paymentUrl = $this->checkoutUrl . $sessionId . '?key=' . $this->publishableKey;

            Log::info('ThawaniService: Checkout session created', [
                'session_id' => $sessionId,
                'client_reference_id' => $data['client_reference_id'],
            ]);

            return [
                'session_id' => $sessionId,
                'payment_url' => $paymentUrl,
                'publishable_key' => $this->publishableKey,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('ThawaniService: Exception creating checkout session', [
                'error' => $e->getMessage(),
                'client_reference_id' => $data['client_reference_id'],
            ]);
            throw $e;
        }
    }

    /**
     * Get the status of a checkout session.
     *
     * This retrieves the current status of a payment session from Thawani.
     *
     * @param string $sessionId The Thawani session ID
     * @return array Session data including payment_status
     * @throws Exception If API call fails
     */
    public function getSessionStatus(string $sessionId): array
    {
        Log::debug('ThawaniService: Getting session status', [
            'session_id' => $sessionId,
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'thawani-api-key' => $this->secretKey,
            ])->get("{$this->baseUrl}/checkout/session/{$sessionId}");

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorMessage = $responseData['message'] ?? 'Unknown error';
                Log::error('ThawaniService: Session status error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                    'session_id' => $sessionId,
                ]);
                throw new Exception("Thawani API error: {$errorMessage}");
            }

            $sessionData = $responseData['data'] ?? [];

            Log::info('ThawaniService: Session status retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $sessionData['payment_status'] ?? 'unknown',
            ]);

            return $sessionData;

        } catch (Exception $e) {
            Log::error('ThawaniService: Exception getting session status', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            throw $e;
        }
    }

    /**
     * Refund a payment.
     *
     * @param string $paymentId The Thawani payment ID
     * @param int|null $amount Amount in baisa to refund (null for full refund)
     * @param string|null $reason Reason for refund
     * @return array Refund response data
     * @throws Exception If refund fails
     */
    public function refundPayment(string $paymentId, ?int $amount = null, ?string $reason = null): array
    {
        Log::debug('ThawaniService: Initiating refund', [
            'payment_id' => $paymentId,
            'amount' => $amount,
        ]);

        $payload = [
            'payment_id' => $paymentId,
        ];

        if ($amount !== null) {
            $payload['amount'] = $amount;
        }

        if ($reason !== null) {
            $payload['reason'] = $reason;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'thawani-api-key' => $this->secretKey,
            ])->post("{$this->baseUrl}/refunds", $payload);

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorMessage = $responseData['message'] ?? 'Unknown error';
                Log::error('ThawaniService: Refund error', [
                    'status' => $response->status(),
                    'response' => $responseData,
                    'payment_id' => $paymentId,
                ]);
                throw new Exception("Thawani refund error: {$errorMessage}");
            }

            Log::info('ThawaniService: Refund initiated', [
                'payment_id' => $paymentId,
                'refund_data' => $responseData['data'] ?? [],
            ]);

            return $responseData['data'] ?? [];

        } catch (Exception $e) {
            Log::error('ThawaniService: Exception during refund', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
            ]);
            throw $e;
        }
    }

    /**
     * Get publishable key for frontend use.
     *
     * @return string
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    /**
     * Get current mode (test/live).
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Check if service is properly configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publishableKey);
    }
}
