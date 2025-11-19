<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\HallController;
use App\Http\Controllers\Customer\BookingController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
| These routes handle the customer-facing pages for browsing halls,
| making bookings, and managing their account.
*/

// Public routes - Browse halls
Route::name('customer.')->group(function () {

    // NEW: Homepage - Hall listings with regions/cities/map
    Route::get('/halls', [HallController::class, 'index'])->name('halls.index');

    // NEW: Get cities by region (AJAX)
    Route::get('/halls/cities/{region}', [HallController::class, 'getCitiesByRegion'])->name('halls.cities-by-region');

    // NEW: Hall details
    Route::get('/halls/{slug}', [HallController::class, 'show'])->name('halls.show');

    // OLD routes - Commented out (using new HallController now)
    // Route::get('/halls', [CustomerController::class, 'index'])->name('halls.index');
    // Route::get('/halls/{hall:slug}', [CustomerController::class, 'show'])->name('halls.show');
});

// Booking Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/book/{hall:slug}', [BookingController::class, 'create'])->name('customer.book');
    Route::post('/book/{hall:slug}', [BookingController::class, 'store'])->name('customer.booking.store');
    Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('customer.check-availability');
    Route::get('/booking/success/{reference}', [BookingController::class, 'success'])->name('customer.booking.success');
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
