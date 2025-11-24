<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Customer\BookingController;

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

// Add these authentication routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/logout', function () {
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
