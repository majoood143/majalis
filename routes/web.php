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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;

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

// Hall Owner Registration (public wizard)
Route::get('/register-as-hall-owner', function () {
    return view('hall-owner.register');
})->middleware('guest')->name('hall-owner.register');

Route::get('/register-as-hall-owner/success', function () {
    return view('hall-owner.success');
})->name('hall-owner.register.success');

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

// Public payment callback routes — no auth required (for admin-sent payment links)
Route::get('/payment/callback/{booking}', [PaymentController::class, 'success'])->name('payment.callback.success');
Route::get('/payment/callback/{booking}/cancel', [PaymentController::class, 'cancel'])->name('payment.callback.cancel');

// ── Review submission (public, token-validated, no auth required) ──────────
// Named 'booking.review' so legacy email views using route('booking.review', $id) continue to work.
Route::get('/reviews/submit',  [ReviewController::class, 'show'])
    ->name('reviews.submit');
Route::get('/booking/{booking}/review', function (\App\Models\Booking $booking) {
    // Redirect to tokenised URL so the completed-email CTA works without a token param
    $token = hash('sha256', implode('|', [
        $booking->id,
        $booking->booking_number,
        $booking->customer_email,
        config('app.key'),
    ]));
    return redirect()->route('reviews.submit', ['booking' => $booking->id, 'token' => $token]);
})->name('booking.review');
Route::post('/reviews/submit', [ReviewController::class, 'store'])->name('reviews.store');

require __DIR__ . '/auth.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/guest-booking.php';

// Catch-all: resolve any slug as a dynamic CMS page (must be last)
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('pages.dynamic');
