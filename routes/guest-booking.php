<?php

declare(strict_types=1);

use App\Http\Controllers\Customer\GuestBookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Booking Routes
|--------------------------------------------------------------------------
|
| These routes handle the guest booking flow for users who want to book
| a hall without creating an account. The flow includes:
|
| 1. Guest enters details (name, email, phone)
| 2. OTP sent to email for verification
| 3. Guest verifies OTP
| 4. Guest completes booking form
| 5. Guest proceeds to payment
| 6. Booking confirmed
| 7. Optional: Guest creates account
|
| Security:
| - OTP verification required before booking
| - Guest token for secure booking access
| - Rate limiting on sessions per email
|
| Include this file in routes/web.php:
| require __DIR__ . '/guest-booking.php';
|
*/

Route::prefix('guest')->name('guest.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest Booking Initiation
    |--------------------------------------------------------------------------
    | These routes handle the initial booking request and OTP verification.
    */

    // Show guest booking form (step 1: enter guest details)
    Route::get('/book/{hall:slug}', [GuestBookingController::class, 'create'])
        ->name('book');

    // Initiate guest booking and send OTP
    Route::post('/book/{hall:slug}/initiate', [GuestBookingController::class, 'initiate'])
        ->name('initiate');

    // Show OTP verification page
    Route::get('/book/{hall:slug}/verify', [GuestBookingController::class, 'showVerifyOtp'])
        ->name('verify-otp');

    // Verify OTP code
    Route::post('/book/{hall:slug}/verify', [GuestBookingController::class, 'verifyOtp'])
        ->name('verify-otp.submit');

    // Resend OTP code
    Route::post('/book/{hall:slug}/resend-otp', [GuestBookingController::class, 'resendOtp'])
        ->name('resend-otp');

    /*
    |--------------------------------------------------------------------------
    | Guest Booking Form
    |--------------------------------------------------------------------------
    | After OTP verification, guest can fill the booking details.
    */

    // Show booking form (step 2: select date, time, services)
    Route::get('/book/{hall:slug}/form', [GuestBookingController::class, 'showBookingForm'])
        ->name('booking-form');

    // Submit booking form
    Route::post('/book/{hall:slug}/store', [GuestBookingController::class, 'store'])
        ->name('booking.store');

    /*
    |--------------------------------------------------------------------------
    | Guest Booking Management (Token-based Access)
    |--------------------------------------------------------------------------
    | These routes allow guests to view and manage their bookings using
    | their unique guest_token instead of authentication.
    */

    // View booking details
    Route::get('/booking/{guest_token}', [GuestBookingController::class, 'show'])
        ->name('booking.show')
        ->where('guest_token', '[a-f0-9]{64}');

    // Show payment page
    Route::get('/booking/{guest_token}/payment', [GuestBookingController::class, 'payment'])
        ->name('booking.payment')
        ->where('guest_token', '[a-f0-9]{64}');

    // Process payment
    Route::post('/booking/{guest_token}/process-payment', [GuestBookingController::class, 'processPayment'])
        ->name('booking.process-payment')
        ->where('guest_token', '[a-f0-9]{64}');

    // Payment success callback (from Thawani)
    Route::get('/payment/success/{guest_token}', [GuestBookingController::class, 'paymentSuccess'])
        ->name('payment.success')
        ->where('guest_token', '[a-f0-9]{64}');

    // Payment cancel callback (from Thawani)
    Route::get('/payment/cancel/{guest_token}', [GuestBookingController::class, 'paymentCancel'])
        ->name('payment.cancel')
        ->where('guest_token', '[a-f0-9]{64}');

    // Booking success page
    Route::get('/booking/{guest_token}/success', [GuestBookingController::class, 'success'])
        ->name('booking.success')
        ->where('guest_token', '[a-f0-9]{64}');

    // Download booking PDF
    Route::get('/booking/{guest_token}/download', [GuestBookingController::class, 'downloadPdf'])
        ->name('booking.download')
        ->where('guest_token', '[a-f0-9]{64}');

    /*
    |--------------------------------------------------------------------------
    | Guest Account Creation
    |--------------------------------------------------------------------------
    | Optional route for guests to create an account after booking.
    | This links all their guest bookings to the new account.
    */

    Route::post('/booking/{guest_token}/create-account', [GuestBookingController::class, 'createAccount'])
        ->name('create-account')
        ->where('guest_token', '[a-f0-9]{64}');

    /*
    |--------------------------------------------------------------------------
    | AJAX Endpoints
    |--------------------------------------------------------------------------
    | Public AJAX endpoints for guest booking features.
    */

    // Check hall availability (public - no auth required)
    Route::post('/check-availability', [GuestBookingController::class, 'checkAvailability'])
        ->name('check-availability');


    // Guest Booking Payment Routes - Add these to your routes file:

    // Payment page (displays payment form) - should already exist
    // Route::get('/guest/booking/{guest_token}/payment', [GuestBookingController::class, 'showPayment'])
    //     ->name('guest.booking.payment');

    // // Process payment (form submission) - ADD THIS
    // Route::post('/guest/booking/{guest_token}/process-payment', [GuestBookingController::class, 'processPayment'])
    //     ->name('guest.booking.process-payment');

    // // Payment success callback - ADD THIS
    // Route::get('/guest/booking/{guest_token}/payment-success/{payment_reference}', [GuestBookingController::class, 'paymentSuccess'])
    //     ->name('guest.booking.payment-success');

    // // Payment cancel callback - ADD THIS
    // Route::get('/guest/booking/{guest_token}/payment-cancel/{payment_reference}', [GuestBookingController::class, 'paymentCancel'])
    //     ->name('guest.booking.payment-cancel');

    // // Confirmation page - should already exist
    // Route::get('/guest/booking/{guest_token}/confirmation', [GuestBookingController::class, 'confirmation'])
    //     ->name('guest.booking.confirmation');

});
