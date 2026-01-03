<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PayoutResource\Widgets;

use App\Enums\PayoutStatus;
use App\Models\OwnerPayout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * PayoutSummaryWidget
 *
 * Displays summary statistics for owner payouts at the top of the
 * payouts list page.
 *
 * Metrics displayed:
 * - Total Received (completed payouts)
 * - Pending Amount
 * - Processing Amount
 * - Average Payout
 *
 * @package App\Filament\Owner\Resources\PayoutResource\Widgets
 */
class PayoutSummaryWidget extends BaseWidget
{
    /**
     * Widget column span.
     *
     * @var int|string|array
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Polling interval (auto-refresh).
     *
     * @var string|null
     */
    protected static ?string $pollingInterval = '60s';

    /**
     * Get the stats to display.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $user = Auth::user();

        // Total completed payouts
        $totalReceived = (float) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::COMPLETED)
            ->sum('net_payout');

        $completedCount = (int) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::COMPLETED)
            ->count();

        // Pending payouts
        $pendingAmount = (float) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::PENDING)
            ->sum('net_payout');

        $pendingCount = (int) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::PENDING)
            ->count();

        // Processing payouts
        $processingAmount = (float) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::PROCESSING)
            ->sum('net_payout');

        $processingCount = (int) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::PROCESSING)
            ->count();

        // Average payout
        $avgPayout = $completedCount > 0 ? $totalReceived / $completedCount : 0;

        // This year received
        $thisYearReceived = (float) OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::COMPLETED)
            ->whereYear('completed_at', now()->year)
            ->sum('net_payout');

        // Last payout
        $lastPayout = OwnerPayout::where('owner_id', $user->id)
            ->where('status', PayoutStatus::COMPLETED)
            ->latest('completed_at')
            ->first();

        return [
            // Total Received
            Stat::make(
                __('owner.payouts.stat_received'),
                number_format($totalReceived, 3) . ' OMR'
            )
                ->description(__('owner.payouts.stat_received_desc', ['count' => $completedCount]))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->getPayoutChartData($user)),

            // Pending
            Stat::make(
                __('owner.payouts.stat_pending'),
                number_format($pendingAmount, 3) . ' OMR'
            )
                ->description(__('owner.payouts.stat_pending_desc', ['count' => $pendingCount]))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),

            // Processing
            Stat::make(
                __('owner.payouts.stat_processing'),
                number_format($processingAmount, 3) . ' OMR'
            )
                ->description(__('owner.payouts.stat_processing_desc', ['count' => $processingCount]))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($processingCount > 0 ? 'info' : 'gray'),

            // Average Payout
            Stat::make(
                __('owner.payouts.stat_average'),
                number_format($avgPayout, 3) . ' OMR'
            )
                ->description(__('owner.payouts.stat_average_desc'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),

            // This Year
            Stat::make(
                __('owner.payouts.stat_this_year'),
                number_format($thisYearReceived, 3) . ' OMR'
            )
                ->description(__('owner.payouts.stat_this_year_desc', ['year' => now()->year]))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            // Last Payout
            Stat::make(
                __('owner.payouts.stat_last_payout'),
                $lastPayout
                    ? number_format((float) $lastPayout->net_payout, 3) . ' OMR'
                    : '-'
            )
                ->description($lastPayout
                    ? $lastPayout->completed_at->format('M d, Y')
                    : __('owner.payouts.no_payouts_yet'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),
        ];
    }

    /**
     * Get payout chart data for the last 6 months.
     *
     * @param \App\Models\User $user
     * @return array<int>
     */
    protected function getPayoutChartData($user): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $monthPayout = (float) OwnerPayout::where('owner_id', $user->id)
                ->where('status', PayoutStatus::COMPLETED)
                ->whereMonth('completed_at', $month->month)
                ->whereYear('completed_at', $month->year)
                ->sum('net_payout');

            $data[] = (int) round($monthPayout);
        }

        return $data;
    }
}
