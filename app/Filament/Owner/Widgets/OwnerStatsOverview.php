<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use App\Models\Hall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OwnerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $ownerId = Auth::id();

        $totalHalls = Hall::where('owner_id', $ownerId)->count();
        $activeHalls = Hall::where('owner_id', $ownerId)->where('is_active', true)->count();

        $totalBookings = Booking::whereHas('hall', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })->count();

        $pendingBookings = Booking::whereHas('hall', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })->where('status', 'pending')->count();

        $totalEarnings = Booking::whereHas('hall', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');

        $monthlyEarnings = Booking::whereHas('hall', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('owner_payout');

        $upcomingBookings = Booking::whereHas('hall', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return [
            Stat::make(__('widgets.owner-stats-overview.my_halls'), $totalHalls)
                ->description(__('widgets.owner-stats-overview.active_halls_desc', ['count' => $activeHalls]))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make(__('widgets.owner-stats-overview.total_earnings'), number_format($totalEarnings, 3) . ' ' . __('common.currency.omr'))
                ->description(__('widgets.owner-stats-overview.all_time_earnings'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make(__('widgets.owner-stats-overview.monthly_earnings'), number_format($monthlyEarnings, 3) . ' ' . __('common.currency.omr'))
                ->description(__('widgets.owner-stats-overview.this_month'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make(__('widgets.owner-stats-overview.total_bookings'), $totalBookings)
                ->description(__('widgets.owner-stats-overview.upcoming_desc', ['count' => $upcomingBookings]))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make(__('widgets.owner-stats-overview.pending_bookings'), $pendingBookings)
                ->description(__('widgets.owner-stats-overview.requires_action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingBookings > 0 ? 'danger' : 'gray'),
        ];
    }
}
