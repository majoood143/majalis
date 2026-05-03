<?php

namespace App\Filament\Admin\Resources\NotificationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Admin\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAsRead')
                ->label(__('notification.actions.mark_as_read'))
                ->visible(fn() => !$this->record->read_at)
                ->action(function () {
                    $this->record->markAsRead();
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),

            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make(__('notification.infolist.details_section'))
                    ->schema([
                        TextEntry::make('type')
                            ->label(__('notification.infolist.type'))
                            ->formatStateUsing(fn($state) => class_basename($state)),
                        TextEntry::make('notifiable.name')
                            ->label(__('notification.infolist.user')),
                        TextEntry::make('data.title')
                            ->label(__('notification.infolist.title')),
                        TextEntry::make('data.body')
                            ->label(__('notification.infolist.message'))
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label(__('notification.infolist.created_at'))
                            ->dateTime(),
                        TextEntry::make('read_at')
                            ->label(__('notification.infolist.read_at'))
                            ->dateTime()
                            ->placeholder(__('notification.infolist.unread_placeholder')),
                    ])->columns(2),
            ]);
    }
}
