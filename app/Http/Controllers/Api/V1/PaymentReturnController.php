<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

/**
 * Where Thawani sends the in-app browser after checkout (success_url / cancel_url
 * of payments started from the app).
 *
 * Public on purpose: the browser has no token. It never trusts the URL — the
 * result comes from PaymentService::handlePaymentSuccess(), which asks Thawani
 * and checks the session belongs to this payment. Then it redirects to the app's
 * deep link, which closes the browser; the app re-reads the booking from the API.
 */
class PaymentReturnController extends Controller
{
    public function __invoke(string $reference, string $result, PaymentService $paymentService): RedirectResponse
    {
        $payment = Payment::with('booking.hall')->where('payment_reference', $reference)->first();
        $booking = $payment?->booking;
        $status = 'failed';

        if ($booking) {
            if ($result === 'cancel') {
                $status = 'cancelled';
            } elseif ($payment->status === Payment::STATUS_PAID) {
                $status = 'paid';
            } elseif ($payment->transaction_id) {
                $outcome = $paymentService->handlePaymentSuccess($payment->transaction_id, $booking);
                $status = ($outcome['success'] ?? false) ? 'paid' : 'failed';
            }
        }

        $query = http_build_query(array_filter([
            'booking' => $booking?->booking_number,
            'status' => $status,
        ]));

        return redirect()->away(config('majalis.app_scheme', 'majalis').'://payment/result?'.$query);
    }
}
