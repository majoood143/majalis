<?php

declare(strict_types=1);

/**
 * HallResource - Filament Admin Resource for Hall Management
 *
 * This resource provides CRUD operations for halls with:
 * - Interactive map picker for location selection (OpenStreetMap)
 * - Bilingual support (Arabic/English)
 * - Comprehensive form with tabbed layout
 * - Advanced table with filters and actions
 *
 * @package App\Filament\Admin\Resources
 * @version 2.0.0
 * @author Majalis Development Team
 */

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HallResource\Pages;
use App\Models\City;
use App\Models\Hall;
use App\Models\HallFeature;
use App\Models\User;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Log;

class HallResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     */
    protected static ?string $model = Hall::class;

    /**
     * Navigation icon for the sidebar.
     */
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    /**
     * Navigation group for organizing menu items.
     */
    protected static ?string $navigationGroup = 'Hall Management';

    /**
     * Sort order in the navigation menu.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Default coordinates for Oman (Muscat).
     * Used as initial map center when creating new halls.
     */
    private const DEFAULT_LATITUDE = 23.5880;
    private const DEFAULT_LONGITUDE = 58.3829;
    private const DEFAULT_ZOOM = 10;

    /**
     * Define the form schema for creating/editing halls.
     *
     * @param Form $form The Filament form instance
     * @return Form Configured form with all fields
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Hall Information')
                    ->tabs([
                        // =============================================
                        // TAB 1: Basic Information
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                // City selection with localized names
                                Forms\Components\Select::make('city_id')
                                    ->label(__('City'))
                                    ->relationship('city', 'name')
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $locale = app()->getLocale();
                                        return is_array($record->name)
                                            ? ($record->name[$locale] ?? $record->name['en'] ?? 'Unnamed')
                                            : $record->name;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false),

                                // Owner selection (only hall owners)
                                Forms\Components\Select::make('owner_id')
                                    ->label(__('Owner'))
                                    ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false),

                                // Bilingual name fields
                                Forms\Components\TextInput::make('name.en')
                                    ->label(__('Name (English)'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter hall name in English'),

                                Forms\Components\TextInput::make('name.ar')
                                    ->label(__('Name (Arabic)'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('أدخل اسم القاعة بالعربية'),

                                // SEO-friendly URL slug
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('URL Slug'))
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('Leave empty to auto-generate from English name'))
                                    ->prefix(config('app.url') . '/halls/'),

                                // Rich text descriptions (bilingual)
                                Forms\Components\RichEditor::make('description.en')
                                    ->label(__('Description (English)'))
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                        'redo',
                                        'undo',
                                    ]),

                                Forms\Components\RichEditor::make('description.ar')
                                    ->label(__('Description (Arabic)'))
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                        'redo',
                                        'undo',
                                    ]),
                            ])->columns(2),

                        // =============================================
                        // TAB 2: Location with Interactive Map
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                // Address textarea
                                Forms\Components\Textarea::make('address')
                                    ->label(__('Full Address'))
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->placeholder('Enter the complete street address'),

                                // Localized address fields
                                Forms\Components\TextInput::make('address_localized.en')
                                    ->label(__('Address (English)'))
                                    ->placeholder('Address in English'),

                                Forms\Components\TextInput::make('address_localized.ar')
                                    ->label(__('Address (Arabic)'))
                                    ->placeholder('العنوان بالعربية'),

                    // =============================================
                    // INTERACTIVE MAP PICKER
                    // Uses OpenStreetMap tiles (free, no API key)
                    // =============================================
                    // =============================================
                    // INTERACTIVE MAP PICKER
                    // Uses OpenStreetMap tiles (free, no API key)
                    // =============================================
                    Forms\Components\Section::make(__('Pick Location on Map'))
                        ->description(__('Click on the map to set the hall location, or drag the marker to adjust.'))
                        ->schema([
                            Map::make('location')
                                ->label(__('Hall Location'))
                                // Default center on Muscat, Oman
                                ->defaultLocation(
                                    latitude: self::DEFAULT_LATITUDE,
                                    longitude: self::DEFAULT_LONGITUDE
                                )
                                // Allow dragging the marker
                                ->draggable()
                            // Enable click to place marker
                            //->clickable()
                           
                            // Update latitude/longitude fields when marker moves
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                if ($state) {
                                    $set('latitude', $state['lat'] ? round((float) $state['lat'], 7) : null);
                                    $set('longitude', $state['lng'] ? round((float) $state['lng'], 7) : null);
                                }
                            })
                                // Sync with existing lat/lng on edit
                                ->afterStateHydrated(function (Map $component, Get $get): void {
                                    $lat = $get('latitude');
                                    $lng = $get('longitude');

                                    if ($lat && $lng) {
                                        $component->state([
                                            'lat' => (float) $lat,
                                            'lng' => (float) $lng,
                                        ]);
                                    }
                                })
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpanFull(),
                                // Hidden/Read-only coordinate fields
                                // These get populated by the map picker
                                Forms\Components\TextInput::make('latitude')
                                    ->label(__('Latitude'))
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        // Update map when latitude changes manually
                                        $lng = $get('longitude');
                                        if ($state && $lng) {
                                            $set('location', [
                                                'lat' => (float) $state,
                                                'lng' => (float) $lng,
                                            ]);
                                        }
                                    })
                                    ->helperText(__('Auto-filled from map. Can also enter manually.')),

                                Forms\Components\TextInput::make('longitude')
                                    ->label(__('Longitude'))
                                    ->numeric()
                                    ->step(0.0000001)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                        // Update map when longitude changes manually
                                        $lat = $get('latitude');
                                        if ($state && $lat) {
                                            $set('location', [
                                                'lat' => (float) $lat,
                                                'lng' => (float) $state,
                                            ]);
                                        }
                                    })
                                    ->helperText(__('Auto-filled from map. Can also enter manually.')),

                                // Optional Google Maps URL for external navigation
                                Forms\Components\TextInput::make('google_maps_url')
                                    ->label(__('Google Maps URL'))
                                    ->url()
                                    ->columnSpanFull()
                                    ->placeholder('https://maps.google.com/...')
                                    ->helperText(__('Optional: Paste a Google Maps link for this location')),
                            ])->columns(2),

                        // =============================================
                        // TAB 3: Capacity & Pricing
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Capacity & Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('capacity_min')
                                    ->label(__('Minimum Capacity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix(__('guests'))
                                    ->placeholder('e.g., 50'),

                                Forms\Components\TextInput::make('capacity_max')
                                    ->label(__('Maximum Capacity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->suffix(__('guests'))
                                    ->placeholder('e.g., 500'),

                                Forms\Components\TextInput::make('price_per_slot')
                                    ->label(__('Base Price per Slot'))
                                    ->numeric()
                                    ->required()
                                    ->prefix('OMR')
                                    ->step(0.001)
                                    ->placeholder('e.g., 150.000'),

                                // Slot-specific pricing overrides
                                Forms\Components\KeyValue::make('pricing_override')
                                    ->label(__('Slot-Specific Pricing'))
                                    ->keyLabel(__('Time Slot'))
                                    ->valueLabel(__('Price (OMR)'))
                                    ->helperText(__('Override prices for: morning, afternoon, evening, full_day'))
                                    ->columnSpanFull()
                                    ->addActionLabel(__('Add Price Override')),
                            ])->columns(2),

                        // =============================================
                        // TAB 4: Contact Information
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder('+968 XXXX XXXX')
                                    ->prefix('📞'),

                                Forms\Components\TextInput::make('whatsapp')
                                    ->label(__('WhatsApp'))
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+968 XXXX XXXX')
                                    ->prefix('💬'),

                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email Address'))
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('contact@hallname.com')
                                    ->prefix('✉️'),
                            ])->columns(3),

                        // =============================================
                        // TAB 5: Features & Media
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Features & Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // Multi-select for hall features
                                Forms\Components\Select::make('features')
                                    ->label(__('Hall Features'))
                                    ->multiple()
                                    ->options(HallFeature::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->helperText(__('Select all features available in this hall')),

                                // Featured image upload
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label(__('Featured Image'))
                                    ->image()
                                    ->directory('halls')
                                    ->columnSpanFull()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080')
                                    ->helperText(__('Recommended: 1920x1080 pixels')),

                                // Gallery images
                                Forms\Components\FileUpload::make('gallery')
                                    ->label(__('Gallery Images'))
                                    ->multiple()
                                    ->image()
                                    ->directory('halls/gallery')
                                    ->maxFiles(10)
                                    ->columnSpanFull()
                                    ->reorderable()
                                    ->helperText(__('Maximum 10 images')),

                                // Video URL
                                Forms\Components\TextInput::make('video_url')
                                    ->label(__('Video URL'))
                                    ->url()
                                    ->columnSpanFull()
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->helperText(__('YouTube or Vimeo link')),
                            ]),

                        // =============================================
                        // TAB 6: Settings
                        // =============================================
                        Forms\Components\Tabs\Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('Active'))
                                    ->inline(false)
                                    ->default(true)
                                    ->helperText(__('Inactive halls are hidden from customers')),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label(__('Featured'))
                                    ->inline(false)
                                    ->default(false)
                                    ->helperText(__('Featured halls appear in highlighted sections')),

                                Forms\Components\Toggle::make('requires_approval')
                                    ->label(__('Requires Approval'))
                                    ->helperText(__('Require admin approval for each booking'))
                                    ->inline(false)
                                    ->default(false),

                                Forms\Components\TextInput::make('cancellation_hours')
                                    ->label(__('Cancellation Window'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->default(24)
                                    ->suffix(__('hours'))
                                    ->helperText(__('Minimum hours before booking to allow cancellation')),

                                Forms\Components\TextInput::make('cancellation_fee_percentage')
                                    ->label(__('Cancellation Fee'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->suffix('%')
                                    ->helperText(__('Fee percentage charged on cancellation')),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    /**
     * Define the table schema for listing halls.
     *
     * @param Table $table The Filament table instance
     * @return Table Configured table with columns, filters, and actions
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Featured image thumbnail
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label(__('Image'))
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/placeholder-hall.png')),

                // Hall name with translation
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->translated_name ?? 'N/A')
                    ->description(fn($record) => $record->slug),

                // City name with translation
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->city->name ?? 'N/A'),

                // Owner name
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->sortable()
                    ->searchable(),

                // Maximum capacity
                Tables\Columns\TextColumn::make('capacity_max')
                    ->label(__('Capacity'))
                    ->sortable()
                    ->suffix(' ' . __('guests'))
                    ->badge()
                    ->color('info'),

                // Base price
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label(__('Price'))
                    ->money('OMR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                // Bookings count
                Tables\Columns\TextColumn::make('bookings_count')
                    ->counts('bookings')
                    ->label(__('Bookings'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

            // Average rating
            Tables\Columns\TextColumn::make('average_rating')
                ->label(__('Rating'))
                ->badge()
                ->color('warning')
                ->sortable()
                ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 1) . '/5' : 'N/A'),

                // Featured status
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                // Active status
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // City filter
                Tables\Filters\SelectFilter::make('city_id')
                    ->label(__('City'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Owner filter
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label(__('Owner'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                // Featured filter
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->trueLabel(__('Featured only'))
                    ->falseLabel(__('Not featured'))
                    ->native(false),

                // Active filter
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueLabel(__('Active only'))
                    ->falseLabel(__('Inactive only'))
                    ->native(false),

                // Capacity range filter
                Tables\Filters\Filter::make('capacity')
                    ->form([
                        Forms\Components\TextInput::make('min_capacity')
                            ->label(__('Min Capacity'))
                            ->numeric(),
                        Forms\Components\TextInput::make('max_capacity')
                            ->label(__('Max Capacity'))
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_capacity'], fn($q) => $q->where('capacity_max', '>=', $data['min_capacity']))
                            ->when($data['max_capacity'], fn($q) => $q->where('capacity_max', '<=', $data['max_capacity']));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('No halls found'))
            ->emptyStateDescription(__('Create your first hall to get started.'))
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    /**
     * Define related resources (relation managers).
     *
     * @return array List of relation manager classes
     */
    public static function getRelations(): array
    {
        return [
            // Add relation managers here if needed
            // e.g., BookingsRelationManager::class,
        ];
    }

    /**
     * Define the resource pages.
     *
     * @return array Route definitions for resource pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHalls::route('/'),
            'create' => Pages\CreateHall::route('/create'),
            'view' => Pages\ViewHall::route('/{record}'),
            'edit' => Pages\EditHall::route('/{record}/edit'),
        ];
    }

    /**
     * Get the navigation badge showing total active halls.
     *
     * @return string|null Badge count
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    /**
     * Get the navigation badge color.
     *
     * @return string|null Badge color
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Get the model label for the resource.
     *
     * @return string Singular label
     */
    public static function getModelLabel(): string
    {
        return (string) __('Hall');
    }

    /**
     * Get the plural model label for the resource.
     *
     * @return string Plural label
     */
    public static function getPluralModelLabel(): string
    {
        return (string) __('Hall');
    }
}
