<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('booking.tabs.all'))
                ->badge(fn() => static::getResource()::getModel()::count()),

            'pending' => Tab::make(__('booking.tabs.pending'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => static::getResource()::getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make(__('booking.tabs.confirmed'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn() => static::getResource()::getModel()::where('status', 'confirmed')->count())
                ->badgeColor('success'),

            'completed' => Tab::make(__('booking.tabs.completed'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => static::getResource()::getModel()::where('status', 'completed')->count())
                ->badgeColor('info'),

            'cancelled' => Tab::make(__('booking.tabs.cancelled'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn() => static::getResource()::getModel()::where('status', 'cancelled')->count())
                ->badgeColor('danger'),

            'today' => Tab::make(__('booking.tabs.today'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('booking_date', today()))
                ->badge(fn() => static::getResource()::getModel()::whereDate('booking_date', today())->count())
                ->badgeColor('primary'),

            'upcoming' => Tab::make(__('booking.tabs.upcoming'))
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('booking_date', '>=', now())
                    ->whereIn('status', ['pending', 'confirmed']))
                ->badge(fn() => static::getResource()::getModel()::where('booking_date', '>=', now())
                    ->whereIn('status', ['pending', 'confirmed'])->count())
                ->badgeColor('info'),
        ];
    }
}
