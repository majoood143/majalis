<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Models\OwnerPayout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\HallController;
use App\Http\Controllers\PageController;

// Home page — Hall listings (served at / without redirect)
Route::get('/', [HallController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

/**
 * Customer Booking PDF Routes
 */
Route::prefix('customer/bookings')->name('customer.bookings.')->group(function () {
    Route::get('{token}/download-pdf', [BookingController::class, 'downloadConfirmation'])
        ->name('download-confirmation');

    Route::get('{token}/preview-pdf', [BookingController::class, 'previewConfirmation'])
        ->name('preview-confirmation');
});

// Admin Payout Receipt Download/Print Route
Route::get('/admin/payout/{payout}/receipt', function ($payout) {
    if (!$payout instanceof OwnerPayout) {
        $payout = OwnerPayout::withTrashed()->find($payout);
    }

    if (!$payout) {
        Log::error('Payout not found', ['payout_id' => request()->route('payout')]);
        abort(404, 'Payout not found.');
    }

    if (empty($payout->receipt_path)) {
        Log::warning('Receipt path is empty', ['payout_id' => $payout->id]);
        abort(404, 'Receipt has not been generated yet.');
    }

    if (!Storage::disk('public')->exists($payout->receipt_path)) {
        Log::warning('Receipt file not found in storage', [
            'payout_id' => $payout->id,
            'path' => $payout->receipt_path,
        ]);
        abort(404, 'Receipt file not found in storage.');
    }

    return Storage::disk('public')->download(
        $payout->receipt_path,
        "payout-receipt-{$payout->payout_number}.pdf"
    );
})
    ->name('admin.payout.receipt')
    ->middleware(['web', 'auth']);

// Print invoice
Route::get('/bookings/{booking}/invoice/print', function (Booking $booking) {
    abort_unless(
        auth()->user()?->can('view', $booking) ||
            auth('filament')->check(),
        403
    );

    return view('invoices.print', ['booking' => $booking->load(['hall', 'extraServices', 'user'])]);
})->name('bookings.invoice.print')->middleware(['auth:web,filament']);

// Static Pages
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('pages.about-us');
Route::get('/contact-us', [PageController::class, 'contactUs'])->name('pages.contact-us');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

// Hall availability API (public — no auth required)
Route::get('/api/halls/check-availability', [HallController::class, 'checkDateAvailability'])
    ->name('api.halls.check-availability');

Route::get('/api/halls/suggest-dates', [HallController::class, 'suggestDates'])
    ->name('api.halls.suggest-dates');

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__ . '/customer-tickets.php';
});

require __DIR__ . '/auth.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/guest-booking.php';
