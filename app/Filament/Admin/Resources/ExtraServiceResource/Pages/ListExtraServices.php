<?php

namespace App\Filament\Admin\Resources\ExtraServiceResource\Pages;

use App\Filament\Admin\Resources\ExtraServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
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
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportServices')
                ->label('Export Services')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportServices())
                ->requiresConfirmation()
                ->modalHeading('Export Extra Services')
                ->modalDescription('Export all extra services data to CSV.')
                ->modalSubmitActionLabel('Export'),

            Actions\Action::make('bulkPriceUpdate')
                ->label('Bulk Price Update')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form([
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

                    \Filament\Forms\Components\Select::make('hall_id')
                        ->label('Apply to Hall (Optional)')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText('Leave empty to apply to all halls'),
                ])
                ->action(function (array $data) {
                    $this->bulkUpdatePrices($data);
                }),

            Actions\Action::make('duplicateServices')
                ->label('Duplicate to Another Hall')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('source_hall_id')
                        ->label('Source Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Select::make('target_hall_id')
                        ->label('Target Hall')
                        ->options(\App\Models\Hall::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    \Filament\Forms\Components\Checkbox::make('copy_inactive')
                        ->label('Include Inactive Services')
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
            'all' => Tab::make('All Services')
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\ExtraService::count()),

            'active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\ExtraService::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\ExtraService::where('is_active', false)->count())
                ->badgeColor('danger'),

            'required' => Tab::make('Required Services')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_required', true))
                ->badge(fn() => \App\Models\ExtraService::where('is_required', true)->count())
                ->badgeColor('warning'),

            'per_person' => Tab::make('Per Person')
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_person'))
                ->badge(fn() => \App\Models\ExtraService::where('unit', 'per_person')->count())
                ->badgeColor('info'),

            'per_item' => Tab::make('Per Item')
                ->icon('heroicon-o-cube')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_item'))
                ->badge(fn() => \App\Models\ExtraService::where('unit', 'per_item')->count())
                ->badgeColor('info'),

            'per_hour' => Tab::make('Per Hour')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'per_hour'))
                ->badge(fn() => \App\Models\ExtraService::where('unit', 'per_hour')->count())
                ->badgeColor('info'),

            'fixed' => Tab::make('Fixed Price')
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('unit', 'fixed'))
                ->badge(fn() => \App\Models\ExtraService::where('unit', 'fixed')->count())
                ->badgeColor('success'),

            'with_image' => Tab::make('With Images')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('image'))
                ->badge(fn() => \App\Models\ExtraService::whereNotNull('image')->count())
                ->badgeColor('purple'),

            'without_image' => Tab::make('Without Images')
                ->icon('heroicon-o-photo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('image'))
                ->badge(fn() => \App\Models\ExtraService::whereNull('image')->count())
                ->badgeColor('gray'),
        ];
    }

    protected function exportServices(): void
    {
        $services = \App\Models\ExtraService::with('hall')->get();

        $filename = 'extra_services_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        fputcsv($file, [
            'ID',
            'Hall',
            'Name (EN)',
            'Name (AR)',
            'Description (EN)',
            'Description (AR)',
            'Price (OMR)',
            'Unit',
            'Min Quantity',
            'Max Quantity',
            'Required',
            'Active',
            'Order',
            'Has Image',
            'Created At',
        ]);

        foreach ($services as $service) {
            fputcsv($file, [
                $service->id,
                $service->hall->name ?? 'N/A',
                $service->getTranslation('name', 'en'),
                $service->getTranslation('name', 'ar'),
                strip_tags($service->getTranslation('description', 'en')),
                strip_tags($service->getTranslation('description', 'ar')),
                number_format($service->price, 3),
                ucfirst(str_replace('_', ' ', $service->unit)),
                $service->minimum_quantity,
                $service->maximum_quantity ?? 'Unlimited',
                $service->is_required ? 'Yes' : 'No',
                $service->is_active ? 'Yes' : 'No',
                $service->order,
                $service->image ? 'Yes' : 'No',
                $service->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Extra services exported successfully.')
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
        $query = \App\Models\ExtraService::query();

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
            ->title('Prices Updated')
            ->body("{$updatedCount} service(s) updated successfully.")
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function duplicateServicesToHall(array $data): void
    {
        $query = \App\Models\ExtraService::where('hall_id', $data['source_hall_id']);

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
            ->title('Services Duplicated')
            ->body("{$duplicatedCount} service(s) duplicated successfully.")
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
