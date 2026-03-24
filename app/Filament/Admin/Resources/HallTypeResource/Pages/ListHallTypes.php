<?php

namespace App\Filament\Admin\Resources\HallTypeResource\Pages;

use App\Filament\Admin\Resources\HallTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListHallTypes extends ListRecords
{
    protected static string $resource = HallTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('hall-type.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\HallType::count()),

            'active' => Tab::make(__('hall-type.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\HallType::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('hall-type.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\HallType::where('is_active', false)->count())
                ->badgeColor('danger'),
        ];
    }
}
