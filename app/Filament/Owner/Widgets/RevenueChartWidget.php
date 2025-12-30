<?php

declare(strict_types=1);

namespace App\Filament\Owner\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Trend';

    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 2,
        'xl' => 2,
    ];

    protected static ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return __('owner.widgets.revenue_trend');
    }

    public function getDescription(): ?string
    {
        $period = $this->filter ?? '30';
        return __('owner.widgets.revenue_last_days', ['days' => $period]);
    }

    /**
     * Get chart data using raw queries for better control
     */
    protected function getData(): array
    {
        $user = Auth::user();
        $period = (int) ($this->filter ?? '30');

        $startDate = now()->subDays($period)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Get daily revenue data
        $revenueData = DB::table('bookings')
            ->join('halls', 'bookings.hall_id', '=', 'halls.id')
            ->select(
                DB::raw('DATE(bookings.booking_date) as date'),
                DB::raw('SUM(bookings.total_amount) as gross_revenue'),
                DB::raw('SUM(bookings.commission_amount) as commission'),
                DB::raw('SUM(bookings.owner_payout) as net_revenue')
            )
            ->where('halls.owner_id', $user->id)
            ->where('bookings.payment_status', 'paid')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zeros
        $dateRange = Carbon::parse($startDate)->daysUntil($endDate);
        $filledData = [];

        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayData = $revenueData->firstWhere('date', $dateStr);

            $filledData[] = [
                'date' => $date->format('M j'),
                'gross' => (float) ($dayData->gross_revenue ?? 0),
                'net' => (float) ($dayData->net_revenue ?? 0),
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => __('owner.widgets.gross_revenue'),
                    'data' => array_column($filledData, 'gross'),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => __('owner.widgets.net_revenue'),
                    'data' => array_column($filledData, 'net'),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => array_column($filledData, 'date'),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => __('owner.widgets.last_7_days'),
            '30' => __('owner.widgets.last_30_days'),
            '60' => __('owner.widgets.last_60_days'),
            '90' => __('owner.widgets.last_90_days'),
        ];
    }
}
