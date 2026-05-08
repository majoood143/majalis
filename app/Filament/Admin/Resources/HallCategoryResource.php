<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\HallCategoryResource\Pages\ListHallCategories;
use App\Filament\Admin\Resources\HallCategoryResource\Pages\CreateHallCategory;
use App\Filament\Admin\Resources\HallCategoryResource\Pages\EditHallCategory;
use App\Filament\Admin\Resources\HallCategoryResource\Pages;
use App\Models\HallCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class HallCategoryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = HallCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('hall-category.category_information'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('hall-category.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('hall-category.name_ar'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label(__('hall-category.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('hall-category.slug_helper')),

                        TextInput::make('sort_order')
                            ->label(__('hall-category.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Textarea::make('description.en')
                            ->label(__('hall-category.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('hall-category.description_ar'))
                            ->rows(3),
                    ])->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('hall-category.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

                TextColumn::make('slug')
                    ->label(__('hall-category.slug'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('hall_types_count')
                    ->label(__('hall-category.hall_types_count'))
                    ->counts('hallTypes')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('hall-category.sort_order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('hall-category.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('hall-category.edit')),
                    DeleteAction::make()
                        ->label(__('hall-category.delete')),
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
            'index' => ListHallCategories::route('/'),
            'create' => CreateHallCategory::route('/create'),
            'edit' => EditHallCategory::route('/{record}/edit'),
        ];
    }
}
