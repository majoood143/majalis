<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\RegionResource\Pages\ListRegions;
use App\Filament\Admin\Resources\RegionResource\Pages\CreateRegion;
use App\Filament\Admin\Resources\RegionResource\Pages\EditRegion;
use App\Filament\Admin\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;


class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';

    protected static string | \UnitEnum | null $navigationGroup = 'Location Management';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('region.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('region.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('region.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('region.region_information'))
                    ->schema([
                        TextInput::make('name.en')
                            ->label(__('region.name_en'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name.ar')
                            ->label(__('region.name_ar'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code')
                            ->label(__('region.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->alphaDash(),

                        Textarea::make('description.en')
                            ->label(__('region.description_en'))
                            ->rows(3),

                        Textarea::make('description.ar')
                            ->label(__('region.description_ar'))
                            ->rows(3),
                    ])->columns(2),

                Section::make(__('region.location'))
                    ->schema([
                        TextInput::make('latitude')
                            ->label(__('region.latitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-90)
                            ->maxValue(90),

                        TextInput::make('longitude')
                            ->label(__('region.longitude'))
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-180)
                            ->maxValue(180),
                    ])->columns(2),

                Section::make(__('region.settings'))
                    ->schema([
                        TextInput::make('order')
                            ->label(__('region.order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('region.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('region.name'))
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($query) use ($search) {
                            $query->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('name->en', $direction);
                    })
                    ->formatStateUsing(function ($record) {
                        $locale = app()->getLocale();
                        $name = $record->getRawOriginal('name'); // Get the raw array
                        $decoded = json_decode($name, true);
                        return $decoded[$locale] ?? $decoded['en'] ?? '';
                    }),

                TextColumn::make('code')
                    ->label(__('region.code'))
                    ->searchable()
                    ->badge(),

                TextColumn::make('cities_count')
                    ->counts('cities')
                    ->label(__('region.cities'))
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label(__('region.is_active'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('region.order'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('region.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('region.filters.active'))
                    ->boolean()
                    ->trueLabel(__('region.filters.active_only'))
                    ->falseLabel(__('region.filters.inactive_only'))
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('region.table_actions.edit')),
                    DeleteAction::make()
                        ->label(__('region.table_actions.delete')),
                        // TODO: ActivityLogTimelineTableAction removed (rmsramos v3-only) - replace with v4 equivalent,
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegions::route('/'),
            'create' => CreateRegion::route('/create'),
            'edit' => EditRegion::route('/{record}/edit'),
        ];
    }
}
