<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallCategoryResource\Pages;
use App\Models\HallCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class HallCategoryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = HallCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

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
        return __('hall-category.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('hall-category.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('hall-category.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('hall-category.category_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label(__('hall-category.name_en'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label(__('hall-category.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('hall-category.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-category.slug_helper')),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('hall-category.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Textarea::make('description.en')
                            ->label(__('hall-category.description_en'))
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label(__('hall-category.description_ar'))
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('hall-category.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('hall-category.slug'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('hall_types_count')
                    ->label(__('hall-category.hall_types_count'))
                    ->counts('hallTypes')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('hall-category.sort_order'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('hall-category.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label(__('hall-category.edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('hall-category.delete')),
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
            'index' => Pages\ListHallCategories::route('/'),
            'create' => Pages\CreateHallCategory::route('/create'),
            'edit' => Pages\EditHallCategory::route('/{record}/edit'),
        ];
    }
}
