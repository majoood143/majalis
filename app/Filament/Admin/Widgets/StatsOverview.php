<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = Cache::remember('admin.stats_overview', now()->addMinutes(5), function () {
            $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $platformEarnings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->sum('commission_amount');

            $monthlyRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount');

            $monthlyBookings = Booking::whereMonth('created_at', now()->month)->count();
            $previousMonthBookings = Booking::whereMonth('created_at', now()->subMonth()->month)->count();
            $bookingsTrend = $previousMonthBookings > 0
                ? (($monthlyBookings - $previousMonthBookings) / $previousMonthBookings) * 100
                : 0;

            return [
                'totalRevenue' => $totalRevenue,
                'platformEarnings' => $platformEarnings,
                'monthlyRevenue' => $monthlyRevenue,
                'totalBookings' => Booking::count(),
                'monthlyBookings' => $monthlyBookings,
                'bookingsTrend' => $bookingsTrend,
                'totalHalls' => Hall::count(),
                'activeHalls' => Hall::where('is_active', true)->count(),
                'hallOwners' => User::where('role', 'hall_owner')->count(),
                'customerCount' => User::where('role', 'customer')->count(),
            ];
        });

        $bookingsTrendValue = round(abs($stats['bookingsTrend']), 1);

        return [
            Stat::make(__('admin.stats_overview.total_revenue'), number_format($stats['totalRevenue'], 3) . ' OMR')
                ->description(__('admin.stats_overview.total_revenue_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('admin.stats_overview.platform_earnings'), number_format($stats['platformEarnings'], 3) . ' OMR')
                ->description(__('admin.stats_overview.platform_earnings_desc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make(__('admin.stats_overview.monthly_revenue'), number_format($stats['monthlyRevenue'], 3) . ' OMR')
                ->description(__('admin.stats_overview.monthly_revenue_desc'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make(__('admin.stats_overview.total_bookings'), $stats['totalBookings'])
                ->description($stats['bookingsTrend'] >= 0
                    ? __('admin.stats_overview.bookings_trend_up', ['value' => $bookingsTrendValue])
                    : __('admin.stats_overview.bookings_trend_down', ['value' => $bookingsTrendValue]))
                ->descriptionIcon($stats['bookingsTrend'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($stats['bookingsTrend'] >= 0 ? 'success' : 'danger'),

            Stat::make(__('admin.stats_overview.active_halls'), $stats['activeHalls'])
                ->description(__('admin.stats_overview.active_halls_desc', ['total' => $stats['totalHalls']]))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make(__('admin.stats_overview.hall_owners'), $stats['hallOwners'])
                ->description(__('admin.stats_overview.hall_owners_desc', ['count' => $stats['customerCount']]))
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
