<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Handlers;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Admin\Resources\Payouts\Api\Requests\CreatePayoutRequest;
use App\Filament\Admin\Resources\Payouts\Api\Transformers\PayoutTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Models\Booking;

/**
 * Create a Payout record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'Create:OwnerPayout';

    protected static string $transformer = PayoutTransformer::class;

    /**
     * Create Payout
     */
    public function handler(CreatePayoutRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        $data['status'] ??= 'pending';

        return static::withTotals($data);
    }

    /**
     * Totals from paid, confirmed/completed bookings of the owner's halls in the period
     * (same calculation as the panel's Create Payout page).
     */
    public static function withTotals(array $data): array
    {
        $bookings = Booking::query()
            ->whereHas('hall', fn ($query) => $query->where('owner_id', $data['owner_id']))
            ->whereBetween('booking_date', [$data['period_start'], $data['period_end']])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        $data['bookings_count'] = $bookings->count();
        $data['gross_revenue'] = (float) $bookings->sum('total_amount');
        $data['commission_amount'] = (float) $bookings->sum('commission_amount');
        $data['net_payout'] = (float) $bookings->sum('owner_payout') + (float) ($data['adjustments'] ?? 0);
        $data['commission_rate'] = $data['gross_revenue'] > 0
            ? ($data['commission_amount'] / $data['gross_revenue']) * 100
            : 0;

        return $data;
    }
}
