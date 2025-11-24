<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

/**
 * TEMPORARY TEST ROUTE - Remove after testing
 * Access: http://your-domain/test-thawani
 */
Route::get('/test-thawani', function () {
    $apiKey = config('services.thawani.secret_key');
    $publishableKey = config('services.thawani.publishable_key');
    $baseUrl = config('services.thawani.base_url');

    // Display configuration
    $output = '<h1>Thawani UAT Test</h1>';
    $output .= '<h2>Configuration:</h2>';
    $output .= '<pre>';
    $output .= "Base URL: {$baseUrl}\n";
    $output .= "Secret Key: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -5) . "\n";
    $output .= "Publishable Key: " . substr($publishableKey, 0, 10) . "..." . substr($publishableKey, -5) . "\n";
    $output .= "Secret Key Length: " . strlen($apiKey) . "\n";
    $output .= "Publishable Key Length: " . strlen($publishableKey) . "\n";
    $output .= '</pre>';

    // Test 1: Simple API test with minimal data
    $output .= '<h2>Test 1: Minimal Request</h2>';

    $client = new Client([
        'base_uri' => $baseUrl,
        'timeout' => 30,
        'http_errors' => false,
    ]);

    $testPayload = [
        'client_reference_id' => 'TEST-' . time(),
        'mode' => 'payment',
        'products' => [
            [
                'name' => 'Test Product',
                'quantity' => 1,
                'unit_amount' => 1000, // 1 OMR in baisa
            ]
        ],
        'success_url' => url('/test-success'),
        'cancel_url' => url('/test-cancel'),
    ];

    try {
        $response = $client->post('/checkout/session', [
            'headers' => [
                'thawani-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $testPayload
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $result = json_decode($body, true);

        $output .= '<pre>';
        $output .= "Status Code: {$statusCode}\n";
        $output .= "Raw Response: " . $body . "\n";
        $output .= "Decoded Response: " . print_r($result, true) . "\n";
        $output .= '</pre>';

        if ($statusCode === 200 && isset($result['success']) && $result['success']) {
            $output .= '<p style="color: green; font-weight: bold;">✅ SUCCESS! Session created.</p>';
            $sessionId = $result['data']['session_id'];
            $paymentUrl = "https://uatcheckout.thawani.om/pay/{$sessionId}?key={$publishableKey}";
            $output .= '<p><a href="' . $paymentUrl . '" target="_blank">Open Payment Page</a></p>';
        } else {
            $output .= '<p style="color: red; font-weight: bold;">❌ FAILED!</p>';
        }
    } catch (Exception $e) {
        $output .= '<pre style="color: red;">';
        $output .= "Exception: " . $e->getMessage() . "\n";
        $output .= "Trace: " . $e->getTraceAsString() . "\n";
        $output .= '</pre>';
    }

    // Test 2: With Arabic product name
    $output .= '<h2>Test 2: With Arabic Product Name</h2>';

    $testPayload2 = [
        'client_reference_id' => 'TEST-AR-' . time(),
        'mode' => 'payment',
        'products' => [
            [
                'name' => 'قاعة الاحتفالات', // Arabic text
                'quantity' => 1,
                'unit_amount' => 2000,
            ]
        ],
        'success_url' => url('/test-success'),
        'cancel_url' => url('/test-cancel'),
    ];

    try {
        $response2 = $client->post('/checkout/session', [
            'headers' => [
                'thawani-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $testPayload2
        ]);

        $statusCode2 = $response2->getStatusCode();
        $body2 = $response2->getBody()->getContents();
        $result2 = json_decode($body2, true);

        $output .= '<pre>';
        $output .= "Status Code: {$statusCode2}\n";
        $output .= "Response: " . print_r($result2, true) . "\n";
        $output .= '</pre>';
    } catch (Exception $e) {
        $output .= '<pre style="color: red;">';
        $output .= "Exception: " . $e->getMessage() . "\n";
        $output .= '</pre>';
    }

    return $output;
});
