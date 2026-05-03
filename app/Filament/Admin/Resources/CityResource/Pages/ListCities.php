<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\City;
use Filament\Notifications\Notification;
use App\Filament\Admin\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->label(__('city.list_actions.create')),

            Action::make('exportCities')
                ->label(__('city.list_actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportCities())
                ->requiresConfirmation()
                ->modalHeading(__('city.export_modal.heading'))
                ->modalDescription(__('city.export_modal.description'))
                ->modalSubmitActionLabel(__('city.export_modal.submit_label')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('city.tabs.all'))
                ->icon('heroicon-o-building-office-2')
                ->badge(fn() => City::count()),

            'active' => Tab::make(__('city.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => City::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('city.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => City::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_halls' => Tab::make(__('city.tabs.with_halls'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('halls'))
                ->badge(fn() => City::has('halls')->count())
                ->badgeColor('info'),

            'without_halls' => Tab::make(__('city.tabs.without_halls'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->doesntHave('halls'))
                ->badge(fn() => City::doesntHave('halls')->count())
                ->badgeColor('warning'),
        ];
    }

    protected function exportCities(): void
    {
        $cities = City::with('region')->get();

        $filename = 'cities_export_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // Add CSV headers
        fputcsv($file, [
            __('city.export_headers.id'),
            __('city.export_headers.name_en'),
            __('city.export_headers.name_ar'),
            __('city.export_headers.code'),
            __('city.export_headers.region'),
            __('city.export_headers.latitude'),
            __('city.export_headers.longitude'),
            __('city.export_headers.halls_count'),
            __('city.export_headers.active'),
            __('city.export_headers.order'),
            __('city.export_headers.created_at'),
        ]);

        // Add data rows
        foreach ($cities as $city) {
            fputcsv($file, [
                $city->id,
                $city->getTranslation('name', 'en'),
                $city->getTranslation('name', 'ar'),
                $city->code,
                $city->region->name ?? __('city.export_values.not_applicable'),
                $city->latitude ?? __('city.export_values.not_applicable'),
                $city->longitude ?? __('city.export_values.not_applicable'),
                $city->halls()->count(),
                $city->is_active ? __('city.export_values.yes') : __('city.export_values.no'),
                $city->order,
                $city->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        // Notify user
        Notification::make()
            ->title(__('city.notifications.export_successful'))
            ->success()
            ->body(__('city.notifications.export_body', ['filename' => $filename]))
            ->persistent()
            ->actions([
                Action::make('download')
                    ->label(__('city.notifications.download'))
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add custom widgets here if needed
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add custom widgets here if needed
        ];
    }
}