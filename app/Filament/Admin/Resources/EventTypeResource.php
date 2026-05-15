<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\EventTypeResource\Pages\ListEventTypes;
use App\Filament\Admin\Resources\EventTypeResource\Pages\CreateEventType;
use App\Filament\Admin\Resources\EventTypeResource\Pages\EditEventType;
use App\Models\EventType;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class EventTypeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = EventType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 5;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('event-type.resource.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('event-type.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('event-type.resource.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('event-type.resource.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('event-type.sections.details.title'))
                    ->description(__('event-type.sections.details.description'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('event-type.fields.name_en'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('name.ar')
                            ->label(__('event-type.fields.name_ar'))
                            ->required()
                            ->maxLength(100),
                    ])->columns(2),

                Section::make(__('event-type.sections.settings.title'))
                    ->description(__('event-type.sections.settings.description'))
                    ->schema([
                        TextInput::make('sort_order')
                            ->label(__('event-type.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText(__('event-type.helpers.sort_order')),

                        Toggle::make('is_active')
                            ->label(__('event-type.fields.is_active'))
                            ->default(true)
                            ->helperText(__('event-type.helpers.is_active')),
                    ])->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('event-type.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

                TextColumn::make('sort_order')
                    ->label(__('event-type.columns.sort_order'))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('event-type.columns.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('bookings_count')
                    ->label(__('event-type.columns.bookings_count'))
                    ->counts('bookings')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('event-type.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('event-type.filters.is_active')),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('event-type.actions.edit')),
                    DeleteAction::make()
                        ->label(__('event-type.actions.delete')),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEventTypes::route('/'),
            'create' => CreateEventType::route('/create'),
            'edit'   => EditEventType::route('/{record}/edit'),
        ];
    }
}
