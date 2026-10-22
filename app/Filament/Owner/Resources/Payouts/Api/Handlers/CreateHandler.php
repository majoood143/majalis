<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Payouts\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\PayoutResource;
use App\Filament\Owner\Resources\Payouts\Api\Requests\CreatePayoutRequest;
use App\Filament\Owner\Resources\Payouts\Api\Transformers\PayoutTransformer;
use App\Models\Booking;
use App\Models\OwnerPayout;

/**
 * Create a Payout record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'Create:OwnerPayout';

    protected static bool $checkPermission = false;

    protected static string $transformer = PayoutTransformer::class;

    /**
     * Create Payout
     */
    public function handler(CreatePayoutRequest $request)
    {
        return $this->store($request);
    }

    /**
     * Payout request: totals are calculated on the server, never taken from the request.
     */
    protected function beforeCreate(array $data): array
    {
        if (OwnerPayout::where('owner_id', auth()->id())->where('status', 'pending')->exists()) {
            static::fail('period_start', __('You already have a pending payout request.'));
        }

        $data['owner_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['adjustments'] = 0;

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
