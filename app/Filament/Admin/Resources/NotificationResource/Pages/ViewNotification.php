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
                Infolists\Components\Section::make('Notification Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('type')
                            ->formatStateUsing(fn($state) => class_basename($state)),
                        Infolists\Components\TextEntry::make('notifiable.name')->label('User'),
                        Infolists\Components\TextEntry::make('data.title')->label('Title'),
                        Infolists\Components\TextEntry::make('data.body')->label('Message')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')->dateTime(),
                        Infolists\Components\TextEntry::make('read_at')->dateTime()->placeholder('Unread'),
                    ])->columns(2),
            ]);
    }
}
