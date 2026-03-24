<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallTypeResource\Pages;
use App\Models\HallCategory;
use App\Models\HallType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class HallTypeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = HallType::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('hall-type.type_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('hall-type.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('hall-type.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('hall-type.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-type.slug_helper')),

                        Forms\Components\Select::make('category_id')
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

                        Forms\Components\TextInput::make('icon')
                            ->label(__('hall-type.icon'))
                            ->maxLength(255)
                            ->helperText(__('hall-type.icon_helper')),

                        Forms\Components\TextInput::make('color')
                            ->label(__('hall-type.color'))
                            ->maxLength(50)
                            ->helperText(__('hall-type.color_helper')),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('hall-type.description_en'))
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('hall-type.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make(__('hall-type.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('hall-type.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('hall-type.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('hall-type.slug'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('hall-type.category'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($record) => $record->category?->getTranslation('name', app()->getLocale()) ?? '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label(__('hall-type.icon'))
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('color')
                    ->label(__('hall-type.color'))
                    ->badge()
                    ->color(fn($state) => $state ?? 'gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('hall-type.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('hall-type.sort_order'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('hall-type.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('hall-type.category'))
                    ->options(
                        HallCategory::query()
                            ->get()
                            ->mapWithKeys(fn(HallCategory $cat) => [
                                $cat->id => $cat->getTranslation('name', 'en'),
                            ])
                            ->toArray()
                    ),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('hall-type.active'))
                    ->boolean()
                    ->trueLabel(__('hall-type.active_only'))
                    ->falseLabel(__('hall-type.inactive_only'))
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('hall-type.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('hall-type.delete')),
                    ActivityLogTimelineTableAction::make('Activities'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListHallTypes::route('/'),
            'create' => Pages\CreateHallType::route('/create'),
            'edit' => Pages\EditHallType::route('/{record}/edit'),
        ];
    }
}
