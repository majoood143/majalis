<?php

namespace App\Filament\Admin\Resources\CityResource\Pages;

use App\Filament\Admin\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportCities')
                ->label('Export Cities')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportCities())
                ->requiresConfirmation()
                ->modalHeading('Export Cities Data')
                ->modalDescription('This will export all cities data to a CSV file.')
                ->modalSubmitActionLabel('Export'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Cities')
                ->icon('heroicon-o-building-office-2')
                ->badge(fn() => \App\Models\City::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\City::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\City::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_halls' => Tab::make('With Halls')
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('halls'))
                ->badge(fn() => \App\Models\City::has('halls')->count())
                ->badgeColor('info'),

            'without_halls' => Tab::make('Without Halls')
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->doesntHave('halls'))
                ->badge(fn() => \App\Models\City::doesntHave('halls')->count())
                ->badgeColor('warning'),
        ];
    }

    protected function exportCities(): void
    {
        $cities = \App\Models\City::with('region')->get();

        $filename = 'cities_export_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        // Create directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // Add CSV headers
        fputcsv($file, [
            'ID',
            'Name (EN)',
            'Name (AR)',
            'Code',
            'Region',
            'Latitude',
            'Longitude',
            'Halls Count',
            'Active',
            'Order',
            'Created At',
        ]);

        // Add data rows
        foreach ($cities as $city) {
            fputcsv($file, [
                $city->id,
                $city->getTranslation('name', 'en'),
                $city->getTranslation('name', 'ar'),
                $city->code,
                $city->region->name ?? 'N/A',
                $city->latitude ?? 'N/A',
                $city->longitude ?? 'N/A',
                $city->halls()->count(),
                $city->is_active ? 'Yes' : 'No',
                $city->order,
                $city->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        // Notify user
        \Filament\Notifications\Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Cities exported successfully to: ' . $filename)
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download')
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
