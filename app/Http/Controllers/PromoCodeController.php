<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PromoCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{
    public function __construct(private readonly PromoCodeService $promoCodeService)
    {
    }

    /**
     * Validate a promo code for a given booking (AJAX endpoint).
     *
     * Accepts: { code: string, booking_id: int }
     * Returns: { valid: bool, discount_amount: float, message: string, ... }
     */
    public function validate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'       => ['required', 'string', 'max:50'],
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid'   => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $booking = Booking::find($request->input('booking_id'));

        if (!$booking) {
            return response()->json(['valid' => false, 'message' => __('promo.invalid_code')], 404);
        }

        $result = $this->promoCodeService->validate(
            (string) $request->input('code'),
            (int) $booking->hall_id,
            (float) $booking->subtotal
        );

        return response()->json($result);
    }
}
