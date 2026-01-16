<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\BookingResource\Pages;

use App\Filament\Owner\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

/**
 * ListBookings Page for Owner Panel
 *
 * Displays all bookings for the authenticated owner's halls
 * with tabs for quick filtering by status.
 */
class ListBookings extends ListRecords
{
    /**
     * The resource class this page belongs to.
     */
    protected static string $resource = BookingResource::class;

    /**
     * Get the header actions for this page.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Owners cannot create bookings - removed CreateAction
            // Actions\Action::make('export')
            //     ->label(__('owner_booking.pages.list.export_label'))
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('gray')
            //     ->action(function () {
            //         // TODO: Implement export
            //         $this->notify('info', __('owner_booking.pages.list.export_notification'));
            //     }),

            Actions\Action::make('export')
                ->label(__('owner_booking.pages.list.export_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    // TODO: Implement export
                    \Filament\Notifications\Notification::make()
                        ->title(__('owner_booking.pages.list.export_notification'))
                        ->info()
                        ->send();
                }),
        ];
    }

    /**
     * Get the tabs for filtering bookings by status.
     */
    public function getTabs(): array
    {
        $baseQuery = fn() => $this->getFilteredTableQuery();

        return [
            'all' => Tab::make(__('owner_booking.pages.list.tabs.all'))
                ->icon('heroicon-o-rectangle-stack')
                ->badge(fn() => $baseQuery()->count())
                ->badgeColor('gray'),

            'pending' => Tab::make(__('owner_booking.pages.list.tabs.pending'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => $baseQuery()->where('status', 'pending')->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make(__('owner_booking.pages.list.tabs.confirmed'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn() => $baseQuery()->where('status', 'confirmed')->count())
                ->badgeColor('success'),

            'upcoming' => Tab::make(__('owner_booking.pages.list.tabs.upcoming'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                )
                ->badge(fn() => $baseQuery()
                    ->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count())
                ->badgeColor('info'),

            'completed' => Tab::make(__('owner_booking.pages.list.tabs.completed'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => $baseQuery()->where('status', 'completed')->count())
                ->badgeColor('info'),

            'cancelled' => Tab::make(__('owner_booking.pages.list.tabs.cancelled'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn() => $baseQuery()->where('status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }

    /**
     * Get the header widgets for this page.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            BookingResource\Widgets\BookingStatsWidget::class,
        ];
    }
}
