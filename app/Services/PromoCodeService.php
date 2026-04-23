<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /**
     * Validate a promo code against a specific hall and subtotal.
     *
     * @return array{valid: bool, message: string, promo_code_id?: int, discount_amount?: float, discount_type?: string, discount_value?: float}
     */
    public function validate(string $code, int $hallId, float $subtotal): array
    {
        $promoCode = PromoCode::where('code', strtoupper(trim($code)))->first();

        if (!$promoCode) {
            return ['valid' => false, 'message' => __('promo.invalid_code')];
        }

        if (!$promoCode->is_active) {
            return ['valid' => false, 'message' => __('promo.code_inactive')];
        }

        if ($promoCode->valid_from && now()->lt($promoCode->valid_from)) {
            return ['valid' => false, 'message' => __('promo.code_not_started')];
        }

        if ($promoCode->valid_until && now()->gt($promoCode->valid_until)) {
            return ['valid' => false, 'message' => __('promo.code_expired')];
        }

        if ($promoCode->max_uses !== null && $promoCode->used_count >= $promoCode->max_uses) {
            return ['valid' => false, 'message' => __('promo.code_used_up')];
        }

        // Hall-scoped code: reject if it doesn't match the booked hall
        if ($promoCode->hall_id !== null && $promoCode->hall_id !== $hallId) {
            return ['valid' => false, 'message' => __('promo.invalid_code')];
        }

        $discountAmount = $promoCode->calculateDiscount($subtotal);

        return [
            'valid'           => true,
            'promo_code_id'   => $promoCode->id,
            'discount_amount' => $discountAmount,
            'discount_type'   => $promoCode->discount_type,
            'discount_value'  => (float) $promoCode->discount_value,
            'message'         => __('promo.code_applied', [
                'amount' => number_format($discountAmount, 3),
            ]),
        ];
    }

    /**
     * Apply a promo code to an existing booking.
     *
     * Updates total_amount, discount_amount, owner_payout, and promo_code_id on the booking.
     * The discount is absorbed by the owner (owner_payout is reduced accordingly).
     */
    public function applyToBooking(PromoCode $promoCode, Booking $booking): void
    {
        $subtotal        = (float) $booking->subtotal;
        $platformFee     = (float) ($booking->platform_fee ?? 0);
        $commissionAmount = (float) ($booking->commission_amount ?? 0);

        $discountAmount = $promoCode->calculateDiscount($subtotal);

        // Customer pays: subtotal + platform_fee - discount
        $newTotal      = max(0.0, $subtotal + $platformFee - $discountAmount);
        // Owner receives: subtotal - commission - discount
        $newOwnerPayout = max(0.0, $subtotal - $commissionAmount - $discountAmount);

        $booking->update([
            'promo_code_id'   => $promoCode->id,
            'discount_amount' => round($discountAmount, 2),
            'total_amount'    => round($newTotal, 2),
            'owner_payout'    => round($newOwnerPayout, 2),
        ]);
    }

    /**
     * Record the usage of a promo code after a successful payment.
     *
     * Increments used_count atomically inside a transaction.
     */
    public function recordUsage(PromoCode $promoCode, Booking $booking): void
    {
        DB::transaction(function () use ($promoCode, $booking) {
            // Guard: don't double-count if already recorded
            $alreadyRecorded = PromoCodeUsage::where('promo_code_id', $promoCode->id)
                ->where('booking_id', $booking->id)
                ->exists();

            if ($alreadyRecorded) {
                return;
            }

            PromoCodeUsage::create([
                'promo_code_id'   => $promoCode->id,
                'booking_id'      => $booking->id,
                'guest_session_id' => null,
                'discount_amount' => (float) ($booking->discount_amount ?? 0),
                'used_at'         => now(),
            ]);

            $promoCode->increment('used_count');
        });
    }
}
