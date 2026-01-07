<?php

namespace App\Filament\Admin\Resources\RegionResource\Pages;

use App\Filament\Admin\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportRegions')
                ->label('Export Regions')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportRegions()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Regions')
                ->icon('heroicon-o-map')
                ->badge(fn() => \App\Models\Region::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\Region::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\Region::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_cities' => Tab::make('With Cities')
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('cities'))
                ->badge(fn() => \App\Models\Region::has('cities')->count())
                ->badgeColor('info'),
        ];
    }

    protected function exportRegions(): void
    {
        $regions = \App\Models\Region::withCount('cities')->get();

        $filename = 'regions_export_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, ['ID', 'Name (EN)', 'Name (AR)', 'Code', 'Cities', 'Active', 'Order', 'Created At']);

        foreach ($regions as $region) {
            fputcsv($file, [
                $region->id,
                $region->getTranslation('name', 'en'),
                $region->getTranslation('name', 'ar'),
                $region->code,
                $region->cities_count,
                $region->is_active ? 'Yes' : 'No',
                $region->order,
                $region->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->success()
            ->title('Export Successful')
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
