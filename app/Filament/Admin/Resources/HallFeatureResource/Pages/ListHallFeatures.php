<?php

namespace App\Filament\Admin\Resources\HallFeatureResource\Pages;

use App\Filament\Admin\Resources\HallFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListHallFeatures extends ListRecords
{
    protected static string $resource = HallFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportFeatures')
                ->label('Export Features')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportFeatures())
                ->requiresConfirmation()
                ->modalHeading('Export Hall Features')
                ->modalDescription('Export all hall features to CSV.')
                ->modalSubmitActionLabel('Export'),

            Actions\Action::make('bulkActivate')
                ->label('Bulk Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Activate All Features')
                ->modalDescription('This will activate all currently inactive features.')
                ->action(function () {
                    $updated = \App\Models\HallFeature::where('is_active', false)->update([
                        'is_active' => true
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Features Activated')
                        ->body("{$updated} feature(s) have been activated.")
                        ->send();

                    Cache::tags(['features'])->flush();
                    $this->redirect(static::getUrl());
                }),

            Actions\Action::make('reorderFeatures')
                ->label('Auto-Reorder')
                ->icon('heroicon-o-bars-3-bottom-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Auto-Reorder Features')
                ->modalDescription('This will automatically reorder all features alphabetically.')
                ->action(function () {
                    $features = \App\Models\HallFeature::orderBy('name->en')->get();

                    $order = 0;
                    foreach ($features as $feature) {
                        $feature->update(['order' => $order]);
                        $order++;
                    }

                    Notification::make()
                        ->success()
                        ->title('Features Reordered')
                        ->body("{$features->count()} feature(s) have been reordered.")
                        ->send();

                    Cache::tags(['features'])->flush();
                    $this->redirect(static::getUrl());
                }),

            Actions\Action::make('generateSlugs')
                ->label('Generate Missing Slugs')
                ->icon('heroicon-o-link')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $features = \App\Models\HallFeature::whereNull('slug')->orWhere('slug', '')->get();
                    $updated = 0;

                    foreach ($features as $feature) {
                        $slug = Str::slug($feature->getTranslation('name', 'en'));

                        // Ensure unique slug
                        $baseSlug = $slug;
                        $counter = 1;
                        while (\App\Models\HallFeature::where('slug', $slug)->where('id', '!=', $feature->id)->exists()) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        }

                        $feature->update(['slug' => $slug]);
                        $updated++;
                    }

                    Notification::make()
                        ->success()
                        ->title('Slugs Generated')
                        ->body("{$updated} slug(s) have been generated.")
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Features')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\HallFeature::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\HallFeature::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\HallFeature::where('is_active', false)->count())
                ->badgeColor('danger'),

            'with_icons' => Tab::make('With Icons')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('icon')->where('icon', '!=', ''))
                ->badge(fn() => \App\Models\HallFeature::whereNotNull('icon')->where('icon', '!=', '')->count())
                ->badgeColor('info'),

            'without_icons' => Tab::make('Without Icons')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('icon')->orWhere('icon', ''))
                ->badge(fn() => \App\Models\HallFeature::whereNull('icon')->orWhere('icon', '')->count())
                ->badgeColor('warning'),

            'with_description' => Tab::make('With Description')
                ->icon('heroicon-o-document-text')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('description->en'))
                ->badge(fn() => \App\Models\HallFeature::whereNotNull('description->en')->count())
                ->badgeColor('purple'),

            'popular' => Tab::make('Most Used')
                ->icon('heroicon-o-fire')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('halls', '>=', 5))
                ->badge(fn() => \App\Models\HallFeature::has('halls', '>=', 5)->count())
                ->badgeColor('orange'),
        ];
    }

    protected function exportFeatures(): void
    {
        $features = \App\Models\HallFeature::withCount('halls')->get();

        $filename = 'hall_features_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'ID',
            'Name (EN)',
            'Name (AR)',
            'Slug',
            'Icon',
            'Description (EN)',
            'Description (AR)',
            'Halls Count',
            'Active',
            'Order',
            'Created At',
        ]);

        foreach ($features as $feature) {
            fputcsv($file, [
                $feature->id,
                $feature->getTranslation('name', 'en'),
                $feature->getTranslation('name', 'ar'),
                $feature->slug ?? '',
                $feature->icon ?? '',
                $feature->getTranslation('description', 'en') ?? '',
                $feature->getTranslation('description', 'ar') ?? '',
                $feature->halls_count ?? 0,
                $feature->is_active ? 'Yes' : 'No',
                $feature->order,
                $feature->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Hall features exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add statistics widgets here if needed
        ];
    }
}
