<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettingResource\Pages;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

/**
 * List page for Service Fee Settings.
 *
 * Provides tabs for filtering by scope and status, plus bulk actions.
 */
class ListServiceFeeSettings extends ListRecords
{
    protected static string $resource = ServiceFeeSettingResource::class;

    /**
     * Header actions: Create + Cleanup.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('cleanupExpired')
                ->label(__('service-fee.cleanup_expired'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('service-fee.cleanup_modal_title'))
                ->modalDescription(__('service-fee.cleanup_modal_desc'))
                ->action(function () {
                    $deleted = \App\Models\ServiceFeeSetting::where('effective_to', '<', now())
                        ->where('is_active', false)
                        ->delete();

                    Notification::make()
                        ->success()
                        ->title(__('service-fee.cleanup_done'))
                        ->body(__('service-fee.cleanup_done_body', ['count' => $deleted]))
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    /**
     * Filter tabs matching CommissionSetting pattern.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('service-fee.tabs.all'))
                ->icon('heroicon-o-squares-2x2')
                ->badge(fn() => \App\Models\ServiceFeeSetting::count()),

            'active' => Tab::make(__('service-fee.tabs.active'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->badge(fn() => \App\Models\ServiceFeeSetting::where('is_active', true)->count())
                ->badgeColor('success'),

            'inactive' => Tab::make(__('service-fee.tabs.inactive'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->badge(fn() => \App\Models\ServiceFeeSetting::where('is_active', false)->count())
                ->badgeColor('danger'),

            'global' => Tab::make(__('service-fee.tabs.global'))
                ->icon('heroicon-o-globe-alt')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('hall_id')->whereNull('owner_id'))
                ->badge(fn() => \App\Models\ServiceFeeSetting::whereNull('hall_id')->whereNull('owner_id')->count())
                ->badgeColor('primary'),

            'hall_specific' => Tab::make(__('service-fee.tabs.hall_specific'))
                ->icon('heroicon-o-building-storefront')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('hall_id'))
                ->badge(fn() => \App\Models\ServiceFeeSetting::whereNotNull('hall_id')->count())
                ->badgeColor('success'),

            'owner_specific' => Tab::make(__('service-fee.tabs.owner_specific'))
                ->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('owner_id')->whereNull('hall_id'))
                ->badge(fn() => \App\Models\ServiceFeeSetting::whereNotNull('owner_id')->whereNull('hall_id')->count())
                ->badgeColor('warning'),
        ];
    }
}
