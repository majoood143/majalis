<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\PromoCodeResource\Pages;
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

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.settings');
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

    /**
     * Scope to only show promo codes belonging to the authenticated owner's halls.
     */
    public static function getEloquentQuery(): Builder
    {
        $ownerHallIds = Hall::where('owner_id', auth()->id())->pluck('id');

        return parent::getEloquentQuery()
            ->where(function (Builder $q) use ($ownerHallIds) {
                $q->whereIn('hall_id', $ownerHallIds)
                    ->orWhere(function (Builder $q2) use ($ownerHallIds) {
                        // Also show codes created by this owner (in case hall was deleted)
                        $q2->where('created_by_type', 'hall_owner')
                            ->where('created_by_id', auth()->id());
                    });
            });
    }

    public static function form(Form $form): Form
    {
        $ownerHalls = Hall::where('owner_id', auth()->id())
            ->get()
            ->mapWithKeys(fn (Hall $h) => [
                $h->id => $h->getTranslation('name', app()->getLocale()),
            ]);

        return $form->schema([
            Forms\Components\Section::make(__('promo.section_details'))
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label(__('promo.field_code'))
                        ->required()
                        ->maxLength(50)
                        ->alphaNum()
                        ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                        ->unique(ignoreRecord: true)
                        ->placeholder('SAVE10'),

                    Forms\Components\TextInput::make('name')
                        ->label(__('promo.field_name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label(__('promo.field_description'))
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make(__('promo.section_discount'))
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('discount_type')
                        ->label(__('promo.field_discount_type'))
                        ->required()
                        ->options([
                            'percentage' => __('promo.type_percentage'),
                            'fixed'      => __('promo.type_fixed'),
                        ])
                        ->live(),

                    Forms\Components\TextInput::make('discount_value')
                        ->label(fn (Get $get): string => $get('discount_type') === 'percentage'
                            ? __('promo.field_discount_value_pct')
                            : __('promo.field_discount_value_fixed'))
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(fn (Get $get): ?float => $get('discount_type') === 'percentage' ? 100 : null)
                        ->step(0.01)
                        ->suffix(fn (Get $get): string => $get('discount_type') === 'percentage' ? '%' : __('currency.omr')),
                ]),

            Forms\Components\Section::make(__('promo.section_validity'))
                ->columns(2)
                ->schema([
                    Forms\Components\DateTimePicker::make('valid_from')
                        ->label(__('promo.field_valid_from'))
                        ->nullable()
                        ->native(false),

                    Forms\Components\DateTimePicker::make('valid_until')
                        ->label(__('promo.field_valid_until'))
                        ->nullable()
                        ->native(false)
                        ->after('valid_from'),

                    Forms\Components\TextInput::make('max_uses')
                        ->label(__('promo.field_max_uses'))
                        ->helperText(__('promo.field_max_uses_helper'))
                        ->nullable()
                        ->integer()
                        ->minValue(1),

                    Forms\Components\Toggle::make('is_active')
                        ->label(__('promo.field_is_active'))
                        ->default(true),
                ]),

            Forms\Components\Section::make(__('promo.section_scope'))
                ->schema([
                    Forms\Components\Select::make('hall_id')
                        ->label(__('promo.field_hall'))
                        ->helperText(__('promo.field_hall_helper_owner'))
                        ->required()
                        ->options($ownerHalls),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('promo.col_code'))
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('promo.col_name'))
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('discount_label')
                    ->label(__('promo.col_discount'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->discount_label),

                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('promo.col_hall'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->hall
                        ? $record->hall->getTranslation('name', app()->getLocale())
                        : __('promo.all_halls')),

                Tables\Columns\TextColumn::make('used_count')
                    ->label(__('promo.col_used'))
                    ->getStateUsing(fn (PromoCode $record): string => $record->max_uses !== null
                        ? "{$record->used_count} / {$record->max_uses}"
                        : (string) $record->used_count)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label(__('promo.col_valid_until'))
                    ->dateTime('d M Y')
                    ->placeholder(__('promo.no_expiry'))
                    ->color(fn (PromoCode $record): string => $record->valid_until && $record->valid_until->isPast()
                        ? 'danger'
                        : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('promo.col_active'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('promo.filter_active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit'   => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
