<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallResource\Pages;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Admin\Resources\HallResource\Widgets\HallStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ListHalls Page - Admin Panel
 * 
 * Displays a list of all halls with filtering, exporting, and bulk operations.
 * 
 * @package App\Filament\Admin\Resources\HallResource\Pages
 */
class ListHalls extends ListRecords
{
    /**
     * The resource this page belongs to.
     */
    protected static string $resource = HallResource::class;

    /**
     * Get the header actions for this page.
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportHalls')
                ->label(__('admin.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportHalls())
                ->requiresConfirmation()
                ->modalHeading(__('admin.actions.export_modal_heading'))
                ->modalDescription(__('admin.actions.export_modal_description'))
                ->modalSubmitActionLabel(__('admin.actions.export')),

            Actions\Action::make('bulkPriceUpdate')
                ->label(__('admin.actions.bulk_price_update'))
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('city_id')
                        ->label(__('admin.fields.city'))
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Select::make('update_type')
                        ->label(__('admin.fields.update_type'))
                        ->options([
                            'percentage_increase' => __('admin.options.percentage_increase'),
                            'percentage_decrease' => __('admin.options.percentage_decrease'),
                            'fixed_increase' => __('admin.options.fixed_increase'),
                            'fixed_decrease' => __('admin.options.fixed_decrease'),
                        ])
                        ->required()
                        ->reactive(),

                    \Filament\Forms\Components\TextInput::make('value')
                        ->label(fn($get) => str_contains($get('update_type') ?? '', 'percentage')
                            ? __('admin.fields.percentage')
                            : __('admin.fields.amount'))
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->step(0.001),
                ])
                ->action(function (array $data) {
                    $this->bulkUpdatePrices($data);
                })
                ->modalHeading(__('admin.actions.bulk_price_modal_heading'))
                ->modalDescription(__('admin.actions.bulk_price_modal_description')),

            Actions\Action::make('generateSlugs')
                ->label(__('admin.actions.generate_slugs'))
                ->icon('heroicon-o-link')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading(__('admin.actions.generate_slugs_modal_heading'))
                ->modalDescription(__('admin.actions.generate_slugs_modal_description'))
                ->action(function () {
                    $this->generateMissingSlugs();
                }),

            Actions\Action::make('bulkFeature')
                ->label(__('admin.actions.bulk_feature'))
                ->icon('heroicon-o-star')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('action')
                        ->label(__('admin.fields.action'))
                        ->options([
                            'mark_featured' => __('admin.options.mark_featured'),
                            'unmark_featured' => __('admin.options.unmark_featured'),
                        ])
                        ->required(),

                    \Filament\Forms\Components\Select::make('city_id')
                        ->label(__('admin.fields.city_filter'))
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->bulkFeatureManagement($data);
                }),

            Actions\Action::make('syncAvailability')
                ->label(__('admin.actions.sync_availability'))
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading(__('admin.actions.sync_availability_modal_heading'))
                ->modalDescription(__('admin.actions.sync_availability_modal_description'))
                ->action(function () {
                    $this->syncHallAvailability();
                }),

            Actions\Action::make('bulkActivation')
                ->label(__('admin.actions.bulk_activation'))
                ->icon('heroicon-o-power')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label(__('admin.fields.status'))
                        ->options([
                            'activate' => __('admin.options.activate'),
                            'deactivate' => __('admin.options.deactivate'),
                        ])
                        ->required(),

                    \Filament\Forms\Components\Select::make('city_id')
                        ->label(__('admin.fields.city_filter'))
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $this->bulkActivation($data);
                }),
        ];
    }

    /**
     * Get the tabs for filtering halls.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.tabs.all'))
                ->icon('heroicon-o-building-office-2')
                ->badge(fn() => \App\Models\Hall::count()),

            'active' => Tab::make(__('admin.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\Hall::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('admin.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\Hall::where('is_active', false)->count())
                ->badgeColor('danger'),

            'featured' => Tab::make(__('admin.tabs.featured'))
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_featured', true))
                ->badge(fn() => \App\Models\Hall::where('is_featured', true)->count())
                ->badgeColor('warning'),

            'pending_approval' => Tab::make(__('admin.tabs.pending_approval'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('requires_approval', true))
                ->badge(fn() => \App\Models\Hall::where('requires_approval', true)->count())
                ->badgeColor('info'),

            'high_capacity' => Tab::make(__('admin.tabs.high_capacity'))
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('capacity_max', '>=', 500))
                ->badge(fn() => \App\Models\Hall::where('capacity_max', '>=', 500)->count())
                ->badgeColor('purple'),

            'premium_price' => Tab::make(__('admin.tabs.premium_price'))
                ->icon('heroicon-o-currency-dollar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('price_per_slot', '>=', 1000))
                ->badge(fn() => \App\Models\Hall::where('price_per_slot', '>=', 1000)->count())
                ->badgeColor('success'),

            'highly_rated' => Tab::make(__('admin.tabs.highly_rated'))
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('average_rating', '>=', 4.5))
                ->badge(fn() => \App\Models\Hall::where('average_rating', '>=', 4.5)->count())
                ->badgeColor('warning'),

            'with_video' => Tab::make(__('admin.tabs.with_video'))
                ->icon('heroicon-o-video-camera')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('video_url'))
                ->badge(fn() => \App\Models\Hall::whereNotNull('video_url')->count())
                ->badgeColor('info'),

            'incomplete' => Tab::make(__('admin.tabs.incomplete'))
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

            'no_bookings' => Tab::make(__('admin.tabs.no_bookings'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('total_bookings', 0))
                ->badge(fn() => \App\Models\Hall::where('total_bookings', 0)->count())
                ->badgeColor('gray'),
        ];
    }

    /**
     * Export halls data to CSV file.
     * 
     * FIX: Uses withCount('bookings') to get the actual count of related bookings
     * instead of relying on the cached 'total_bookings' column which may be out of sync.
     *
     * @return void
     */
    protected function exportHalls(): void
    {
        try {
            // FIX: Added withCount('bookings') to dynamically count related bookings
            // This ensures we get the actual booking count from the relationship,
            // not the potentially stale cached 'total_bookings' column
            $halls = \App\Models\Hall::with(['city', 'owner'])
                ->withCount('bookings')  // Adds 'bookings_count' attribute to each Hall
                ->get();

            // Generate unique filename with timestamp
            $filename = 'halls_export_' . now()->format('Y_m_d_His') . '.csv';
            $path = storage_path('app/public/exports/' . $filename);

            // Ensure exports directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $file = fopen($path, 'w');

            // Add UTF-8 BOM for better Excel compatibility with Arabic text
            fputs($file, "\xEF\xBB\xBF");

            // Write CSV header row
            fputcsv($file, [
                'ID',
                __('admin.export.name_en'),
                __('admin.export.name_ar'),
                __('admin.export.slug'),
                __('admin.export.city'),
                __('admin.export.owner'),
                __('admin.export.address'),
                __('admin.export.latitude'),
                __('admin.export.longitude'),
                __('admin.export.capacity_min'),
                __('admin.export.capacity_max'),
                __('admin.export.base_price'),
                __('admin.export.phone'),
                __('admin.export.email'),
                __('admin.export.total_bookings'),
                __('admin.export.average_rating'),
                __('admin.export.featured'),
                __('admin.export.active'),
                __('admin.export.created_at'),
            ]);

            // Write data rows
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
                    // PHP 8.4 strict types: Cast to float for number_format()
                    number_format((float) $hall->price_per_slot, 3),
                    $hall->phone,
                    $hall->email ?? '',
                    // FIX: Use bookings_count from withCount() instead of cached total_bookings
                    // This ensures we export the actual count of related bookings
                    $hall->bookings_count ?? 0,
                    $hall->average_rating ?? 0,
                    $hall->is_featured ? __('admin.yes') : __('admin.no'),
                    $hall->is_active ? __('admin.yes') : __('admin.no'),
                    $hall->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);

            // Show success notification with download link
            Notification::make()
                ->title(__('admin.notifications.export_success'))
                ->success()
                ->body(__('admin.notifications.export_success_body'))
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label(__('admin.actions.download'))
                        ->url(asset('storage/exports/' . $filename))
                        ->openUrlInNewTab(),
                ])
                ->send();

        } catch (\Exception $e) {
            // Handle export errors gracefully
            Notification::make()
                ->title(__('admin.notifications.export_error'))
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Bulk update prices for halls based on city filter and update type.
     *
     * @param array<string, mixed> $data Form data containing city_id, update_type, and value
     * @return void
     */
    protected function bulkUpdatePrices(array $data): void
    {
        DB::beginTransaction();

        try {
            $query = \App\Models\Hall::query();

            // Apply city filter if provided
            if (isset($data['city_id'])) {
                $query->where('city_id', $data['city_id']);
            }

            $halls = $query->get();
            $updatedCount = 0;

            foreach ($halls as $hall) {
                $newPrice = $hall->price_per_slot;

                // Calculate new price based on update type
                switch ($data['update_type']) {
                    case 'percentage_increase':
                        $newPrice = $hall->price_per_slot * (1 + ($data['value'] / 100));
                        break;
                    case 'percentage_decrease':
                        $newPrice = max(0, $hall->price_per_slot * (1 - ($data['value'] / 100)));
                        break;
                    case 'fixed_increase':
                        $newPrice = $hall->price_per_slot + $data['value'];
                        break;
                    case 'fixed_decrease':
                        $newPrice = max(0, $hall->price_per_slot - $data['value']);
                        break;
                }

                $oldPrice = $hall->price_per_slot;
                $hall->price_per_slot = round($newPrice, 3);
                $hall->save();
                $updatedCount++;

                // Log the activity for audit trail
                activity()
                    ->performedOn($hall)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'update_type' => $data['update_type'],
                        'value' => $data['value'],
                    ])
                    ->log('Bulk price update');
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('admin.notifications.prices_updated'))
                ->body(__('admin.notifications.prices_updated_body', ['count' => $updatedCount]))
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('admin.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Generate missing slugs for halls that don't have one.
     *
     * @return void
     */
    protected function generateMissingSlugs(): void
    {
        DB::beginTransaction();

        try {
            $halls = \App\Models\Hall::whereNull('slug')->orWhere('slug', '')->get();
            $updated = 0;

            foreach ($halls as $hall) {
                $slug = Str::slug($hall->getTranslation('name', 'en'));
                $baseSlug = $slug;
                $counter = 1;

                // Ensure unique slug
                while (\App\Models\Hall::where('slug', $slug)->where('id', '!=', $hall->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $hall->update(['slug' => $slug]);
                $updated++;
            }

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('admin.notifications.slugs_generated'))
                ->body(__('admin.notifications.slugs_generated_body', ['count' => $updated]))
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('admin.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Bulk manage featured status for halls.
     *
     * @param array<string, mixed> $data Form data containing action and city_id
     * @return void
     */
    protected function bulkFeatureManagement(array $data): void
    {
        DB::beginTransaction();

        try {
            $query = \App\Models\Hall::query();

            if (isset($data['city_id'])) {
                $query->where('city_id', $data['city_id']);
            }

            $isFeatured = $data['action'] === 'mark_featured';
            $updated = $query->update(['is_featured' => $isFeatured]);

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('admin.notifications.feature_updated'))
                ->body(__('admin.notifications.feature_updated_body', ['count' => $updated]))
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('admin.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Bulk activate or deactivate halls.
     *
     * @param array<string, mixed> $data Form data containing status and city_id
     * @return void
     */
    protected function bulkActivation(array $data): void
    {
        DB::beginTransaction();

        try {
            $query = \App\Models\Hall::query();

            if (isset($data['city_id'])) {
                $query->where('city_id', $data['city_id']);
            }

            $isActive = $data['status'] === 'activate';
            $updated = $query->update(['is_active' => $isActive]);

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('admin.notifications.activation_updated'))
                ->body(__('admin.notifications.activation_updated_body', ['count' => $updated]))
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('admin.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Sync hall availability for the next 3 months.
     * Creates availability records for all active halls if they don't exist.
     *
     * @return void
     */
    protected function syncHallAvailability(): void
    {
        DB::beginTransaction();

        try {
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

            DB::commit();

            Notification::make()
                ->success()
                ->title(__('admin.notifications.availability_synced'))
                ->body(__('admin.notifications.availability_synced_body', ['count' => $createdCount]))
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title(__('admin.notifications.update_error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Get the header widgets for this page.
     *
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            HallStatsWidget::class,
        ];
    }
}
