<?php

namespace App\Filament\Admin\Resources\NotificationResource\Pages;

use Filament\Actions\Action;
use Illuminate\Notifications\DatabaseNotification;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Admin\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAllAsRead')
                ->label(__('notification.actions.mark_all_as_read'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title(__('notification.notifications.all_marked_read_title'))
                        ->send();

                    $this->redirect(static::getUrl());
                }),

            Action::make('deleteRead')
                ->label(__('notification.actions.delete_read'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $deleted = DatabaseNotification::whereNotNull('read_at')->delete();

                    Notification::make()
                        ->success()
                        ->title(__('notification.notifications.deleted_title', ['count' => $deleted]))
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('notification.tabs.all'))
                ->badge(fn() => DatabaseNotification::count()),

            'unread' => Tab::make(__('notification.tabs.unread'))
                ->icon('heroicon-o-bell-alert')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('read_at'))
                ->badge(fn() => DatabaseNotification::whereNull('read_at')->count())
                ->badgeColor('warning'),

            'read' => Tab::make(__('notification.tabs.read'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('read_at'))
                ->badge(fn() => DatabaseNotification::whereNotNull('read_at')->count())
                ->badgeColor('success'),
        ];
    }
}
