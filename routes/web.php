<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Models\OwnerPayout;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// // Add these authentication routes
// Route::get('/login', function () {
//     return redirect('/admin/login');
// })->name('login');

// Route::get('/register', function () {
//     return view('auth.register');
// })->name('register');

// Route::post('/logout', function () {
//     return redirect('/');
// })->name('logout');

/**
 * Customer Authentication Routes
 *
 * Laravel Breeze handles customer authentication
 */
Route::middleware('guest')->group(function () {
    // Customer login page
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Customer registration page
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

// Logout route (for all user types)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Test routes
Route::get('/test-pdf/{booking}', function (Booking $booking) {
    try {
        $pdf = Pdf::loadHTML('<h1>Test PDF</h1><p>Booking: ' . $booking->booking_number . '</p>');
        return $pdf->stream();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . '<br><br>Trace: ' . $e->getTraceAsString();
    }
});

Route::get('/test-storage', function () {
    try {
        $testContent = 'Test file created at ' . now();
        Storage::disk('local')->put('test.txt', $testContent);
        $exists = Storage::disk('local')->exists('test.txt');
        $path = Storage::disk('local')->path('test.txt');

        return response()->json([
            'write_successful' => true,
            'file_exists' => $exists,
            'full_path' => $path,
            'file_readable' => file_exists($path),
            'content' => Storage::disk('local')->get('test.txt')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/test-view/{booking}', function (Booking $booking) {
    try {
        $booking->load(['hall.owner', 'extraServices']);

        return view('pdf.invoice', [
            'booking' => $booking,
            'hall' => $booking->hall,
            'owner' => $booking->hall->owner,
            'extraServices' => $booking->extraServices,
        ]);
    } catch (\Exception $e) {
        return 'View Error: ' . $e->getMessage() . '<br><br>' . $e->getTraceAsString();
    }
});

// ============================================================
// STATIC PAGES ROUTES - ADD THIS SECTION
// ============================================================

// About Us Page
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('pages.about-us');

// Contact Us Page
Route::get('/contact-us', [PageController::class, 'contactUs'])->name('pages.contact-us');

// Terms and Conditions Page
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('pages.terms');

// Privacy Policy Page
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');

// Dynamic Page Route (for any custom pages)
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

// ============================================================
// END STATIC PAGES ROUTES
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__ . '/customer-tickets.php';
});

require __DIR__ . '/auth.php';

// Customer Routes - This now includes the new HallController routes
require __DIR__ . '/customer.php';

// REMOVE THESE DUPLICATE ROUTES - They're now in customer.php
// Route::prefix('halls')->name('halls.')->group(function () {
//     Route::get('/', [HallController::class, 'index'])->name('index');
//     Route::get('/cities/{region}', [HallController::class, 'getCitiesByRegion'])->name('cities-by-region');
//     Route::get('/{slug}', [HallController::class, 'show'])->name('show');
// });

/**
 * Customer Booking PDF Routes
 */
Route::prefix('customer/bookings')->name('customer.bookings.')->group(function () {
    // Download PDF confirmation
    Route::get('{token}/download-pdf', [BookingController::class, 'downloadConfirmation'])
        ->name('download-confirmation');

    // Preview PDF confirmation in browser
    Route::get('{token}/preview-pdf', [BookingController::class, 'previewConfirmation'])
        ->name('preview-confirmation');
});

require __DIR__ . '/test-thawani.php';

// In routes/web.php
Route::get('/direct-thawani-test', function () {
    $apiKey = 'rRQ26GcsZzoEhbrP2HZvLYDbn9C9et';

    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://uatcheckout.thawani.om/api/v1',
        'timeout' => 30,
        'http_errors' => false,
        'verify' => true,
    ]);

    $payload = [
        'client_reference_id' => 'TEST-' . time(),
        'mode' => 'payment',
        'products' => [
            [
                'name' => 'Test Product',
                'quantity' => 1,
                'unit_amount' => 1000,
            ]
        ],
        'success_url' => 'http://localhost:8000/test-success',
        'cancel_url' => 'http://localhost:8000/test-cancel',
    ];

    $jsonPayload = json_encode($payload);

    try {
        $response = $client->post('/checkout/session', [
            'headers' => [
                'thawani-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => $jsonPayload,
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $headers = $response->getHeaders();

        return response()->json([
            'status_code' => $statusCode,
            'headers' => $headers,
            'body' => $body,
            'decoded' => json_decode($body, true),
            'payload_sent' => $payload,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'class' => get_class($e),
        ], 500);
    }
});

Route::get('/check-keys', function () {
    $secret = config('services.thawani.secret_key');
    $pub = config('services.thawani.publishable_key');

    return response()->json([
        'secret_key' => $secret,
        'secret_length' => strlen($secret),
        'publishable_key' => $pub,
        'pub_length' => strlen($pub),
        'keys_are_different' => $secret !== $pub,
        'secret_hex' => bin2hex($secret),
        'pub_hex' => bin2hex($pub),
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::get('/test-native-curl', function () {
    $apiKey = 'rRQ26GcsZzoEhbrP2HZvLYDbn9C9et';
    $baseUrl = 'https://uatcheckout.thawani.om/api/v1';

    $payload = [
        'client_reference_id' => 'TEST-' . time(),
        'mode' => 'payment',
        'products' => [
            [
                'name' => 'Test Product',
                'quantity' => 1,
                'unit_amount' => 1000,
            ]
        ],
        'success_url' => 'http://localhost:8000/test-success',
        'cancel_url' => 'http://localhost:8000/test-cancel',
    ];

    $jsonPayload = json_encode($payload);

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/checkout/session',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonPayload,
        CURLOPT_HTTPHEADER => [
            'thawani-api-key: ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    return response()->json([
        'status_code' => $statusCode,
        'curl_error' => $curlError ?: null,
        'response' => $response,
        'decoded' => json_decode($response, true),
        'curl_info' => $curlInfo,
    ], 200, [], JSON_PRETTY_PRINT);
});

// Admin Payout Receipt Download/Print Route
Route::get('/admin/payout/{payout}/receipt', function ($payout) {
    // Handle both model instance and ID
    if (!$payout instanceof OwnerPayout) {
        $payout = OwnerPayout::withTrashed()->find($payout);
    }

    // Check if payout exists
    if (!$payout) {
        Log::error('Payout not found', ['payout_id' => request()->route('payout')]);
        abort(404, 'Payout not found.');
    }

    // Check if receipt path exists
    if (empty($payout->receipt_path)) {
        Log::warning('Receipt path is empty', ['payout_id' => $payout->id]);
        abort(404, 'Receipt has not been generated yet.');
    }

    // Check if file exists in storage
    if (!Storage::disk('public')->exists($payout->receipt_path)) {
        Log::warning('Receipt file not found in storage', [
            'payout_id' => $payout->id,
            'path' => $payout->receipt_path,
        ]);
        abort(404, 'Receipt file not found in storage.');
    }

    // Return file download
    return Storage::disk('public')->download(
        $payout->receipt_path,
        "payout-receipt-{$payout->payout_number}.pdf"
    );
})
    ->name('admin.payout.receipt')
    ->middleware(['web', 'auth']);

Route::get('/diagnose-payment', function () {
    $diagnostics = [];

    // ============================================
    // 1. CHECK ENVIRONMENT
    // ============================================
    $diagnostics['environment'] = [
        'APP_ENV' => config('app.env'),
        'APP_DEBUG' => config('app.debug'),
        'APP_URL' => config('app.url'),
        'IS_LOCALHOST' => str_contains(config('app.url'), 'localhost') || str_contains(config('app.url'), '127.0.0.1'),
    ];

    // ============================================
    // 2. CHECK THAWANI CREDENTIALS
    // ============================================
    $diagnostics['thawani'] = [
        'secret_key' => config('services.thawani.secret_key'),
        'secret_key_length' => strlen(config('services.thawani.secret_key')),
        'secret_key_valid' => strlen(config('services.thawani.secret_key')) > 20,
        'publishable_key' => config('services.thawani.publishable_key'),
        'publishable_key_length' => strlen(config('services.thawani.publishable_key')),
        'publishable_key_valid' => strlen(config('services.thawani.publishable_key')) > 20,
        'base_url' => config('services.thawani.base_url'),
        'keys_different' => config('services.thawani.secret_key') !== config('services.thawani.publishable_key'),
    ];

    // ============================================
    // 3. CHECK ROUTES
    // ============================================
    $testBookingId = 1;

    try {
        $successUrl = route('customer.payment.success', ['booking' => $testBookingId]);
        $cancelUrl = route('customer.payment.cancel', ['booking' => $testBookingId]);

        $diagnostics['routes'] = [
            'success_route_exists' => true,
            'cancel_route_exists' => true,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'success_url_has_localhost' => str_contains($successUrl, 'localhost') || str_contains($successUrl, '127.0.0.1'),
            'cancel_url_has_localhost' => str_contains($cancelUrl, 'localhost') || str_contains($cancelUrl, '127.0.0.1'),
            'urls_are_https' => str_starts_with($successUrl, 'https://') && str_starts_with($cancelUrl, 'https://'),
        ];
    } catch (\Exception $e) {
        $diagnostics['routes'] = [
            'error' => $e->getMessage(),
        ];
    }

    // ============================================
    // 4. CHECK BOOKING WITH ADVANCE PAYMENT
    // ============================================
    try {
        $booking = \App\Models\Booking::whereNotNull('advance_amount')
            ->where('advance_amount', '>', 0)
            ->first();

        if ($booking) {
            $diagnostics['test_booking'] = [
                'found' => true,
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'total_amount' => $booking->total_amount,
                'payment_type' => $booking->payment_type,
                'advance_amount' => $booking->advance_amount,
                'balance_due' => $booking->balance_due,
                'is_advance_payment' => $booking->isAdvancePayment(),
            ];
        } else {
            $diagnostics['test_booking'] = [
                'found' => false,
                'message' => 'No booking with advance payment found',
            ];
        }
    } catch (\Exception $e) {
        $diagnostics['test_booking'] = [
            'error' => $e->getMessage(),
        ];
    }

    // ============================================
    // 5. TEST PAYMENT SERVICE
    // ============================================
    try {
        $paymentService = app(\App\Services\PaymentService::class);
        $diagnostics['payment_service'] = [
            'instantiated' => true,
            'class' => get_class($paymentService),
        ];
    } catch (\Exception $e) {
        $diagnostics['payment_service'] = [
            'error' => $e->getMessage(),
        ];
    }

    // ============================================
    // 6. CHECK RECENT PAYMENT ERRORS
    // ============================================
    try {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $recentErrors = [];

            foreach (array_reverse($lines) as $line) {
                if (str_contains($line, 'Thawani') && str_contains($line, 'ERROR')) {
                    $recentErrors[] = $line;
                    if (count($recentErrors) >= 5) break;
                }
            }

            $diagnostics['recent_errors'] = [
                'found' => count($recentErrors),
                'errors' => $recentErrors,
            ];
        }
    } catch (\Exception $e) {
        $diagnostics['recent_errors'] = [
            'error' => $e->getMessage(),
        ];
    }

    // ============================================
    // 7. THAWANI API TEST
    // ============================================
    try {
        $apiKey = config('services.thawani.secret_key');
        $baseUrl = config('services.thawani.base_url');

        $testPayload = [
            'client_reference_id' => 'DIAGNOSTIC-TEST-' . time(),
            'mode' => 'payment',
            'products' => [
                [
                    'name' => 'Diagnostic Test',
                    'quantity' => 1,
                    'unit_amount' => 1000,
                ]
            ],
            'success_url' => route('customer.payment.success', ['booking' => 1]),
            'cancel_url' => route('customer.payment.cancel', ['booking' => 1]),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $baseUrl . '/checkout/session',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($testPayload),
            CURLOPT_HTTPHEADER => [
                'thawani-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        $diagnostics['thawani_test'] = [
            'status_code' => $statusCode,
            'success' => $statusCode === 200 && isset($responseData['success']) && $responseData['success'],
            'response' => $responseData,
            'request_payload' => $testPayload,
        ];
    } catch (\Exception $e) {
        $diagnostics['thawani_test'] = [
            'error' => $e->getMessage(),
        ];
    }

    // ============================================
    // 8. DETERMINE ISSUES
    // ============================================
    $issues = [];
    $recommendations = [];

    if ($diagnostics['environment']['IS_LOCALHOST']) {
        $issues[] = '❌ CRITICAL: APP_URL is set to localhost - Thawani cannot access localhost URLs';
        $recommendations[] = '✅ FIX: Use ngrok (ngrok http 8000) and set APP_URL to ngrok URL';
    }

    if (!$diagnostics['thawani']['secret_key_valid']) {
        $issues[] = '❌ CRITICAL: Thawani secret key appears invalid';
        $recommendations[] = '✅ FIX: Check THAWANI_API_KEY in .env file';
    }

    if (!$diagnostics['thawani']['publishable_key_valid']) {
        $issues[] = '❌ CRITICAL: Thawani publishable key appears invalid';
        $recommendations[] = '✅ FIX: Check THAWANI_PUBLISHABLE_KEY in .env file';
    }

    if (!$diagnostics['thawani']['keys_different']) {
        $issues[] = '❌ WARNING: Secret and publishable keys are the same';
        $recommendations[] = '✅ FIX: Verify you have two different keys from Thawani';
    }

    if (isset($diagnostics['routes']['success_url_has_localhost']) && $diagnostics['routes']['success_url_has_localhost']) {
        $issues[] = '❌ CRITICAL: Callback URLs contain localhost';
        $recommendations[] = '✅ FIX: Update APP_URL in .env and clear config';
    }

    if (isset($diagnostics['routes']['urls_are_https']) && !$diagnostics['routes']['urls_are_https']) {
        $issues[] = '⚠️ WARNING: Callback URLs are not HTTPS (required for production)';
        $recommendations[] = '✅ FIX: Use HTTPS for production environment';
    }

    if (isset($diagnostics['thawani_test']['status_code']) && $diagnostics['thawani_test']['status_code'] === 400) {
        $issues[] = '❌ CRITICAL: Thawani API returns 400 - Request rejected';
        $errorMsg = $diagnostics['thawani_test']['response']['description'] ?? 'Unknown error';
        $recommendations[] = '✅ ERROR: ' . $errorMsg;
    }

    $diagnostics['summary'] = [
        'issues_found' => count($issues),
        'issues' => $issues,
        'recommendations' => $recommendations,
        'ready_for_payment' => count($issues) === 0,
    ];

    // ============================================
    // RENDER RESULTS
    // ============================================
    return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
})->name('diagnose.payment');





