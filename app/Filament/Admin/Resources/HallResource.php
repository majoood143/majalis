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
use App\Filament\Traits\HasTranslations;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Log;
use App\Models\Region;
use Illuminate\Support\Collection;

class HallResource extends Resource
{

    use HasTranslations;

    // ✅ Use METHOD override (not property) - avoids PHP trait conflicts
    protected static function getTranslationNamespace(): string
    {
        return 'admin';
    }
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
    //protected static ?string $navigationGroup = 'Hall Management';


    public static function getNavigationGroup(): ?string
    {
        return __('admin.hall_navigation_group');
    }
    /**
     * Sort order in the navigation menu.
     */
    protected static ?int $navigationSort = 1;

    // Specify the translation file namespace
    protected static ?string $translationNamespace = 'admin';

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
                Forms\Components\Tabs::make(__('admin.hall_information'))
                    ->tabs([
                        // =============================================
                        // TAB 1: Basic Information
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.basic_info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([

                    Forms\Components\Select::make('region_id')
                        ->label(__('booking.fields.region_id.label'))
                        ->options(fn() => Region::where('is_active', true)->ordered()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        // ✅ FIX: Hydrate region_id from hall relationship when editing
                        ->afterStateHydrated(function (Forms\Components\Select $component, ?string $state, ?Hall $record) {
                            // Only hydrate if no state is set and we have a record with a hall
                            if ($state === null && $record?->hall?->city?->region_id) {
                                $component->state($record->hall->city->region_id);
                            }
                        })
                        ->afterStateUpdated(function (Set $set) {
                            // Reset dependent fields when region changes
                            $set('city_id', null);
                            $set('hall_id', null);
                            $set('booking_date', null);
                            $set('time_slot', null);
                            $set('hall_price', 0);
                        })
                        ->helperText(__('booking.fields.region_id.helper')),


                    Forms\Components\Select::make('city_id')
                        ->label(__('booking.fields.city_id.label'))
                        ->options(function (Get $get, ?Hall $record): Collection {
                            $regionId = $get('region_id');

                            // ✅ FIX: Fall back to record's hall region when editing
                            if (!$regionId && $record?->hall?->city?->region_id) {
                                $regionId = $record->hall->city->region_id;
                            }

                            return City::query()
                                ->when(
                                    $regionId,
                                    fn($query) => $query->where('region_id', $regionId)
                                )
                                ->where('is_active', true)
                                ->ordered()
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        // ✅ FIX: Hydrate city_id from hall relationship when editing
                        ->afterStateHydrated(function (Forms\Components\Select $component, ?string $state, ?Hall $record) {
                            // Only hydrate if no state is set and we have a record with a hall
                            if ($state === null && $record?->hall?->city_id) {
                                $component->state($record->hall->city_id);
                            }
                        })
                        ->afterStateUpdated(function (Set $set) {
                            // Reset dependent fields when city changes
                            $set('hall_id', null);
                            $set('booking_date', null);
                            $set('time_slot', null);
                            $set('hall_price', 0);
                        })
                        ->disabled(fn(Get $get, ?Hall $record): bool => !$get('region_id') && !$record?->hall_id)
                        ->helperText(__('booking.fields.city_id.helper')),
                                // City selection with localized names
                                // Forms\Components\Select::make('city_id')
                                //     ->label(__('admin.city'))
                                //     ->relationship('city', 'name')
                                //     ->getOptionLabelFromRecordUsing(function ($record) {
                                //         $locale = app()->getLocale();
                                //         return is_array($record->name)
                                //             ? ($record->name[$locale] ?? $record->name['en'] ?? 'Unnamed')
                                //             : $record->name;
                                //     })
                                //     ->searchable()
                                //     ->preload()
                                //     ->required()
                                //     ->native(false),

                                // Owner selection (only hall owners)
                                Forms\Components\Select::make('owner_id')
                                    ->label(__('admin.owner'))
                                    ->options(User::where('role', 'hall_owner')->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false),

                                // Bilingual name fields
                                Forms\Components\TextInput::make('name.en')
                                    ->label(__('admin.name_english'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.enter_hall_name_english')),

                                Forms\Components\TextInput::make('name.ar')
                                    ->label(__('admin.name_arabic'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.enter_hall_name_arabic')),

                                // SEO-friendly URL slug
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('admin.url_slug'))
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('admin.auto_generate_slug'))
                                    ->prefix(config('app.url') . '/halls/'),

                                Forms\Components\TextInput::make('area')
                                    ->label(__('admin.area'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix(__('admin.sqm'))
                                    ->placeholder(__('admin.enter_capacity_example')),

                                // Rich text descriptions (bilingual)
                                Forms\Components\RichEditor::make('description.en')
                                    ->label(__('admin.description_english'))
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
                                    ->label(__('admin.description_arabic'))
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


                        // ========== ADVANCE PAYMENT TAB ==========
                        Forms\Components\Tabs\Tab::make(__('admin.advance_payment'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make(__('admin.advance_payment_settings'))
                                    ->description(__('admin.advance_payment_explanation'))
                                    ->schema([

                                        // Enable/Disable Toggle
                                        Forms\Components\Toggle::make('allows_advance_payment')
                                            ->label(__('admin.allows_advance_payment'))
                                            ->helperText(__('admin.allows_advance_payment_help'))
                                            ->reactive()
                                            ->default(false)
                                            ->columnSpanFull(),

                                        // Advance Payment Type Selection
                                        Forms\Components\Radio::make('advance_payment_type')
                                            ->label(__('admin.advance_payment_type'))
                                            ->helperText(__('admin.advance_payment_type_help'))
                                            ->options([
                                                'fixed' => __('admin.advance_type_fixed'),
                                                'percentage' => __('admin.advance_type_percentage'),
                                            ])
                                            ->default('percentage')
                                            ->reactive()
                                            ->inline()
                                            ->visible(fn($get) => $get('allows_advance_payment'))
                                            ->required(fn($get) => $get('allows_advance_payment')),

                                        // Fixed Amount Field
                                        Forms\Components\TextInput::make('advance_payment_amount')
                                            ->label(__('admin.advance_payment_amount'))
                                            ->helperText(__('admin.advance_payment_amount_help'))
                                            ->numeric()
                                            ->prefix('OMR')
                                            ->step(0.001)
                                            ->minValue(0)
                                            ->placeholder(__('admin.advance_payment_amount_placeholder'))
                                            ->visible(fn($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'fixed')
                                            ->required(fn($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'fixed')
                                            ->rule('min:0.001')
                                            ->reactive(),

                                        // Percentage Field
                                        Forms\Components\TextInput::make('advance_payment_percentage')
                                            ->label(__('admin.advance_payment_percentage'))
                                            ->helperText(__('admin.advance_payment_percentage_help'))
                                            ->numeric()
                                            ->suffix('%')
                                            ->step(0.01)
                                            ->minValue(0.01)
                                            ->maxValue(100)
                                            ->placeholder(__('admin.advance_payment_percentage_placeholder'))
                                            ->visible(fn($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'percentage')
                                            ->required(fn($get) => $get('allows_advance_payment') && $get('advance_payment_type') === 'percentage')
                                            ->rule('max:100')
                                            ->reactive(),

                                        // Minimum Advance Payment
                                        Forms\Components\TextInput::make('minimum_advance_payment')
                                            ->label(__('admin.minimum_advance_payment'))
                                            ->helperText(__('admin.minimum_advance_payment_help'))
                                            ->numeric()
                                            ->prefix('OMR')
                                            ->step(0.001)
                                            ->minValue(0)
                                            ->placeholder(__('admin.minimum_advance_payment_placeholder'))
                                            ->visible(fn($get) => $get('allows_advance_payment')),

                                    ])->columns(2)->collapsible(),

                                // Preview Section
                                Forms\Components\Section::make(__('admin.advance_payment_preview'))
                                    ->description(__('admin.advance_payment_preview_help'))
                                    ->schema([

                                        Forms\Components\Placeholder::make('advance_preview')
                                            ->label('')
                                            ->content(function ($get, $record) {
                                                // Get advance payment settings
                                                $allowsAdvance = $get('allows_advance_payment');

                                                if (!$allowsAdvance) {
                                                    return '<div class="text-sm text-gray-500">'
                                                        . __('admin.advance_payment') . ' '
                                                        . __('admin.disabled')
                                                        . '</div>';
                                                }

                                                $type = $get('advance_payment_type');
                                                $fixedAmount = (float) $get('advance_payment_amount');
                                                $percentage = (float) $get('advance_payment_percentage');
                                                $minimumAdvance = (float) $get('minimum_advance_payment');

                                                // Get base price for preview (use price_per_slot or 1000 as example)
                                                $basePrice = $record ? (float) $record->price_per_slot : 1000.000;

                                                // Use a sample total price for preview
                                                $sampleTotal = $basePrice + 200; // Assume 200 OMR in services

                                                // Calculate advance based on type
                                                $advanceAmount = 0.0;
                                                if ($type === 'fixed') {
                                                    $advanceAmount = $fixedAmount;
                                                } elseif ($type === 'percentage' && $percentage > 0) {
                                                    $advanceAmount = ($sampleTotal * $percentage) / 100;
                                                }

                                                // Apply minimum if set
                                                if ($minimumAdvance > 0 && $advanceAmount < $minimumAdvance) {
                                                    $advanceAmount = $minimumAdvance;
                                                }

                                                // Ensure advance doesn't exceed total
                                                if ($advanceAmount > $sampleTotal) {
                                                    $advanceAmount = $sampleTotal;
                                                }

                                                $balance = $sampleTotal - $advanceAmount;

                                                return '<div class="p-4 space-y-3 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
                            <div class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                ' . __('admin.preview_for_price', ['price' => number_format($sampleTotal, 3)]) . '
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <div class="text-xs tracking-wide text-blue-700 uppercase dark:text-blue-300">
                                        ' . __('admin.customer_pays_advance') . '
                                    </div>
                                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        ' . number_format($advanceAmount, 3) . ' <span class="text-sm font-normal">OMR</span>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs tracking-wide text-blue-700 uppercase dark:text-blue-300">
                                        ' . __('admin.balance_due_before_event') . '
                                    </div>
                                    <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        ' . number_format($balance, 3) . ' <span class="text-sm font-normal">OMR</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                                💡 ' . __('admin.advance_includes_services') . '
                            </div>
                        </div>';
                                            })
                                            ->columnSpanFull(),

                                    ])
                            ]),
                        // ========================================

                        // =============================================
                        // TAB 3: Location with Interactive Map
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.location'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                // Address textarea
                                Forms\Components\Textarea::make('address')
                                    ->label(__('admin.full_address'))
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->placeholder(__('admin.enter_full_address')),

                                // Localized address fields
                                Forms\Components\TextInput::make('address_localized.en')
                                    ->label(__('admin.address_english'))
                                    ->placeholder(__('admin.enter_address_english')),

                                Forms\Components\TextInput::make('address_localized.ar')
                                    ->label(__('admin.address_arabic'))
                                    ->placeholder(__('admin.enter_address_arabic')),

                                // INTERACTIVE MAP PICKER
                                Forms\Components\Section::make(__('admin.pick_location_on_map'))
                                    ->description(__('admin.map_helper_click'))
                                    ->schema([
                                        Map::make('location')
                                            ->label(__('admin.hall_location'))
                                            // Default center on Muscat, Oman
                                            ->defaultLocation(
                                                latitude: self::DEFAULT_LATITUDE,
                                                longitude: self::DEFAULT_LONGITUDE
                                            )
                                            // Allow dragging the marker
                                            ->draggable()
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
                                    ->label(__('admin.latitude'))
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
                                    ->helperText(__('admin.coordinate_helper')),

                                Forms\Components\TextInput::make('longitude')
                                    ->label(__('admin.longitude'))
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
                                    ->helperText(__('admin.coordinate_helper')),

                                // Optional Google Maps URL for external navigation
                                Forms\Components\TextInput::make('google_maps_url')
                                    ->label(__('admin.google_maps_url'))
                                    ->url()
                                    ->columnSpanFull()
                                    ->placeholder(__('admin.video_placeholder'))
                                    ->helperText(__('admin.optional_google_maps')),
                            ])->columns(2),

                        // =============================================
                        // TAB 4: Capacity & Pricing
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.capacity_pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('capacity_min')
                                    ->label(__('admin.minimum_capacity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix(__('admin.guests'))
                                    ->placeholder(__('admin.enter_capacity_example')),

                                Forms\Components\TextInput::make('capacity_max')
                                    ->label(__('admin.maximum_capacity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->suffix(__('admin.guests'))
                                    ->placeholder(__('admin.enter_capacity_example')),

                                Forms\Components\TextInput::make('price_per_slot')
                                    ->label(__('admin.base_price_per_slot'))
                                    ->numeric()
                                    ->required()
                                    ->prefix('OMR')
                                    ->step(0.001)
                                    ->placeholder(__('admin.enter_price_example')),

                                // Slot-specific pricing overrides
                                Forms\Components\KeyValue::make('pricing_override')
                                    ->label(__('admin.slot_specific_pricing'))
                                    ->keyLabel(__('admin.time_slot'))
                                    ->valueLabel(__('admin.price_omr'))
                                    ->helperText(__('admin.override_prices_help'))
                                    ->columnSpanFull()
                                    ->addActionLabel(__('admin.add_price_override')),
                            ])->columns(2),

                        // =============================================
                        // TAB 5: Contact Information
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label(__('admin.phone_number'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder(__('admin.phone_placeholder'))
                                    ->prefix('📞'),

                                Forms\Components\TextInput::make('whatsapp')
                                    ->label(__('admin.whatsapp'))
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder(__('admin.whatsapp_placeholder'))
                                    ->prefix('💬'),

                                Forms\Components\TextInput::make('email')
                                    ->label(__('admin.email_address'))
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.email_placeholder'))
                                    ->prefix('✉️'),
                            ])->columns(3),

                        // =============================================
                        // TAB 6: Features & Media
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.features_media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // Multi-select for hall features
                                Forms\Components\Select::make('features')
                                    ->label(__('admin.hall_features'))
                                    ->multiple()
                                    ->options(HallFeature::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->helperText(__('admin.select_features_help')),

                                // Featured image upload
                                Forms\Components\FileUpload::make('featured_image')
                                    ->label(__('admin.featured_image'))
                                    ->image()
                                    ->directory('halls')
                                    ->columnSpanFull()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080')
                                    ->helperText(__('admin.recommended_image_size')),

                                // Gallery images
                                Forms\Components\FileUpload::make('gallery')
                                    ->label(__('admin.gallery_images'))
                                    ->multiple()
                                    ->image()
                                    ->directory('halls/gallery')
                                    ->maxFiles(10)
                                    ->columnSpanFull()
                                    ->reorderable()
                                    ->helperText(__('admin.max_images')),

                                // Video URL
                                Forms\Components\TextInput::make('video_url')
                                    ->label(__('admin.video_url'))
                                    ->url()
                                    ->columnSpanFull()
                                    ->placeholder(__('admin.video_placeholder'))
                                    ->helperText(__('admin.youtube_vimeo_link')),
                            ]),

                        // =============================================
                        // TAB 7: Settings
                        // =============================================
                        Forms\Components\Tabs\Tab::make(__('admin.settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('admin.active'))
                                    ->inline(false)
                                    ->default(true)
                                    ->helperText(__('admin.inactive_halls_hidden')),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label(__('admin.featured'))
                                    ->inline(false)
                                    ->default(false)
                                    ->helperText(__('admin.featured_halls_highlighted')),

                                Forms\Components\Toggle::make('requires_approval')
                                    ->label(__('admin.requires_approval'))
                                    ->helperText(__('admin.require_admin_approval'))
                                    ->inline(false)
                                    ->default(false),

                                Forms\Components\TextInput::make('cancellation_hours')
                                    ->label(__('admin.cancellation_window'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->default(24)
                                    ->suffix(__('admin.hours'))
                                    ->helperText(__('admin.allow_cancellation_help')),

                                Forms\Components\TextInput::make('cancellation_fee_percentage')
                                    ->label(__('admin.cancellation_fee'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->suffix('%')
                                    ->helperText(__('admin.cancellation_fee_help')),
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
                    ->label(__('admin.image'))
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/placeholder-hall.png')),

                // Hall name with translation
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->translated_name ?? 'N/A')
                    ->description(fn($record) => $record->slug),

                // City name with translation
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('admin.city'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->city->name ?? 'N/A'),

                // Owner name
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('admin.owner'))
                    ->sortable()
                    ->searchable(),

                // Maximum capacity
                Tables\Columns\TextColumn::make('capacity_max')
                    ->label(__('admin.capacity'))
                    ->sortable()
                    ->suffix(' ' . __('admin.guests'))
                    ->badge()
                    ->color('info'),

                // Base price
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label(__('admin.price'))
                    ->money('OMR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                // Bookings count
                Tables\Columns\TextColumn::make('bookings_count')
                    ->counts('bookings')
                    ->label(__('admin.bookings'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // Average rating
                Tables\Columns\TextColumn::make('average_rating')
                    ->label(__('admin.rating'))
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 1) . '/5' : 'N/A'),

                // Featured status
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('admin.featured'))
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                // Active status
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('admin.active'))
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
                    ->label(__('admin.city_filter'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Owner filter
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label(__('admin.owner_filter'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                // Featured filter
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('admin.featured_filter'))
                    ->boolean()
                    ->trueLabel(__('admin.featured_only'))
                    ->falseLabel(__('admin.not_featured'))
                    ->native(false),

                // Active filter
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('admin.active_filter'))
                    ->boolean()
                    ->trueLabel(__('admin.active_only'))
                    ->falseLabel(__('admin.inactive_only'))
                    ->native(false),

                // Capacity range filter
                Tables\Filters\Filter::make('capacity')
                    ->form([
                        Forms\Components\TextInput::make('min_capacity')
                            ->label(__('admin.min_capacity_filter'))
                            ->numeric(),
                        Forms\Components\TextInput::make('max_capacity')
                            ->label(__('admin.max_capacity_filter'))
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
            ->emptyStateHeading(__('admin.no_halls_found'))
            ->emptyStateDescription(__('admin.create_first_hall'))
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
        return __('admin.hall');
    }

    /**
     * Get the plural model label for the resource.
     *
     * @return string Plural label
     */
    public static function getPluralModelLabel(): string
    {
        return __('admin.halls');
    }
}
