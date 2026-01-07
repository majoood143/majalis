<?php

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListHalls extends ListRecords
{
    protected static string $resource = HallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportHalls')
                ->label('Export Halls')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportHalls())
                ->requiresConfirmation()
                ->modalHeading('Export Halls Data')
                ->modalDescription('Export all halls data to CSV.')
                ->modalSubmitActionLabel('Export'),

            Actions\Action::make('bulkPriceUpdate')
                ->label('Bulk Price Update')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('city_id')
                        ->label('Filter by City (Optional)')
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Select::make('update_type')
                        ->label('Update Type')
                        ->options([
                            'percentage_increase' => 'Percentage Increase',
                            'percentage_decrease' => 'Percentage Decrease',
                            'fixed_increase' => 'Fixed Amount Increase',
                            'fixed_decrease' => 'Fixed Amount Decrease',
                        ])
                        ->required()
                        ->reactive(),

                    \Filament\Forms\Components\TextInput::make('value')
                        ->label(fn($get) => str_contains($get('update_type') ?? '', 'percentage') ? 'Percentage (%)' : 'Amount (OMR)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->step(0.001),
                ])
                ->action(function (array $data) {
                    $this->bulkUpdatePrices($data);
                }),

            Actions\Action::make('generateSlugs')
                ->label('Generate Missing Slugs')
                ->icon('heroicon-o-link')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $this->generateMissingSlugs();
                }),

            Actions\Action::make('bulkFeature')
                ->label('Bulk Feature Management')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('action')
                        ->options([
                            'mark_featured' => 'Mark as Featured',
                            'unmark_featured' => 'Remove Featured Status',
                        ])
                        ->required(),

                    \Filament\Forms\Components\Select::make('city_id')
                        ->label('Filter by City (Optional)')
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->bulkFeatureManagement($data);
                }),

            Actions\Action::make('syncAvailability')
                ->label('Sync Availability')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate Availability Records')
                ->modalDescription('Generate availability slots for all halls for the next 3 months.')
                ->action(function () {
                    $this->syncHallAvailability();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Halls')
                ->icon('heroicon-o-building-office-2')
                ->badge(fn() => \App\Models\Hall::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\Hall::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\Hall::where('is_active', false)->count())
                ->badgeColor('danger'),

            'featured' => Tab::make('Featured')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_featured', true))
                ->badge(fn() => \App\Models\Hall::where('is_featured', true)->count())
                ->badgeColor('warning'),

            'pending_approval' => Tab::make('Pending Approval')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('requires_approval', true))
                ->badge(fn() => \App\Models\Hall::where('requires_approval', true)->count())
                ->badgeColor('info'),

            'high_capacity' => Tab::make('High Capacity (500+)')
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('capacity_max', '>=', 500))
                ->badge(fn() => \App\Models\Hall::where('capacity_max', '>=', 500)->count())
                ->badgeColor('purple'),

            'premium_price' => Tab::make('Premium (1000+ OMR)')
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('price_per_slot', '>=', 1000))
                ->badge(fn() => \App\Models\Hall::where('price_per_slot', '>=', 1000)->count())
                ->badgeColor('success'),

            'highly_rated' => Tab::make('Highly Rated (4.5+)')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('average_rating', '>=', 4.5))
                ->badge(fn() => \App\Models\Hall::where('average_rating', '>=', 4.5)->count())
                ->badgeColor('warning'),

            'with_video' => Tab::make('With Video')
                ->icon('heroicon-o-video-camera')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('video_url'))
                ->badge(fn() => \App\Models\Hall::whereNotNull('video_url')->count())
                ->badgeColor('info'),

            'incomplete' => Tab::make('Incomplete Profile')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where(function ($q) {
                    $q->whereNull('featured_image')
                        ->orWhereNull('description->en')
                        ->orWhereNull('latitude')
                        ->orWhereNull('longitude');
                }))
                ->badge(fn() => \App\Models\Hall::where(function ($q) {
                    $q->whereNull('featured_image')
                        ->orWhereNull('description->en')
                        ->orWhereNull('latitude')
                        ->orWhereNull('longitude');
                })->count())
                ->badgeColor('danger'),
        ];
    }

    protected function exportHalls(): void
    {
        $halls = \App\Models\Hall::with(['city', 'owner'])->get();

        $filename = 'halls_export_' . now()->format('Y_m_d_His') . '.csv';
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
            'City',
            'Owner',
            'Address',
            'Latitude',
            'Longitude',
            'Min Capacity',
            'Max Capacity',
            'Base Price',
            'Phone',
            'Email',
            'Total Bookings',
            'Average Rating',
            'Featured',
            'Active',
            'Created At',
        ]);

        foreach ($halls as $hall) {
            fputcsv($file, [
                $hall->id,
                $hall->getTranslation('name', 'en'),
                $hall->getTranslation('name', 'ar'),
                $hall->slug ?? '',
                $hall->city->name ?? 'N/A',
                $hall->owner->name ?? 'N/A',
                $hall->address,
                $hall->latitude ?? '',
                $hall->longitude ?? '',
                $hall->capacity_min,
                $hall->capacity_max,
                number_format($hall->price_per_slot, 3),
                $hall->phone,
                $hall->email ?? '',
                $hall->total_bookings ?? 0,
                $hall->average_rating ?? 0,
                $hall->is_featured ? 'Yes' : 'No',
                $hall->is_active ? 'Yes' : 'No',
                $hall->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Halls data exported successfully.')
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download File')
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function bulkUpdatePrices(array $data): void
    {
        $query = \App\Models\Hall::query();

        if (isset($data['city_id'])) {
            $query->where('city_id', $data['city_id']);
        }

        $halls = $query->get();
        $updatedCount = 0;

        foreach ($halls as $hall) {
            $newPrice = $hall->price_per_slot;

            switch ($data['update_type']) {
                case 'percentage_increase':
                    $newPrice = $hall->price_per_slot * (1 + ($data['value'] / 100));
                    break;
                case 'percentage_decrease':
                    $newPrice = $hall->price_per_slot * (1 - ($data['value'] / 100));
                    break;
                case 'fixed_increase':
                    $newPrice = $hall->price_per_slot + $data['value'];
                    break;
                case 'fixed_decrease':
                    $newPrice = $hall->price_per_slot - $data['value'];
                    break;
            }

            if ($newPrice >= 0) {
                $hall->price_per_slot = round($newPrice, 3);
                $hall->save();
                $updatedCount++;

                activity()
                    ->performedOn($hall)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_price' => $hall->getOriginal('price_per_slot'),
                        'new_price' => $newPrice,
                        'update_type' => $data['update_type'],
                    ])
                    ->log('Bulk price update');
            }
        }

        //Cache::tags(['halls'])->flush();

        Notification::make()
            ->success()
            ->title('Prices Updated')
            ->body("{$updatedCount} hall(s) updated successfully.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function generateMissingSlugs(): void
    {
        $halls = \App\Models\Hall::whereNull('slug')->orWhere('slug', '')->get();
        $updated = 0;

        foreach ($halls as $hall) {
            $slug = Str::slug($hall->getTranslation('name', 'en'));
            $baseSlug = $slug;
            $counter = 1;

            while (\App\Models\Hall::where('slug', $slug)->where('id', '!=', $hall->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $hall->update(['slug' => $slug]);
            $updated++;
        }

        Notification::make()
            ->success()
            ->title('Slugs Generated')
            ->body("{$updated} slug(s) have been generated.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function bulkFeatureManagement(array $data): void
    {
        $query = \App\Models\Hall::query();

        if (isset($data['city_id'])) {
            $query->where('city_id', $data['city_id']);
        }

        $isFeatured = $data['action'] === 'mark_featured';
        $updated = $query->update(['is_featured' => $isFeatured]);

        //Cache::tags(['halls'])->flush();

        Notification::make()
            ->success()
            ->title('Featured Status Updated')
            ->body("{$updated} hall(s) updated successfully.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function syncHallAvailability(): void
    {
        $halls = \App\Models\Hall::where('is_active', true)->get();
        $createdCount = 0;

        $startDate = now();
        $endDate = now()->addMonths(3);

        foreach ($halls as $hall) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                foreach (['morning', 'afternoon', 'evening', 'full_day'] as $timeSlot) {
                    $exists = \App\Models\HallAvailability::where('hall_id', $hall->id)
                        ->where('date', $currentDate->toDateString())
                        ->where('time_slot', $timeSlot)
                        ->exists();

                    if (!$exists) {
                        \App\Models\HallAvailability::create([
                            'hall_id' => $hall->id,
                            'date' => $currentDate->toDateString(),
                            'time_slot' => $timeSlot,
                            'is_available' => true,
                        ]);

                        $createdCount++;
                    }
                }

                $currentDate->addDay();
            }
        }

        Notification::make()
            ->success()
            ->title('Availability Synced')
            ->body("{$createdCount} availability slot(s) created.")
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add hall statistics widgets here
        ];
    }
}
