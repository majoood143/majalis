<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Region;
use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->label(__('region.list_actions.create')),

            Action::make('exportRegions')
                ->label(__('region.list_actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportRegions()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('region.tabs.all'))
                ->icon('heroicon-o-map')
                ->badge(fn() => Region::count()),

            'active' => Tab::make(__('region.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => Region::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('region.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => Region::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_cities' => Tab::make(__('region.tabs.with_cities'))
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('cities'))
                ->badge(fn() => Region::has('cities')->count())
                ->badgeColor('info'),
        ];
    }

    protected function exportRegions(): void
    {
        $regions = Region::withCount('cities')->get();

        $filename = 'regions_export_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            __('region.export_headers.id'),
            __('region.export_headers.name_en'),
            __('region.export_headers.name_ar'),
            __('region.export_headers.code'),
            __('region.export_headers.cities'),
            __('region.export_headers.active'),
            __('region.export_headers.order'),
            __('region.export_headers.created_at'),
        ]);

        foreach ($regions as $region) {
            fputcsv($file, [
                $region->id,
                $region->getTranslation('name', 'en'),
                $region->getTranslation('name', 'ar'),
                $region->code,
                $region->cities_count,
                $region->is_active ? __('region.export_values.yes') : __('region.export_values.no'),
                $region->order,
                $region->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title(__('region.notifications.export_successful'))
            ->actions([
                Action::make('download')
                    ->label(__('region.notifications.download'))
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}