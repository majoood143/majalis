<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
| These routes handle the customer-facing pages for browsing halls,
| making bookings, and managing their account.
*/

// Public routes - Browse halls
Route::name('customer.')->group(function () {

    // Homepage - Hall listings
    Route::get('/halls', [CustomerController::class, 'index'])->name('halls.index');

    // Hall details
    Route::get('/halls/{hall:slug}', [CustomerController::class, 'show'])->name('halls.show');

});

// Protected routes - Requires authentication
Route::middleware(['auth'])->name('customer.')->group(function () {

    // Customer Dashboard
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');

    // Booking form
    Route::get('/halls/{hall:slug}/book', [CustomerController::class, 'book'])->name('book');

    // Bookings Management
    Route::get('/my-bookings', [CustomerController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [CustomerController::class, 'bookingDetails'])->name('booking.details');

    // Create booking
    Route::post('/halls/{hall}/book', [CustomerBookingController::class, 'store'])->name('booking.store');

    // Payment
    Route::get('/bookings/{booking}/payment', [CustomerBookingController::class, 'payment'])->name('booking.payment');
    Route::post('/bookings/{booking}/payment', [CustomerBookingController::class, 'processPayment'])->name('booking.payment.process');

    // Cancel booking
    Route::post('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('booking.cancel');

});

// AJAX routes
Route::middleware(['auth'])->group(function () {
    Route::post('/api/check-availability', [CustomerBookingController::class, 'checkAvailability'])->name('api.check-availability');
});
