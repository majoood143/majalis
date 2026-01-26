<?php

declare(strict_types=1);

/**
 * ExpenseStatsWidget
 * 
 * Displays expense statistics on the expenses list page.
 * Shows total expenses, pending payments, and monthly trends.
 * 
 * @package App\Filament\Owner\Resources\ExpenseResource\Widgets
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources\ExpenseResource\Widgets;

use App\Enums\ExpensePaymentStatus;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * ExpenseStatsWidget Class
 * 
 * Provides statistical overview of expenses for hall owners.
 */
class ExpenseStatsWidget extends BaseWidget
{
    /**
     * Get the statistics to display.
     *
     * @return array
     */
    protected function getStats(): array
    {
        $ownerId = Auth::user()?->hallOwner?->id ?? Auth::id();
        
        // Total expenses this month
        $monthlyTotal = Expense::where('owner_id', $ownerId)
            ->approved()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('total_amount');

        // Total expenses last month for comparison
        $lastMonthTotal = Expense::where('owner_id', $ownerId)
            ->approved()
            ->whereMonth('expense_date', now()->subMonth()->month)
            ->whereYear('expense_date', now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate percentage change
        $percentageChange = $lastMonthTotal > 0 
            ? round((($monthlyTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
            : 0;

        // Pending payments total
        $pendingTotal = Expense::where('owner_id', $ownerId)
            ->whereIn('payment_status', [ExpensePaymentStatus::Pending, ExpensePaymentStatus::Partial])
            ->sum('total_amount');

        // Count of pending payments
        $pendingCount = Expense::where('owner_id', $ownerId)
            ->whereIn('payment_status', [ExpensePaymentStatus::Pending, ExpensePaymentStatus::Partial])
            ->count();

        // Yearly total
        $yearlyTotal = Expense::where('owner_id', $ownerId)
            ->approved()
            ->whereYear('expense_date', now()->year)
            ->sum('total_amount');

        // Booking-linked expenses this month
        $bookingExpenses = Expense::where('owner_id', $ownerId)
            ->approved()
            ->whereNotNull('booking_id')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('total_amount');

        return [
            // Monthly Total
            Stat::make(
                app()->getLocale() === 'ar' ? 'مصروفات هذا الشهر' : 'This Month',
                number_format((float) $monthlyTotal, 3) . ' OMR'
            )
                ->description(
                    $percentageChange >= 0 
                        ? (app()->getLocale() === 'ar' ? "زيادة {$percentageChange}% عن الشهر الماضي" : "{$percentageChange}% increase from last month")
                        : (app()->getLocale() === 'ar' ? "انخفاض " . abs($percentageChange) . "% عن الشهر الماضي" : abs($percentageChange) . "% decrease from last month")
                )
                ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentageChange >= 0 ? 'danger' : 'success')
                ->chart($this->getMonthlyChart($ownerId)),

            // Pending Payments
            Stat::make(
                app()->getLocale() === 'ar' ? 'مدفوعات معلقة' : 'Pending Payments',
                number_format((float) $pendingTotal, 3) . ' OMR'
            )
                ->description(
                    app()->getLocale() === 'ar' 
                        ? "{$pendingCount} مصروف في الانتظار" 
                        : "{$pendingCount} expenses pending"
                )
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            // Yearly Total
            Stat::make(
                app()->getLocale() === 'ar' ? 'إجمالي هذه السنة' : 'Year to Date',
                number_format((float) $yearlyTotal, 3) . ' OMR'
            )
                ->description(
                    app()->getLocale() === 'ar' 
                        ? 'إجمالي ' . now()->year 
                        : now()->year . ' total'
                )
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            // Booking-linked Expenses
            Stat::make(
                app()->getLocale() === 'ar' ? 'مصروفات الحجوزات' : 'Booking Expenses',
                number_format((float) $bookingExpenses, 3) . ' OMR'
            )
                ->description(
                    app()->getLocale() === 'ar' 
                        ? 'مرتبطة بحجوزات هذا الشهر' 
                        : 'Linked to bookings this month'
                )
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }

    /**
     * Get monthly expense chart data.
     *
     * @param int $ownerId
     * @return array
     */
    protected function getMonthlyChart(int $ownerId): array
    {
        $data = [];
        
        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Expense::where('owner_id', $ownerId)
                ->approved()
                ->whereDate('expense_date', $date)
                ->sum('total_amount');
            
            $data[] = (float) $total;
        }

        return $data;
    }
}
