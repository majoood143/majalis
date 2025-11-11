<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


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
    return view('auth.register'); // You'll need to create this
})->name('register');

Route::post('/logout', function () {
    //Auth::logout();
    //auth()->logout();
    return redirect('/');
})->name('logout');

Route::get('/test-pdf/{booking}', function (Booking $booking) {
    try {
        // Test 1: Simple PDF
        $pdf = Pdf::loadHTML('<h1>Test PDF</h1><p>Booking: ' . $booking->booking_number . '</p>');
        return $pdf->stream();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . '<br><br>Trace: ' . $e->getTraceAsString();
    }
});

Route::get('/test-storage', function () {
    try {
        $testContent = 'Test file created at ' . now();

        // Test write
        Storage::disk('local')->put('test.txt', $testContent);

        // Check if exists
        $exists = Storage::disk('local')->exists('test.txt');

        // Get path
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

require __DIR__.'/auth.php';


// Customer Routes - MUST BE LAST
require __DIR__ . '/customer.php';
