<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationResource\Pages;
use Illuminate\Notifications\DatabaseNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->disabled(),

                Forms\Components\KeyValue::make('data')
                    ->disabled(),

                Forms\Components\DateTimePicker::make('read_at')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->formatStateUsing(fn($state) => class_basename($state)),

                Tables\Columns\TextColumn::make('notifiable.name')
                    ->label(__('notification.columns.user'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('data.title')
                    ->label(__('notification.columns.title'))
                    ->limit(50),

                Tables\Columns\TextColumn::make('data.body')
                    ->label(__('notification.columns.message'))
                    ->limit(50),

                Tables\Columns\IconColumn::make('read_at')
                    ->label(__('notification.columns.read'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read_at')
                    ->label(__('notification.filters.read_status'))
                    ->queries(
                        true: fn($query) => $query->whereNotNull('read_at'),
                        false: fn($query) => $query->whereNull('read_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsRead')
                    ->label(__('notification.actions.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => !$record->read_at)
                    ->action(fn($record) => $record->markAsRead()),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markAsRead')
                    ->label(__('notification.actions.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn($records) => $records->each->markAsRead()),

                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
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
