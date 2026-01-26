<?php

declare(strict_types=1);

/**
 * ListExpenses Page
 * 
 * Lists all expenses for the hall owner with filtering and statistics.
 * 
 * @package App\Filament\Owner\Resources\ExpenseResource\Pages
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Filament\Owner\Resources\ExpenseResource\Pages;

use App\Enums\ExpensePaymentStatus;
use App\Enums\ExpenseType;
use App\Filament\Owner\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * ListExpenses Page Class
 */
class ListExpenses extends ListRecords
{
    /**
     * The resource this page belongs to.
     *
     * @var string
     */
    protected static string $resource = ExpenseResource::class;

    /**
     * Get the header actions for this page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(fn() => app()->getLocale() === 'ar' ? 'إضافة مصروف' : 'Add Expense'),
        ];
    }

    /**
     * Get the header widgets for this page.
     *
     * @return array
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseResource\Widgets\ExpenseStatsWidget::class,
        ];
    }

    /**
     * Get the tabs for filtering records.
     *
     * @return array
     */
    public function getTabs(): array
    {
        $ownerId = Auth::user()?->hallOwner?->id ?? Auth::id();
        $baseQuery = fn() => Expense::where('owner_id', $ownerId);

        return [
            // All Expenses
            'all' => Tab::make(app()->getLocale() === 'ar' ? 'الكل' : 'All')
                ->badge($baseQuery()->count())
                ->badgeColor('gray'),

            // Booking Expenses
            'booking' => Tab::make(app()->getLocale() === 'ar' ? 'مصروفات الحجوزات' : 'Booking')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('booking_id'))
                ->badge($baseQuery()->whereNotNull('booking_id')->count())
                ->badgeColor('info'),

            // Operational Expenses
            'operational' => Tab::make(app()->getLocale() === 'ar' ? 'تشغيلية' : 'Operational')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('expense_type', ExpenseType::Operational))
                ->badge($baseQuery()->where('expense_type', ExpenseType::Operational)->count())
                ->badgeColor('warning'),

            // Recurring Expenses
            'recurring' => Tab::make(app()->getLocale() === 'ar' ? 'متكررة' : 'Recurring')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_recurring', true))
                ->badge($baseQuery()->where('is_recurring', true)->count())
                ->badgeColor('success'),

            // Pending Payment
            'pending' => Tab::make(app()->getLocale() === 'ar' ? 'في الانتظار' : 'Pending Payment')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('payment_status', [
                    ExpensePaymentStatus::Pending,
                    ExpensePaymentStatus::Partial,
                ]))
                ->badge($baseQuery()->whereIn('payment_status', [
                    ExpensePaymentStatus::Pending,
                    ExpensePaymentStatus::Partial,
                ])->count())
                ->badgeColor('danger'),
        ];
    }
}
