<?php

namespace App\Filament\Admin\Resources\HallResource\Widgets;

use App\Models\Hall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HallStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalHalls = Hall::count();
        $activeHalls = Hall::where('is_active', true)->count();
        $featuredHalls = Hall::where('is_featured', true)->count();
        $pendingHalls = Hall::where('requires_approval', true)->count();

        $totalRevenue = Hall::sum('price_per_slot');
        $averagePrice = $totalHalls > 0 ? round($totalRevenue / $totalHalls, 2) : 0;

        return [
            Stat::make(__('admin.stats.total_halls'), $totalHalls)
                ->description(__('admin.stats.total_halls_desc'))
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make(__('admin.stats.active_halls'), $activeHalls)
                ->description(__('admin.stats.active_halls_desc'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('admin.stats.featured_halls'), $featuredHalls)
                ->description(__('admin.stats.featured_halls_desc'))
                ->icon('heroicon-o-star')
                ->color('warning'),

            Stat::make(__('admin.stats.pending_halls'), $pendingHalls)
                ->description(__('admin.stats.pending_halls_desc'))
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make(__('admin.stats.average_price'), number_format($averagePrice, 2) . ' OMR')
                ->description(__('admin.stats.average_price_desc'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
