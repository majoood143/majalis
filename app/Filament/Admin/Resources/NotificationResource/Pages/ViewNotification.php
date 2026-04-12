<?php

namespace App\Filament\Admin\Resources\NotificationResource\Pages;

use App\Filament\Admin\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsRead')
                ->label(__('notification.actions.mark_as_read'))
                ->visible(fn() => !$this->record->read_at)
                ->action(function () {
                    $this->record->markAsRead();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('notification.infolist.details_section'))
                    ->schema([
                        Infolists\Components\TextEntry::make('type')
                            ->label(__('notification.infolist.type'))
                            ->formatStateUsing(fn($state) => class_basename($state)),
                        Infolists\Components\TextEntry::make('notifiable.name')
                            ->label(__('notification.infolist.user')),
                        Infolists\Components\TextEntry::make('data.title')
                            ->label(__('notification.infolist.title')),
                        Infolists\Components\TextEntry::make('data.body')
                            ->label(__('notification.infolist.message'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('notification.infolist.created_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('read_at')
                            ->label(__('notification.infolist.read_at'))
                            ->dateTime()
                            ->placeholder(__('notification.infolist.unread_placeholder')),
                    ])->columns(2),
            ]);
    }
}
