<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegionResource\Pages;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Location Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Region Information')
                    ->schema([
                        Forms\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->alphaDash(),

                        Forms\Components\Textarea::make('description.en')
                            ->label('Description (English)')
                            ->rows(3),

                        Forms\Components\Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-90)
                            ->maxValue(90),

                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->step(0.0000001)
                            ->minValue(-180)
                            ->maxValue(180),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
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
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('cities_count')
                    ->counts('cities')
                    ->label('Cities')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
