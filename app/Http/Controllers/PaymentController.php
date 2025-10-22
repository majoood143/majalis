<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function success(Booking $booking, Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $data = $this->paymentService->verifyPayment($sessionId);

            if ($data['payment_status'] === 'paid') {
                return view('booking.payment-success', compact('booking'));
            }
        }

        return redirect()->route('booking.show', $booking)
            ->with('error', 'Payment verification failed');
    }

    public function cancel(Booking $booking)
    {
        return view('booking.payment-cancelled', compact('booking'));
    }

    public function webhook(Request $request)
    {
        try {
            $this->paymentService->handleWebhook($request->all());
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
