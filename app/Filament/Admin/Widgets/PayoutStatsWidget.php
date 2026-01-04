<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\PayoutStatus;
use App\Models\OwnerPayout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
    protected static ?string $pollingInterval = '60s';

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
        // Get payout statistics
        $pendingCount = OwnerPayout::where('status', PayoutStatus::PENDING)->count();
        $pendingAmount = (float) OwnerPayout::where('status', PayoutStatus::PENDING)->sum('net_payout');

        $processingCount = OwnerPayout::where('status', PayoutStatus::PROCESSING)->count();
        $processingAmount = (float) OwnerPayout::where('status', PayoutStatus::PROCESSING)->sum('net_payout');

        $completedThisMonth = OwnerPayout::where('status', PayoutStatus::COMPLETED)
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year);
        $completedCount = $completedThisMonth->count();
        $completedAmount = (float) $completedThisMonth->sum('net_payout');

        $totalCompletedAll = (float) OwnerPayout::where('status', PayoutStatus::COMPLETED)->sum('net_payout');

        $onHoldCount = OwnerPayout::where('status', PayoutStatus::ON_HOLD)->count();
        $failedCount = OwnerPayout::where('status', PayoutStatus::FAILED)->count();

        // Calculate trends (compare with last month)
        $lastMonthCompleted = (float) OwnerPayout::where('status', PayoutStatus::COMPLETED)
            ->whereMonth('completed_at', now()->subMonth()->month)
            ->whereYear('completed_at', now()->subMonth()->year)
            ->sum('net_payout');

        $trend = $lastMonthCompleted > 0
            ? round((($completedAmount - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1)
            : 0;

        return [
            // Pending Payouts
            Stat::make(
                __('admin.payout.stats.pending'),
                number_format($pendingAmount, 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.pending_count', ['count' => $pendingCount]))
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
                number_format($processingAmount, 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.processing_count', ['count' => $processingCount]))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),

            // Completed This Month
            Stat::make(
                __('admin.payout.stats.completed_month'),
                number_format($completedAmount, 3) . ' OMR'
            )
                ->description(
                    $trend >= 0
                        ? __('admin.payout.stats.increase', ['percent' => abs($trend)])
                        : __('admin.payout.stats.decrease', ['percent' => abs($trend)])
                )
                ->descriptionIcon($trend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger')
                ->chart($this->getCompletedTrend()),

            // Total Paid Out (All Time)
            Stat::make(
                __('admin.payout.stats.total_paid'),
                number_format($totalCompletedAll, 3) . ' OMR'
            )
                ->description(__('admin.payout.stats.all_time'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            // On Hold
            Stat::make(
                __('admin.payout.stats.on_hold'),
                (string) $onHoldCount
            )
                ->description(__('admin.payout.stats.requires_attention'))
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color($onHoldCount > 0 ? 'warning' : 'gray'),

            // Failed
            Stat::make(
                __('admin.payout.stats.failed'),
                (string) $failedCount
            )
                ->description(__('admin.payout.stats.needs_review'))
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($failedCount > 0 ? 'danger' : 'gray'),
        ];
    }

    /**
     * Get pending payouts trend data for the last 7 days.
     *
     * @return array
     */
    protected function getPendingTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = OwnerPayout::where('status', PayoutStatus::PENDING)
                ->whereDate('created_at', '<=', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Get completed payouts trend data for the last 6 months.
     *
     * @return array
     */
    protected function getCompletedTrend(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $amount = OwnerPayout::where('status', PayoutStatus::COMPLETED)
                ->whereMonth('completed_at', $month->month)
                ->whereYear('completed_at', $month->year)
                ->sum('net_payout');
            $data[] = (float) $amount;
        }
        return $data;
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
