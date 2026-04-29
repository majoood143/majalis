<?php

namespace App\Filament\Admin\Resources\HallOwnerResource\Widgets;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\HallOwner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HallOwnerStatsOverview extends BaseWidget
{
    public ?HallOwner $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $ownerId = $this->record->user_id;

        $hallIds = Hall::where('owner_id', $ownerId)->pluck('id');
        $totalHalls = $hallIds->count();
        $activeHalls = Hall::where('owner_id', $ownerId)->where('is_active', true)->count();

        $baseQuery = fn() => Booking::whereIn('hall_id', $hallIds);

        $totalBookings = $baseQuery()->count();
        $confirmedBookings = $baseQuery()->whereIn('status', ['confirmed', 'completed'])->count();
        $pendingBookings = $baseQuery()->where('status', 'pending')->count();
        $cancelledBookings = $baseQuery()->where('status', 'cancelled')->count();

        $paidQuery = $baseQuery()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid');

        $totalIncome = (clone $paidQuery)->sum('total_amount');
        $totalCommission = (clone $paidQuery)->sum('commission_amount');
        $totalPayout = (clone $paidQuery)->sum('owner_payout');

        $pendingPayout = $baseQuery()
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->sum('owner_payout');

        return [
            Stat::make(__('hall-owner.widgets.total_halls'), $totalHalls)
                ->description($activeHalls . ' ' . __('hall-owner.widgets.active_halls'))
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),

            Stat::make(__('hall-owner.widgets.total_bookings'), $totalBookings)
                ->description($confirmedBookings . ' confirmed • ' . $pendingBookings . ' pending • ' . $cancelledBookings . ' cancelled')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make(__('hall-owner.widgets.total_income'), number_format((float) $totalIncome, 3) . ' OMR')
                ->description(__('hall-owner.widgets.from_paid_bookings'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('hall-owner.widgets.total_commission'), number_format((float) $totalCommission, 3) . ' OMR')
                ->description(__('hall-owner.widgets.platform_commission'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),

            Stat::make(__('hall-owner.widgets.total_payout'), number_format((float) $totalPayout, 3) . ' OMR')
                ->description(number_format((float) $pendingPayout, 3) . ' OMR ' . __('hall-owner.widgets.pending_payout'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
