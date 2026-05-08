<?php

namespace App\Filament\Admin\Resources\ExtraServiceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Hall;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\ExtraService;
use App\Filament\Admin\Resources\ExtraServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListExtraServices extends ListRecords
{
    protected static string $resource = ExtraServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->label(__('extra-service.actions.create')),

            Action::make('exportServices')
                ->label(__('extra-service.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportServices())
                ->requiresConfirmation()
                ->modalHeading(__('extra-service.actions.export_modal.heading'))
                ->modalDescription(__('extra-service.actions.export_modal.description'))
                ->modalSubmitActionLabel(__('extra-service.actions.export_modal.submit_label')),

            Action::make('bulkPriceUpdate')
                ->label(__('extra-service.actions.bulk_price_update'))
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->schema([
                    Select::make('update_type')
                        ->label(__('extra-service.actions.bulk_price_update_modal.update_type'))
                        ->options([
                            'percentage_increase' => __('extra-service.actions.bulk_price_update_modal.update_type_options.percentage_increase'),
                            'percentage_decrease' => __('extra-service.actions.bulk_price_update_modal.update_type_options.percentage_decrease'),
                            'fixed_increase' => __('extra-service.actions.bulk_price_update_modal.update_type_options.fixed_increase'),
                            'fixed_decrease' => __('extra-service.actions.bulk_price_update_modal.update_type_options.fixed_decrease'),
                        ])
                        ->required()
                        ->reactive(),

                    TextInput::make('value')
                        ->label(function ($get) {
                            $updateType = $get('update_type') ?? '';
                            $label = __('extra-service.actions.bulk_price_update_modal.value');
                            
                            if (str_contains($updateType, 'percentage')) {
                                return $label . ' (%)';
                            }
                            
                            return $label . ' (OMR)';
                        })
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->step(0.001),

                    Select::make('hall_id')
                        ->label(__('extra-service.actions.bulk_price_update_modal.hall_optional'))
                        ->options(Hall::where('is_active', true)->get()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText(__('extra-service.actions.bulk_price_update_modal.hall_helper')),
                ])
                ->action(function (array $data) {
                    $this->bulkUpdatePrices($data);
                }),

            Action::make('duplicateServices')
                ->label(__('extra-service.actions.duplicate_services'))
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->schema([
                    Select::make('source_hall_id')
                        ->label(__('extra-service.actions.duplicate_services_modal.source_hall'))
                        ->options(Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('target_hall_id')
                        ->label(__('extra-service.actions.duplicate_services_modal.target_hall'))
                        ->options(Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Checkbox::make('copy_inactive')
                        ->label(__('extra-service.actions.duplicate_services_modal.copy_inactive'))
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $this->duplicateServicesToHall($data);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('extra-service.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => ExtraService::count()),

            'active' => Tab::make(__('extra-service.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => ExtraService::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('extra-service.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => ExtraService::where('is_active', false)->count())
                ->badgeColor('danger'),

            'required' => Tab::make(__('extra-service.tabs.required'))
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_required', true))
                ->badge(fn() => ExtraService::where('is_required', true)->count())
                ->badgeColor('warning'),

            'per_person' => Tab::make(__('extra-service.tabs.per_person'))
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_person'))
                ->badge(fn() => ExtraService::where('unit', 'per_person')->count())
                ->badgeColor('info'),

            'per_item' => Tab::make(__('extra-service.tabs.per_item'))
                ->icon('heroicon-o-cube')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_item'))
                ->badge(fn() => ExtraService::where('unit', 'per_item')->count())
                ->badgeColor('info'),

            'per_hour' => Tab::make(__('extra-service.tabs.per_hour'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_hour'))
                ->badge(fn() => ExtraService::where('unit', 'per_hour')->count())
                ->badgeColor('info'),

            'fixed' => Tab::make(__('extra-service.tabs.fixed'))
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'fixed'))
                ->badge(fn() => ExtraService::where('unit', 'fixed')->count())
                ->badgeColor('success'),

            'with_image' => Tab::make(__('extra-service.tabs.with_image'))
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('image'))
                ->badge(fn() => ExtraService::whereNotNull('image')->count())
                ->badgeColor('purple'),

            'without_image' => Tab::make(__('extra-service.tabs.without_image'))
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('image'))
                ->badge(fn() => ExtraService::whereNull('image')->count())
                ->badgeColor('gray'),
        ];
    }

    protected function exportServices(): void
    {
        $services = ExtraService::with('hall')->get();

        $filename = 'extra_services_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            __('extra-service.export_headers.id'),
            __('extra-service.export_headers.hall'),
            __('extra-service.export_headers.name_en'),
            __('extra-service.export_headers.name_ar'),
            __('extra-service.export_headers.description_en'),
            __('extra-service.export_headers.description_ar'),
            __('extra-service.export_headers.price'),
            __('extra-service.export_headers.unit'),
            __('extra-service.export_headers.min_quantity'),
            __('extra-service.export_headers.max_quantity'),
            __('extra-service.export_headers.required'),
            __('extra-service.export_headers.active'),
            __('extra-service.export_headers.order'),
            __('extra-service.export_headers.has_image'),
            __('extra-service.export_headers.created_at'),
        ]);

        foreach ($services as $service) {
            fputcsv($file, [
                $service->id,
                $service->hall->name ?? __('extra-service.export_values.n_a'),
                $service->getTranslation('name', 'en'),
                $service->getTranslation('name', 'ar'),
                strip_tags($service->getTranslation('description', 'en')),
                strip_tags($service->getTranslation('description', 'ar')),
                number_format($service->price, 3),
                ucfirst(str_replace('_', ' ', $service->unit)),
                $service->minimum_quantity,
                $service->maximum_quantity ?? __('extra-service.export_values.unlimited'),
                $service->is_required ? __('extra-service.export_values.yes') : __('extra-service.export_values.no'),
                $service->is_active ? __('extra-service.export_values.yes') : __('extra-service.export_values.no'),
                $service->order,
                $service->image ? __('extra-service.export_values.yes') : __('extra-service.export_values.no'),
                $service->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title(__('extra-service.notifications.export_successful'))
            ->success()
            ->body(__('extra-service.notifications.export_body'))
            ->persistent()
            ->actions([
                Action::make('download')
                    ->label(__('extra-service.notifications.download'))
                    ->url(asset('storage/exports/' . $filename))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    protected function bulkUpdatePrices(array $data): void
    {
        $query = ExtraService::query();

        if (isset($data['hall_id'])) {
            $query->where('hall_id', $data['hall_id']);
        }

        $services = $query->get();
        $updatedCount = 0;

        foreach ($services as $service) {
            $newPrice = $service->price;

            switch ($data['update_type']) {
                case 'percentage_increase':
                    $newPrice = $service->price * (1 + ($data['value'] / 100));
                    break;
                case 'percentage_decrease':
                    $newPrice = $service->price * (1 - ($data['value'] / 100));
                    break;
                case 'fixed_increase':
                    $newPrice = $service->price + $data['value'];
                    break;
                case 'fixed_decrease':
                    $newPrice = $service->price - $data['value'];
                    break;
            }

            if ($newPrice >= 0) {
                $service->price = round($newPrice, 3);
                $service->save();
                $updatedCount++;

                activity()
                    ->performedOn($service)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_price' => $service->getOriginal('price'),
                        'new_price' => $newPrice,
                        'update_type' => $data['update_type'],
                    ])
                    ->log('Bulk price update');
            }
        }

        Notification::make()
            ->success()
            ->title(__('extra-service.notifications.prices_updated'))
            ->body(__('extra-service.notifications.services_updated', ['count' => $updatedCount]))
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function duplicateServicesToHall(array $data): void
    {
        $query = ExtraService::where('hall_id', $data['source_hall_id']);

        if (!$data['copy_inactive']) {
            $query->where('is_active', true);
        }

        $services = $query->get();
        $duplicatedCount = 0;

        foreach ($services as $service) {
            $newService = $service->replicate();
            $newService->hall_id = $data['target_hall_id'];
            $newService->save();

            // Copy image if exists
            if ($service->image) {
                $newService->image = $service->image;
                $newService->save();
            }

            $duplicatedCount++;
        }

        Notification::make()
            ->success()
            ->title(__('extra-service.notifications.services_duplicated'))
            ->body(__('extra-service.notifications.services_duplicated_body', ['count' => $duplicatedCount]))
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add widgets for statistics if needed
        ];
    }
}