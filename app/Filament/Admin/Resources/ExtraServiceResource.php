<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExtraServiceResource\Pages;
use App\Models\ExtraService;
use App\Models\Hall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExtraServiceResource extends Resource
{
    protected static ?string $model = ExtraService::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Hall Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Information')
                    ->schema([
                Forms\Components\Select::make('hall_id')
                    ->label('Hall')
                    ->options(function () {
                        return Hall::with(['city', 'owner'])
                            ->active()
                            ->get()
                            ->mapWithKeys(function ($hall) {
                                //$locale = app()->getLocale();

                                // Get hall name
                                $hallName = $hall->name ?? 'Unnamed Hall';
                                    // ? ($hall->name[$locale] ?? $hall->name['en'] ?? 'Unnamed Hall')
                                    // : $hall->name;

                                // Get city name
                                $cityName = $hall->city->name ?? 'Unknown City';
                                // if ($hall->city && is_array($hall->city->name)) {
                                //     $cityName = $hall->city->name[$locale] ?? $hall->city->name['en'] ?? 'Unknown City';
                                // }

                                // Get owner name
                                $ownerName = $hall->owner->name ?? 'No Owner';

                                // Format: "Hall Name - City (Owner)"
                                $label = "{$hallName} - {$cityName} - ({$ownerName})";

                                return [$hall->id => $label];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                        Forms\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description.en')
                            ->label('Description (English)')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description.ar')
                            ->label('Description (Arabic)')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\Select::make('unit')
                            ->options([
                                'per_person' => 'Per Person',
                                'per_item' => 'Per Item',
                                'per_hour' => 'Per Hour',
                                'fixed' => 'Fixed Price',
                            ])
                            ->default('fixed'),

                        Forms\Components\TextInput::make('minimum_quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        Forms\Components\TextInput::make('maximum_quantity')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Leave empty for unlimited'),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('services')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Toggle::make('is_required')
                            ->label('Required Service')
                            ->helperText('Auto-added to all bookings')
                            ->inline(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->name),

                Tables\Columns\TextColumn::make('hall.name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->hall->name),

                Tables\Columns\TextColumn::make('price')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'per_person' => 'Per Person',
                        'per_item' => 'Per Item',
                        'per_hour' => 'Per Hour',
                        'fixed' => 'Fixed',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hall_id')
                    ->relationship('hall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('unit')
                    ->options([
                        'per_person' => 'Per Person',
                        'per_item' => 'Per Item',
                        'per_hour' => 'Per Hour',
                        'fixed' => 'Fixed Price',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Required')
                    ->boolean()
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
            'index' => Pages\ListExtraServices::route('/'),
            'create' => Pages\CreateExtraService::route('/create'),
            'edit' => Pages\EditExtraService::route('/{record}/edit'),
        ];
    }
}
