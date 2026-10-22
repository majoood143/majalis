<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\HallController;
use App\Http\Controllers\Api\V1\PaymentReturnController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Customer mobile API (v1)
|--------------------------------------------------------------------------
| Business flows for the customer app (see MOBILE_APP.md). The admin and
| owner apps use the panel APIs under /api/admin and /api/owner instead.
*/
Route::prefix('v1')->name('api.v1.')->middleware(['api.locale', 'throttle:api-v1'])->group(function () {
    // Public catalogue
    Route::get('config', [CatalogController::class, 'config'])->name('config');
    Route::get('regions', [CatalogController::class, 'regions'])->name('regions');
    Route::get('cities', [CatalogController::class, 'cities'])->name('cities');
    Route::get('hall-types', [CatalogController::class, 'hallTypes'])->name('hall-types');
    Route::get('hall-features', [CatalogController::class, 'hallFeatures'])->name('hall-features');
    Route::get('event-types', [CatalogController::class, 'eventTypes'])->name('event-types');

    Route::get('halls', [HallController::class, 'index'])->name('halls.index');
    Route::get('halls/{slug}', [HallController::class, 'show'])->name('halls.show');
    Route::get('halls/{slug}/availability', [HallController::class, 'availability'])
        ->middleware('throttle:api-v1-availability')
        ->name('halls.availability');

    // Authentication (per-device Sanctum tokens)
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:api-v1-auth')->name('register');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:api-v1-auth')->name('login');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware('throttle:api-v1-password-reset')
            ->name('forgot-password');
    });

    Route::middleware(['auth:sanctum', 'api.active'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('me', [ProfileController::class, 'show'])->name('me.show');
        Route::patch('me', [ProfileController::class, 'update'])->name('me.update');
        Route::put('me/password', [ProfileController::class, 'updatePassword'])
            ->middleware('throttle:api-v1-sensitive')
            ->name('me.password');
        Route::delete('me', [ProfileController::class, 'destroy'])->middleware('throttle:api-v1-sensitive')->name('me.destroy');

        Route::post('devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::delete('devices/{token}', [DeviceController::class, 'destroy'])
            ->where('token', '.+')
            ->name('devices.destroy');

        // Bookings & payment (phase 3)
        Route::post('bookings/quote', [BookingController::class, 'quote'])->name('bookings.quote');
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('bookings', [BookingController::class, 'store'])
            ->middleware('throttle:api-v1-booking')
            ->name('bookings.store');
        Route::get('bookings/{bookingNumber}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('bookings/{bookingNumber}/pay', [BookingController::class, 'pay'])
            ->middleware('throttle:api-v1-booking')
            ->name('bookings.pay');
        Route::post('bookings/{bookingNumber}/cancel', [BookingController::class, 'cancel'])
            ->middleware('throttle:api-v1-sensitive')
            ->name('bookings.cancel');
        Route::get('bookings/{bookingNumber}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');

        // Saved halls
        Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::put('halls/{slug}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('halls/{slug}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    });

    // Thawani sends the in-app browser here after checkout (no token in a browser).
    Route::get('payments/thawani/{reference}/{result}', PaymentReturnController::class)
        ->where('result', 'success|cancel')
        ->name('payments.return');
});
