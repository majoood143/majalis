<?php

namespace App\Filament\Admin\Resources\NotificationResource\Pages;

use App\Filament\Admin\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAllAsRead')
                ->label(__('notification.actions.mark_all_as_read'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title(__('notification.notifications.all_marked_read_title'))
                        ->send();

                    $this->redirect(static::getUrl());
                }),

            Actions\Action::make('deleteRead')
                ->label(__('notification.actions.delete_read'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $deleted = \Illuminate\Notifications\DatabaseNotification::whereNotNull('read_at')->delete();

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
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::count()),

            'unread' => Tab::make(__('notification.tabs.unread'))
                ->icon('heroicon-o-bell-alert')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('read_at'))
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->count())
                ->badgeColor('warning'),

            'read' => Tab::make(__('notification.tabs.read'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('read_at'))
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::whereNotNull('read_at')->count())
                ->badgeColor('success'),
        ];
    }
}
