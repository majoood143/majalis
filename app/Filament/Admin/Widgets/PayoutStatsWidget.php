<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\PayoutStatus;
use App\Models\OwnerPayout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * PayoutStatsWidget
 *
 * Displays summary statistics for owner payouts on the admin dashboard.
 * Shows pending, processing, and completed payout totals.
 *
 * @package App\Filament\Admin\Widgets
 */
class PayoutStatsWidget extends BaseWidget
{
    /**
     * Sort order for this widget.
     *
     * @var int
     */
    protected static ?int $sort = 30;

    /**
     * Polling interval for auto-refresh.
     *
     * @var string|null
     */
    protected ?string $pollingInterval = '60s';

    /**
     * Number of columns for the widget grid.
     *
     * @var int
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Get the statistics to display.
     *
     * @return array
     */
    protected function getStats(): array
    {
        $stats = Cache::remember('admin.payout_stats', now()->addMinutes(5), function () {
            $pendingCount = OwnerPayout::where('status', PayoutStatus::PENDING)->count();
            $pendingAmount = (float) OwnerPayout::where('status', PayoutStatus::PENDING)->sum('net_payout');

            $processingCount = OwnerPayout::where('status', PayoutStatus::PROCESSING)->count();
            $processingAmount = (float) OwnerPayout::where('status', PayoutStatus::PROCESSING)->sum('net_payout');

            $completedThisMonth = OwnerPayout::where('status', PayoutStatus::COMPLETED)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year);
            $completedAmount = (float) $completedThisMonth->sum('net_payout');

            $totalCompletedAll = (float) OwnerPayout::where('status', PayoutStatus::COMPLETED)->sum('net_payout');

            $onHoldCount = OwnerPayout::where('status', PayoutStatus::ON_HOLD)->count();
            $failedCount = OwnerPayout::where('status', PayoutStatus::FAILED)->count();

            $lastMonthCompleted = (float) OwnerPayout::where('status', PayoutStatus::COMPLETED)
                ->whereMonth('completed_at', now()->subMonth()->month)
                ->whereYear('completed_at', now()->subMonth()->year)
                ->sum('net_payout');

            $trend = $lastMonthCompleted > 0
                ? round((($completedAmount - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1)
                : 0;

            return [
                'pendingCount' => $pendingCount,
                'pendingAmount' => $pendingAmount,
                'processingCount' => $processingCount,
                'processingAmount' => $processingAmount,
                'completedAmount' => $completedAmount,
                'totalCompletedAll' => $totalCompletedAll,
                'onHoldCount' => $onHoldCount,
                'failedCount' => $failedCount,
                'trend' => $trend,
            ];
        });

        return [
            // Pending Payouts
            Stat::make(
                __('admin.payout.stats.pending'),
                number_format($stats['pendingAmount'], 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.pending_count', ['count' => $stats['pendingCount']]))
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chart($this->getPendingTrend())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setFilter', { filter: 'status', value: 'pending' })",
                ]),

            // Processing Payouts
            Stat::make(
                __('admin.payout.stats.processing'),
                number_format($stats['processingAmount'], 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.processing_count', ['count' => $stats['processingCount']]))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),

            // Completed This Month
            Stat::make(
                __('admin.payout.stats.completed_month'),
                number_format($stats['completedAmount'], 3) . ' OMR'
            )
                ->description(
                    $stats['trend'] >= 0
                        ? __('admin.payout.stats.increase', ['percent' => abs($stats['trend'])])
                        : __('admin.payout.stats.decrease', ['percent' => abs($stats['trend'])])
                )
                ->descriptionIcon($stats['trend'] >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($stats['trend'] >= 0 ? 'success' : 'danger')
                ->chart($this->getCompletedTrend()),

            // Total Paid Out (All Time)
            Stat::make(
                __('admin.payout.stats.total_paid'),
                number_format($stats['totalCompletedAll'], 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.all_time'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            // On Hold
            Stat::make(
                __('admin.payout.stats.on_hold'),
                (string) $stats['onHoldCount']
            )
                ->description(__('admin.payout.stats.requires_attention'))
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color($stats['onHoldCount'] > 0 ? 'warning' : 'gray'),

            // Failed
            Stat::make(
                __('admin.payout.stats.failed'),
                (string) $stats['failedCount']
            )
                ->description(__('admin.payout.stats.needs_review'))
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($stats['failedCount'] > 0 ? 'danger' : 'gray'),
        ];
    }

    /**
     * Get pending payouts trend data for the last 7 days.
     *
     * @return array
     */
    protected function getPendingTrend(): array
    {
        return Cache::remember('admin.payout_stats.pending_trend', now()->addMinutes(5), function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $data[] = OwnerPayout::where('status', PayoutStatus::PENDING)
                    ->whereDate('created_at', '<=', $date)
                    ->count();
            }
            return $data;
        });
    }

    /**
     * Get completed payouts trend data for the last 6 months.
     *
     * @return array
     */
    protected function getCompletedTrend(): array
    {
        return Cache::remember('admin.payout_stats.completed_trend', now()->addMinutes(5), function () {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $data[] = (float) OwnerPayout::where('status', PayoutStatus::COMPLETED)
                    ->whereMonth('completed_at', $month->month)
                    ->whereYear('completed_at', $month->year)
                    ->sum('net_payout');
            }
            return $data;
        });
    }

    /**
     * Get the heading for this widget.
     *
     * @return string|null
     */
    protected function getHeading(): ?string
    {
        return __('admin.payout.stats.heading');
    }

    /**
     * Get the description for this widget.
     *
     * @return string|null
     */
    protected function getDescription(): ?string
    {
        return __('admin.payout.stats.description');
    }
}
