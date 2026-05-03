<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\PromoCodeResource\Pages\ListPromoCodes;
use App\Filament\Admin\Resources\PromoCodeResource\Pages\CreatePromoCode;
use App\Filament\Admin\Resources\PromoCodeResource\Pages\EditPromoCode;
use App\Filament\Admin\Resources\PromoCodeResource\Pages;
use App\Filament\Admin\Resources\PromoCodeResource\RelationManagers\BookingsRelationManager;
use App\Models\Hall;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('promo.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('promo.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('promo.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('promo.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('promo.section_details'))
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label(__('promo.field_code'))
                        ->required()
                        ->maxLength(50)
                        ->alphaNum()
                        ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                        ->unique(ignoreRecord: true)
                        ->placeholder('SAVE10'),

                    TextInput::make('name')
                        ->label(__('promo.field_name'))
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label(__('promo.field_description'))
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            Section::make(__('promo.section_discount'))
                ->columns(2)
                ->schema([
                    Select::make('discount_type')
                        ->label(__('promo.field_discount_type'))
                        ->required()
                        ->options([
                            'percentage' => __('promo.type_percentage'),
                            'fixed'      => __('promo.type_fixed'),
                        ])
                        ->live(),

                    TextInput::make('discount_value')
                        ->label(fn (\Filament\Schemas\Components\Utilities\Get $get): string => $get('discount_type') === 'percentage'
                            ? __('promo.field_discount_value_pct')
                            : __('promo.field_discount_value_fixed'))
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(fn (\Filament\Schemas\Components\Utilities\Get $get): ?float => $get('discount_type') === 'percentage' ? 100 : null)
                        ->step(0.01)
                        ->suffix(fn (\Filament\Schemas\Components\Utilities\Get $get): string => $get('discount_type') === 'percentage' ? '%' : __('currency.omr')),
                ]),

            Section::make(__('promo.section_validity'))
                ->columns(2)
                ->schema([
                    DateTimePicker::make('valid_from')
                        ->label(__('promo.field_valid_from'))
                        ->nullable()
                        ->native(false),

                    DateTimePicker::make('valid_until')
                        ->label(__('promo.field_valid_until'))
                        ->nullable()
                        ->native(false)
                        ->after('valid_from'),

                    TextInput::make('max_uses')
                        ->label(__('promo.field_max_uses'))
                        ->helperText(__('promo.field_max_uses_helper'))
                        ->nullable()
                        ->integer()
                        ->minValue(1),

                    Toggle::make('is_active')
                        ->label(__('promo.field_is_active'))
                        ->default(true),
                ]),

            Section::make(__('promo.section_scope'))
                ->schema([
                    Select::make('hall_id')
                        ->label(__('promo.field_hall'))
                        ->helperText(__('promo.field_hall_helper'))
                        ->nullable()
                        ->searchable()
                        ->preload()
                        ->options(
                            Hall::all()->mapWithKeys(fn (Hall $h) => [
                                $h->id => $h->getTranslation('name', app()->getLocale()),
                            ])
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('promo.col_code'))
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('name')
                    ->label(__('promo.col_name'))
                    ->searchable()
                    ->limit(30),

                TextColumn::make('discount_label')
                    ->label(__('promo.col_discount'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->discount_label),

                TextColumn::make('hall.name')
                    ->label(__('promo.col_hall'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->hall
                        ? $record->hall->getTranslation('name', app()->getLocale())
                        : __('promo.all_halls'))
                    ->badge()
                    ->color(fn (PromoCode $record) => $record->hall_id ? 'info' : 'gray'),

                TextColumn::make('used_count')
                    ->label(__('promo.col_used'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->max_uses !== null
                        ? "{$record->used_count} / {$record->max_uses}"
                        : (string) $record->used_count)
                    ->alignCenter(),

                TextColumn::make('valid_until')
                    ->label(__('promo.col_valid_until'))
                    ->dateTime('d M Y')
                    ->placeholder(__('promo.no_expiry'))
                    ->color(fn (PromoCode $record): string => $record->valid_until && $record->valid_until->isPast()
                        ? 'danger'
                        : 'success'),

                IconColumn::make('is_active')
                    ->label(__('promo.col_active'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('promo.filter_active')),

                SelectFilter::make('discount_type')
                    ->label(__('promo.filter_type'))
                    ->options([
                        'percentage' => __('promo.type_percentage'),
                        'fixed'      => __('promo.type_fixed'),
                    ]),

                SelectFilter::make('hall_id')
                    ->label(__('promo.filter_hall'))
                    ->options(
                        Hall::all()->mapWithKeys(fn (Hall $h) => [
                            $h->id => $h->getTranslation('name', app()->getLocale()),
                        ])
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPromoCodes::route('/'),
            'create' => CreatePromoCode::route('/create'),
            'edit'   => EditPromoCode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed(false);
    }
}
