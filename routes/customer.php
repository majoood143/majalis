<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\HallController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
| These routes handle the customer-facing pages for browsing halls,
| making bookings, and managing their account.
|
| Authentication: Uses Laravel Breeze (web guard)
| Session handling: Redirects to /login on expiration
*/

// Public routes - No authentication required
Route::name('customer.')->group(function () {
    // Hall browsing (public)
    Route::get('/halls', [HallController::class, 'index'])->name('halls.index');
    Route::get('/halls/cities/{region}', [HallController::class, 'getCitiesByRegion'])->name('halls.cities-by-region');
    Route::get('/halls/{slug}', [HallController::class, 'show'])->name('halls.show');
});

// Protected routes - Requires authentication
Route::middleware(['auth:web'])->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Booking Routes
    Route::get('/book/{hall:slug}', [BookingController::class, 'create'])->name('book');
    Route::post('/book/{hall:slug}', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');

    // My Bookings
    Route::get('/my-bookings', [CustomerController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [CustomerController::class, 'bookingDetails'])->name('booking.details');

    // Booking Management
    Route::get('/booking/success/{bookingNumber}', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/booking/cancelled/{booking}', [BookingController::class, 'cancelled'])->name('booking.cancelled');
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('booking.cancel');

    // Payment Routes
    Route::get('/booking/{booking}/payment', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/{booking}/process-payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
    Route::get('/booking/{booking}/retry-payment', [BookingController::class, 'retryPayment'])->name('booking.retry-payment');
    Route::get('/payment/success/{booking}', [BookingController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel/{booking}', [BookingController::class, 'paymentCancel'])->name('payment.cancel');

    // PDF Downloads
    Route::get('/booking/{booking}/download-pdf', [BookingController::class, 'downloadPdf'])->name('booking.download-pdf');
});

// Static Pages Routes
// These routes support both English and Arabic with automatic locale detection
Route::prefix('{locale?}')->where(['locale' => 'en|ar'])->group(function () {

    // About Us Page
    Route::get('/about-us', [PageController::class, 'aboutUs'])
        ->name('pages.about-us');

    // Contact Us Page
    Route::get('/contact-us', [PageController::class, 'contactUs'])
        ->name('pages.contact-us');

    // Terms and Conditions Page
    Route::get('/terms-and-conditions', [PageController::class, 'terms'])
        ->name('pages.terms');

    // Privacy Policy Page
    Route::get('/privacy-policy', [PageController::class, 'privacy'])
        ->name('pages.privacy');

    // Dynamic Page Route (for any custom pages)
    Route::get('/page/{slug}', [PageController::class, 'show'])
        ->name('pages.show');
});
