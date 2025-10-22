<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('booking/{booking}/payment')->group(function () {
    Route::get('success', [PaymentController::class, 'success'])->name('booking.payment.success');
    Route::get('cancel', [PaymentController::class, 'cancel'])->name('booking.payment.cancel');
});

// Webhook for Thawani
Route::post('webhooks/thawani', [PaymentController::class, 'webhook'])->name('webhooks.thawani');
