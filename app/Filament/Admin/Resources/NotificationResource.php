<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\NotificationResource\Pages\ListNotifications;
use App\Filament\Admin\Resources\NotificationResource\Pages\ViewNotification;
use App\Filament\Admin\Resources\NotificationResource\Pages;
use Illuminate\Notifications\DatabaseNotification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bell';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return __('notification.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('notification.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('notification.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('notification.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type')
                    ->disabled(),

                KeyValue::make('data')
                    ->disabled(),

                DateTimePicker::make('read_at')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->searchable()
                    ->formatStateUsing(fn($state) => class_basename($state)),

                TextColumn::make('notifiable.name')
                    ->label(__('notification.columns.user'))
                    ->searchable(),

                TextColumn::make('data.title')
                    ->label(__('notification.columns.title'))
                    ->limit(50),

                TextColumn::make('data.body')
                    ->label(__('notification.columns.message'))
                    ->limit(50),

                IconColumn::make('read_at')
                    ->label(__('notification.columns.read'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label(__('notification.filters.read_status'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('read_at'),
                        false: fn($query) => $query->whereNull('read_at'),
                    ),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label(__('notification.actions.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => !$record->read_at)
                    ->action(fn($record) => $record->markAsRead()),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('markAsRead')
                    ->label(__('notification.actions.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn($records) => $records->each->markAsRead()),

                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'view' => ViewNotification::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('read_at')->count() ?: null;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
