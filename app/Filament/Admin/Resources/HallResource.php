<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallResource\Pages;
use App\Models\City;
use App\Models\Hall;
use App\Models\HallFeature;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HallResource extends Resource
{
    protected static ?string $model = Hall::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Hall Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Hall Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                    Forms\Components\Select::make('city_id')
                        ->label('City')
                        ->relationship('city', 'name')
                        ->getOptionLabelFromRecordUsing(function ($record) {
                            $locale = app()->getLocale();
                            return is_array($record->name)
                                ? ($record->name[$locale] ?? $record->name['en'] ?? 'Unnamed')
                                : $record->name;
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                                Forms\Components\Select::make('owner_id')
                                    ->label('Owner')
                                    ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
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

                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Leave empty to auto-generate'),

                                Forms\Components\RichEditor::make('description.en')
                                    ->label('Description (English)')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description.ar')
                                    ->label('Description (Arabic)')
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('address_localized.en')
                                    ->label('Address (English)'),

                                Forms\Components\TextInput::make('address_localized.ar')
                                    ->label('Address (Arabic)'),

                                Forms\Components\TextInput::make('latitude')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->required(),

                                Forms\Components\TextInput::make('longitude')
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->required(),

                                Forms\Components\TextInput::make('google_maps_url')
                                    ->url()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Capacity & Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('capacity_min')
                                    ->label('Minimum Capacity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('capacity_max')
                                    ->label('Maximum Capacity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\TextInput::make('price_per_slot')
                                    ->label('Base Price per Slot')
                                    ->numeric()
                                    ->required()
                                    ->prefix('OMR')
                                    ->step(0.001),

                                Forms\Components\KeyValue::make('pricing_override')
                                    ->label('Slot-Specific Pricing')
                                    ->keyLabel('Time Slot')
                                    ->valueLabel('Price (OMR)')
                                    ->helperText('Override prices for specific time slots (morning, afternoon, evening, full_day)')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('whatsapp')
                                    ->tel()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('Features & Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Select::make('features')
                                    ->label('Features')
                                    ->multiple()
                                    ->options(HallFeature::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->directory('halls')
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('gallery')
                                    ->multiple()
                                    ->image()
                                    ->directory('halls/gallery')
                                    ->maxFiles(10)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('video_url')
                                    ->url()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->inline(false),

                                Forms\Components\Toggle::make('is_featured')
                                    ->inline(false),

                                Forms\Components\Toggle::make('requires_approval')
                                    ->helperText('Require admin approval for bookings')
                                    ->inline(false),

                                Forms\Components\TextInput::make('cancellation_hours')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix('hours')
                                    ->helperText('Minimum hours before booking to allow cancellation'),

                                Forms\Components\TextInput::make('cancellation_fee_percentage')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->helperText('Cancellation fee percentage'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->translated_name ?? 'N/A'),

            Tables\Columns\TextColumn::make('city.name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->city->name),

                Tables\Columns\TextColumn::make('owner.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('capacity_max')
                    ->label('Capacity')
                    ->sortable()
                    ->suffix(' guests'),

                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label('Price')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_bookings')
                    ->label('Bookings')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('owner_id')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListHalls::route('/'),
            'create' => Pages\CreateHall::route('/create'),
            'view' => Pages\ViewHall::route('/{record}'),
            'edit' => Pages\EditHall::route('/{record}/edit'),
        ];
    }
}
