<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CommissionSetting;
use App\Models\Hall;
use Illuminate\Support\Facades\Log;

/**
 * Service for resolving and calculating platform commissions.
 *
 * Commission resolution priority (highest to lowest):
 *   1. Hall-specific commission setting
 *   2. Owner-specific commission setting
 *   3. Global (platform-wide) commission setting
 *
 * The resolved commission is applied as:
 *   - `platform_fee`       → added to customer's total (customer pays this)
 *   - `commission_amount`   → deducted from owner payout (owner pays this)
 *   - Both fields store the SAME value — the commission IS the platform fee
 *
 * @package App\Services
 * @version 1.0.0
 */
class CommissionService
{
    /**
     * Resolve the applicable commission setting for a given hall.
     *
     * Follows priority: Hall-specific > Owner-specific > Global.
     * Only returns active settings that are currently effective.
     *
     * @param Hall $hall The hall to resolve commission for
     * @return CommissionSetting|null The applicable setting, or null if none found
     */
    public function resolveForHall(Hall $hall): ?CommissionSetting
    {
        $today = now()->toDateString();

        // Priority 1: Hall-specific commission
        $hallCommission = CommissionSetting::query()
            ->active()
            ->forHall($hall->id)
            ->effectiveOn($today)
            ->orderByDesc('created_at')
            ->first();

        if ($hallCommission) {
            Log::debug('Commission resolved: Hall-specific', [
                'hall_id'          => $hall->id,
                'commission_id'    => $hallCommission->id,
                'type'             => $hallCommission->commission_type,
                'value'            => $hallCommission->commission_value,
            ]);

            return $hallCommission;
        }

        // Priority 2: Owner-specific commission
        if ($hall->owner_id) {
            $ownerCommission = CommissionSetting::query()
                ->active()
                ->forOwner($hall->owner_id)
                ->whereNull('hall_id') // Ensure it's owner-level, not hall-level
                ->effectiveOn($today)
                ->orderByDesc('created_at')
                ->first();

            if ($ownerCommission) {
                Log::debug('Commission resolved: Owner-specific', [
                    'hall_id'          => $hall->id,
                    'owner_id'         => $hall->owner_id,
                    'commission_id'    => $ownerCommission->id,
                    'type'             => $ownerCommission->commission_type,
                    'value'            => $ownerCommission->commission_value,
                ]);

                return $ownerCommission;
            }
        }

        // Priority 3: Global commission
        $globalCommission = CommissionSetting::query()
            ->active()
            ->global()
            ->effectiveOn($today)
            ->orderByDesc('created_at')
            ->first();

        if ($globalCommission) {
            Log::debug('Commission resolved: Global', [
                'hall_id'          => $hall->id,
                'commission_id'    => $globalCommission->id,
                'type'             => $globalCommission->commission_type,
                'value'            => $globalCommission->commission_value,
            ]);
        } else {
            Log::warning('No active commission setting found', [
                'hall_id'  => $hall->id,
                'owner_id' => $hall->owner_id,
            ]);
        }

        return $globalCommission;
    }

    /**
     * Calculate the commission/platform fee amount for a given subtotal.
     *
     * @param Hall  $hall     The hall being booked
     * @param float $subtotal The subtotal (hall_price + services_price) before fee
     * @return array{
     *     platform_fee: float,
     *     commission_amount: float,
     *     commission_type: string|null,
     *     commission_value: float|null,
     *     total_amount: float,
     *     owner_payout: float
     * }
     */
    public function calculateFees(Hall $hall, float $subtotal): array
    {
        $commissionSetting = $this->resolveForHall($hall);

        // Default: no commission found → 0 fee
        if (!$commissionSetting) {
            return [
                'platform_fee'      => 0.00,
                'commission_amount'  => 0.00,
                'commission_type'    => null,
                'commission_value'   => null,
                'total_amount'       => $subtotal,
                'owner_payout'       => $subtotal,
            ];
        }

        // Calculate the fee based on type (percentage or fixed)
        $commissionType  = $commissionSetting->commission_type->value ?? (string) $commissionSetting->commission_type;
        $commissionValue = (float) $commissionSetting->commission_value;
        $feeAmount       = 0.00;

        if ($commissionType === 'percentage') {
            // Percentage of subtotal (e.g., 10% of 100 OMR = 10 OMR)
            $feeAmount = ($subtotal * $commissionValue) / 100;
        } elseif ($commissionType === 'fixed') {
            // Fixed amount (e.g., 5 OMR flat fee)
            $feeAmount = $commissionValue;
        }

        // Round to 3 decimal places (Omani Rial uses 3 decimals)
        $feeAmount = round($feeAmount, 3);

        // Total = subtotal + platform fee (customer pays the fee)
        $totalAmount = round($subtotal + $feeAmount, 3);

        // Owner payout = total - commission (commission = platform fee)
        $ownerPayout = round($totalAmount - $feeAmount, 3);

        Log::info('Commission calculated', [
            'hall_id'           => $hall->id,
            'subtotal'          => $subtotal,
            'commission_type'   => $commissionType,
            'commission_value'  => $commissionValue,
            'fee_amount'        => $feeAmount,
            'total_amount'      => $totalAmount,
            'owner_payout'      => $ownerPayout,
        ]);

        return [
            'platform_fee'      => $feeAmount,
            'commission_amount'  => $feeAmount,
            'commission_type'    => $commissionType,
            'commission_value'   => $commissionValue,
            'total_amount'       => $totalAmount,
            'owner_payout'       => $ownerPayout,
        ];
    }
}
