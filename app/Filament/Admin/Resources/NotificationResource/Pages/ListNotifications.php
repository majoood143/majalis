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
                ->label('Mark All as Read')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title('All notifications marked as read')
                        ->send();

                    $this->redirect(static::getUrl());
                }),

            Actions\Action::make('deleteRead')
                ->label('Delete Read')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $deleted = \Illuminate\Notifications\DatabaseNotification::whereNotNull('read_at')->delete();

                    Notification::make()
                        ->success()
                        ->title("{$deleted} notification(s) deleted")
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::count()),

            'unread' => Tab::make('Unread')
                ->icon('heroicon-o-bell-alert')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('read_at'))
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->count())
                ->badgeColor('warning'),

            'read' => Tab::make('Read')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('read_at'))
                ->badge(fn() => \Illuminate\Notifications\DatabaseNotification::whereNotNull('read_at')->count())
                ->badgeColor('success'),
        ];
    }
}
