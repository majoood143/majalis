<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
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
            Stat::make('Total Revenue', number_format($totalRevenue, 3) . ' OMR')
                ->description('All-time revenue')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Platform Earnings', number_format($platformEarnings, 3) . ' OMR')
                ->description('Total commission earned')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Monthly Revenue', number_format($monthlyRevenue, 3) . ' OMR')
                ->description('This month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Total Bookings', Booking::count())
                ->description($bookingsTrend >= 0 ? "+{$bookingsTrend}% from last month" : "{$bookingsTrend}% from last month")
                ->descriptionIcon($bookingsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($bookingsTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Active Halls', Hall::where('is_active', true)->count())
                ->description('Out of ' . Hall::count() . ' total halls')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Hall Owners', User::where('role', 'hall_owner')->count())
                ->description(User::where('role', 'customer')->count() . ' customers registered')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
