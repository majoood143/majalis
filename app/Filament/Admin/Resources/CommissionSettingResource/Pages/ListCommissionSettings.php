<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListCommissionSettings extends ListRecords
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('exportCommissions')
                ->label(__('commission-setting.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportCommissions())
                ->requiresConfirmation()
                ->modalHeading('Export Commission Settings')
                ->modalDescription('Export all commission settings to CSV file.')
                ->modalSubmitActionLabel('Export'),

            Actions\Action::make('bulkActivate')
                ->label(__('commission-setting.bulk_activate'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Activate All Commission Settings')
                ->modalDescription('This will activate all currently inactive commission settings.')
                ->action(function () {
                    $updated = \App\Models\CommissionSetting::where('is_active', false)->update([
                        'is_active' => true
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Commission Settings Activated')
                        ->body("{$updated} commission setting(s) have been activated.")
                        ->send();

                    $this->redirect(static::getUrl());
                }),

            Actions\Action::make('cleanupExpired')
                ->label(__('commission-setting.cleanup_expired'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Expired Commission Settings')
                ->modalDescription('This will permanently delete all expired commission settings.')
                ->action(function () {
                    $deleted = \App\Models\CommissionSetting::where('effective_to', '<', now())
                        ->where('is_active', false)
                        ->delete();

                    Notification::make()
                        ->success()
                        ->title('Cleanup Completed')
                        ->body("{$deleted} expired commission setting(s) have been deleted.")
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('commission-setting.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\CommissionSetting::count()),

            'active' => Tab::make(__('commission-setting.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\CommissionSetting::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('commission-setting.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\CommissionSetting::where('is_active', false)->count())
                ->badgeColor('danger'),

            'global' => Tab::make(__('commission-setting.tabs.global'))
                ->icon('heroicon-o-globe-alt')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('hall_id')->whereNull('owner_id'))
                ->badge(fn() => \App\Models\CommissionSetting::whereNull('hall_id')->whereNull('owner_id')->count())
                ->badgeColor('primary'),

            'hall_specific' => Tab::make(__('commission-setting.tabs.hall_specific'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('hall_id'))
                ->badge(fn() => \App\Models\CommissionSetting::whereNotNull('hall_id')->count())
                ->badgeColor('success'),

            'owner_specific' => Tab::make(__('commission-setting.tabs.owner_specific'))
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('owner_id')->whereNull('hall_id'))
                ->badge(fn() => \App\Models\CommissionSetting::whereNotNull('owner_id')->whereNull('hall_id')->count())
                ->badgeColor('warning'),

            'percentage' => Tab::make(__('commission-setting.tabs.percentage'))
                ->icon('heroicon-o-percent-badge')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('commission_type', 'percentage'))
                ->badge(fn() => \App\Models\CommissionSetting::where('commission_type', 'percentage')->count())
                ->badgeColor('info'),

            'fixed' => Tab::make(__('commission-setting.tabs.fixed'))
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('commission_type', 'fixed'))
                ->badge(fn() => \App\Models\CommissionSetting::where('commission_type', 'fixed')->count())
                ->badgeColor('info'),

            'expiring_soon' => Tab::make(__('commission-setting.tabs.expiring_soon'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->whereNotNull('effective_to')
                        ->where('effective_to', '>', now())
                        ->where('effective_to', '<=', now()->addDays(30))
                )
                ->badge(
                    fn() => \App\Models\CommissionSetting::whereNotNull('effective_to')
                        ->where('effective_to', '>', now())
                        ->where('effective_to', '<=', now()->addDays(30))
                        ->count()
                )
                ->badgeColor('warning'),

            'expired' => Tab::make(__('commission-setting.tabs.expired'))
                ->icon('heroicon-o-x-mark')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->whereNotNull('effective_to')
                        ->where('effective_to', '<', now())
                )
                ->badge(
                    fn() => \App\Models\CommissionSetting::whereNotNull('effective_to')
                        ->where('effective_to', '<', now())
                        ->count()
                )
                ->badgeColor('danger'),
        ];
    }

    protected function exportCommissions(): void
    {
        $commissions = \App\Models\CommissionSetting::with(['hall', 'owner'])->get();

        $filename = 'commission_settings_' . now()->format('Y_m_d_His') . '.csv';
        $path = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $file = fopen($path, 'w');

        // Add CSV headers
        fputcsv($file, [
            'ID',
            'Scope Type',
            'Hall',
            'Owner',
            'Name (EN)',
            'Name (AR)',
            'Commission Type',
            'Commission Value',
            'Effective From',
            'Effective To',
            'Active',
            'Created At',
        ]);

        foreach ($commissions as $commission) {
            $scopeType = 'Global';
            $hall = '';
            $owner = '';

            if ($commission->hall_id) {
                $scopeType = 'Hall-Specific';
                $hall = $commission->hall->name ?? '';
            } elseif ($commission->owner_id) {
                $scopeType = 'Owner-Specific';
                $owner = $commission->owner->name ?? '';
            }

            fputcsv($file, [
                $commission->id,
                $scopeType,
                $hall,
                $owner,
                $commission->getTranslation('name', 'en') ?? '',
                $commission->getTranslation('name', 'ar') ?? '',
                ucfirst($commission->commission_type->value ?? $commission->commission_type),
                $commission->commission_value,
                $commission->effective_from?->format('Y-m-d') ?? 'Immediate',
                $commission->effective_to?->format('Y-m-d') ?? 'Indefinite',
                $commission->is_active ? 'Yes' : 'No',
                $commission->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        Notification::make()
            ->title('Export Successful')
            ->success()
            ->body('Commission settings exported successfully.')
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
            // Add widgets here for statistics if needed
        ];
    }
}
