<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\HallTypeResource\Pages\ListHallTypes;
use App\Filament\Admin\Resources\HallTypeResource\Pages\CreateHallType;
use App\Filament\Admin\Resources\HallTypeResource\Pages\EditHallType;
use App\Filament\Admin\Resources\HallTypeResource\Pages;
use App\Models\HallCategory;
use App\Models\HallType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class HallTypeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = HallType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 4;

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
        return __('admin.hall_navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('hall-type.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-type.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-type.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('hall-type.type_information'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('hall-type.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('hall-type.name_ar'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label(__('hall-type.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-type.slug_helper')),

                        Select::make('category_id')
                            ->label(__('hall-type.category'))
                            ->options(
                                HallCategory::query()
                                    ->get()
                                    ->mapWithKeys(fn(HallCategory $cat) => [
                                        $cat->id => $cat->getTranslation('name', app()->getLocale()),
                                    ])
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('icon')
                            ->label(__('hall-type.icon'))
                            ->maxLength(255)
                            ->helperText(__('hall-type.icon_helper')),

                        TextInput::make('color')
                            ->label(__('hall-type.color'))
                            ->maxLength(50)
                            ->helperText(__('hall-type.color_helper')),

                        Textarea::make('description.en')
                            ->label(__('hall-type.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('hall-type.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Section::make(__('hall-type.settings'))
                    ->schema([
                        TextInput::make('sort_order')
                            ->label(__('hall-type.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('hall-type.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('hall-type.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

                TextColumn::make('slug')
                    ->label(__('hall-type.slug'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->label(__('hall-type.category'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($record) => $record->category?->getTranslation('name', app()->getLocale()) ?? '-')
                    ->sortable(),

                TextColumn::make('icon')
                    ->label(__('hall-type.icon'))
                    ->badge()
                    ->toggleable(),

                TextColumn::make('color')
                    ->label(__('hall-type.color'))
                    ->badge()
                    ->color(fn($state) => $state ?? 'gray')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('hall-type.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('hall-type.sort_order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('hall-type.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('hall-type.category'))
                    ->options(
                        HallCategory::query()
                            ->get()
                            ->mapWithKeys(fn(HallCategory $cat) => [
                                $cat->id => $cat->getTranslation('name', 'en'),
                            ])
                            ->toArray()
                    ),

                TernaryFilter::make('is_active')
                    ->label(__('hall-type.active'))
                    ->boolean()
                    ->trueLabel(__('hall-type.active_only'))
                    ->falseLabel(__('hall-type.inactive_only'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('hall-type.edit')),
                    DeleteAction::make()
                        ->label(__('hall-type.delete')),
                    // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
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
            'index' => ListHallTypes::route('/'),
            'create' => CreateHallType::route('/create'),
            'edit' => EditHallType::route('/{record}/edit'),
        ];
    }
}
