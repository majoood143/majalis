<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\HallResource\Pages;
use App\Filament\Owner\Resources\HallResource\RelationManagers;
use App\Models\Hall;
use App\Models\City;
use App\Models\HallFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use App\Models\Region;

/**
 * HallResource for Owner Panel
 *
 * This resource allows hall owners to manage ONLY their own halls.
 * It extends OwnerResource which automatically scopes queries to the owner.
 *
 * Features:
 * - Scoped to owner's halls only
 * - Bilingual support (Arabic/English)
 * - Availability management
 * - Pricing configuration
 * - Gallery management
 * - Booking overview
 *
 * @package App\Filament\Owner\Resources
 */
class HallResource extends OwnerResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = Hall::class;

    /**
     * The navigation icon for the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    /**
     * The navigation group for the resource.
     *
     * @var string|null
     */
    //protected static ?string $navigationGroup = 'Hall Management';

    public static function getNavigationGroup(): ?string
    {
        return __('owner.nav_groups.hall_management');
    }

    /**
     * The navigation sort order.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 1;

    /**
     * The record title attribute.
     *
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('owner.halls.navigation');
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('owner.halls.singular');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner.halls.plural');
    }

    /**
     * Get the navigation badge showing hall count.
     */
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $count = Hall::where('owner_id', $user->id)->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    /**
     * Apply owner scope to halls query.
     * Only shows halls owned by the current user.
     */
    protected static function applyOwnerScope(Builder $query, $user): Builder
    {
        return $query->where('owner_id', $user->id);
    }

    /**
     * Configure the form for creating/editing halls.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Form Tabs
                Forms\Components\Tabs::make('Hall')
                    ->tabs([
                        // ==================== TAB 1: Basic Information ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.basic'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.basic_info'))
                                    ->description(__('owner.halls.sections.basic_info_desc'))
                                    ->columns(2)
                                    ->schema([
                                        // Name (English)
                                        Forms\Components\TextInput::make('name.en')
                                            ->label(__('owner.halls.fields.name_en'))
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Grand Celebration Hall')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Forms\Set $set, ?string $state, ?string $old) {
                                                if (blank($old)) {
                                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                                }
                                            }),

                                        // Name (Arabic)
                                        Forms\Components\TextInput::make('name.ar')
                                            ->label(__('owner.halls.fields.name_ar'))
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('قاعة الاحتفالات الكبرى')
                                            ->extraInputAttributes(['dir' => 'rtl']),

                                        // Description (English)
                                        Forms\Components\RichEditor::make('description.en')
                                            ->label(__('owner.halls.fields.description_en'))
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                                'redo',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),

                                        // Description (Arabic)
                                        Forms\Components\RichEditor::make('description.ar')
                                            ->label(__('owner.halls.fields.description_ar'))
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                                'redo',
                                                'undo',
                                            ])
                                            ->extraInputAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),

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



                                        // City
                                        // Forms\Components\Select::make('city_id')
                                        //     ->label(__('owner.halls.fields.city'))
                                        //     ->required()
                                        //     ->searchable()
                                        //     ->preload()
                                        //     ->options(function () {
                                        //         return City::with('region')
                                        //             ->get()
                                        //             ->mapWithKeys(function ($city) {
                                        //                 $locale = app()->getLocale();
                                        //                 $cityName = $city->getTranslation('name', $locale);
                                        //                 $regionName = $city->region?->getTranslation('name', $locale) ?? '';
                                        //                 return [$city->id => "{$cityName} ({$regionName})"];
                                        //             });
                                        //     }),

                                        // Slug (auto-generated)
                                        Forms\Components\TextInput::make('slug')
                                            ->label(__('owner.halls.fields.slug'))
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->alphaDash()
                                            ->helperText(__('owner.halls.helpers.slug')),
                                    ]),
                            ]),

                        // ==================== TAB 2: Location ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.location'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.address'))
                                    ->columns(2)
                                    ->schema([
                                        // Address (English)
                                        Forms\Components\TextInput::make('address')
                                            ->label(__('owner.halls.fields.address_en'))
                                            ->required()
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        // Address (Arabic)
                                        Forms\Components\TextInput::make('address_localized.ar')
                                            ->label(__('owner.halls.fields.address_ar'))
                                            ->maxLength(500)
                                            ->extraInputAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),

                                        // Google Maps URL
                                        Forms\Components\TextInput::make('google_maps_url')
                                            ->label(__('owner.halls.fields.google_maps_url'))
                                            ->url()
                                            ->maxLength(1000)
                                            ->prefix('🗺️')
                                            ->placeholder('https://maps.google.com/...')
                                            ->columnSpanFull(),

                                        // Coordinates
                                        Forms\Components\TextInput::make('latitude')
                                            ->label(__('owner.halls.fields.latitude'))
                                            ->numeric()
                                            ->required()
                                            ->step(0.000001)
                                            ->placeholder('23.588462'),

                                        Forms\Components\TextInput::make('longitude')
                                            ->label(__('owner.halls.fields.longitude'))
                                            ->numeric()
                                            ->required()
                                            ->step(0.000001)
                                            ->placeholder('58.382935'),
                                    ]),
                            ]),

                        // ==================== TAB 3: Capacity & Pricing ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.pricing'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                // Capacity Section
                                Forms\Components\Section::make(__('owner.halls.sections.capacity'))
                                    ->columns(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('capacity_min')
                                            ->label(__('owner.halls.fields.capacity_min'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(10),

                                        Forms\Components\TextInput::make('capacity_max')
                                            ->label(__('owner.halls.fields.capacity_max'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(100)
                                            ->gte('capacity_min'),

                                        Forms\Components\TextInput::make('area')
                                            ->label(__('owner.halls.fields.area'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->required()
                                            ->suffix('m²'),
                                    ]),

                                // Pricing Section
                                Forms\Components\Section::make(__('owner.halls.sections.pricing'))
                                    ->description(__('owner.halls.sections.pricing_desc'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('price_per_slot')
                                            ->label(__('owner.halls.fields.base_price'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->helperText(__('owner.halls.helpers.base_price')),

                                        Forms\Components\Placeholder::make('pricing_info')
                                            ->content(__('owner.halls.helpers.pricing_override_info'))
                                            ->columnSpanFull(),

                                        // Slot-specific pricing overrides
                                        Forms\Components\TextInput::make('pricing_override.morning')
                                            ->label(__('owner.halls.fields.price_morning'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->placeholder(__('owner.halls.placeholders.use_base')),

                                        Forms\Components\TextInput::make('pricing_override.afternoon')
                                            ->label(__('owner.halls.fields.price_afternoon'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->placeholder(__('owner.halls.placeholders.use_base')),

                                        Forms\Components\TextInput::make('pricing_override.evening')
                                            ->label(__('owner.halls.fields.price_evening'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->placeholder(__('owner.halls.placeholders.use_base')),

                                        Forms\Components\TextInput::make('pricing_override.full_day')
                                            ->label(__('owner.halls.fields.price_full_day'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->placeholder(__('owner.halls.placeholders.use_base')),
                                    ]),

                                // Advance Payment Section
                                Forms\Components\Section::make(__('owner.halls.sections.advance_payment'))
                                    ->description(__('owner.halls.sections.advance_payment_desc'))
                                    ->columns(2)
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Toggle::make('advance_payment_required')
                                            ->label(__('owner.halls.fields.advance_required'))
                                            ->default(false)
                                            ->live()
                                            ->columnSpanFull(),

                                        Forms\Components\Select::make('advance_payment_type')
                                            ->label(__('owner.halls.fields.advance_type'))
                                            ->options([
                                                'percentage' => __('owner.halls.advance_types.percentage'),
                                                'fixed' => __('owner.halls.advance_types.fixed'),
                                            ])
                                            ->default('percentage')
                                            ->visible(fn(Forms\Get $get): bool => $get('advance_payment_required'))
                                            ->live(),

                                        Forms\Components\TextInput::make('advance_payment_value')
                                            ->label(fn(Forms\Get $get): string => $get('advance_payment_type') === 'percentage'
                                                ? __('owner.halls.fields.advance_percentage')
                                                : __('owner.halls.fields.advance_amount'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix(fn(Forms\Get $get): string => $get('advance_payment_type') === 'percentage' ? '%' : 'OMR')
                                            ->visible(fn(Forms\Get $get): bool => $get('advance_payment_required')),

                                        Forms\Components\TextInput::make('advance_payment_minimum')
                                            ->label(__('owner.halls.fields.advance_minimum'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->helperText(__('owner.halls.helpers.advance_minimum'))
                                            ->visible(fn(Forms\Get $get): bool => $get('advance_payment_required') && $get('advance_payment_type') === 'percentage'),
                                    ]),
                            ]),

                        // ==================== TAB 4: Features ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.features'))
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.amenities'))
                                    ->description(__('owner.halls.sections.amenities_desc'))
                                    ->schema([
                                        Forms\Components\CheckboxList::make('features')
                                            ->label('')
                                            ->options(function () {
                                                $locale = app()->getLocale();
                                                return HallFeature::where('is_active', true)
                                                    //->orderBy('sort_order')
                                                    ->get()
                                                    ->mapWithKeys(fn($feature) => [
                                                        $feature->id => $feature->getTranslation('name', $locale),
                                                    ]);
                                            })
                                            ->columns(3)
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->gridDirection('row'),
                                    ]),
                            ]),

                        // ==================== TAB 5: Media ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.images'))
                                    ->schema([
                                        // Featured Image
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label(__('owner.halls.fields.featured_image'))
                                            ->image()
                                            ->imageEditor()
                                            ->directory('halls/featured')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('1080')
                                            ->visibility('public')
                                            //->maxSize(5120)
                                            ->helperText(__('owner.halls.helpers.featured_image')),

                                        // Gallery
                                        Forms\Components\FileUpload::make('gallery')
                                            ->label(__('owner.halls.fields.gallery'))
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->directory('halls/gallery')
                                            ->visibility('public')
                                            //->maxSize(5120)
                                            ->maxFiles(20)
                                            ->helperText(__('owner.halls.helpers.gallery')),
                                    ]),

                                Forms\Components\Section::make(__('owner.halls.sections.video'))
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\TextInput::make('video_url')
                                            ->label(__('owner.halls.fields.video_url'))
                                            ->url()
                                            ->placeholder('https://youtube.com/watch?v=...')
                                            ->helperText(__('owner.halls.helpers.video_url')),

                                        Forms\Components\TextInput::make('virtual_tour_url')
                                            ->label(__('owner.halls.fields.virtual_tour'))
                                            ->url()
                                            ->placeholder('https://...')
                                            ->helperText(__('owner.halls.helpers.virtual_tour')),
                                    ]),
                            ]),

                        // ==================== TAB 6: Contact ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.contact'))
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.contact'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label(__('owner.halls.fields.phone'))
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('+968 9XXX XXXX'),

                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label(__('owner.halls.fields.whatsapp'))
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('+968 9XXX XXXX'),

                                        Forms\Components\TextInput::make('email')
                                            ->label(__('owner.halls.fields.email'))
                                            ->email()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==================== TAB 7: Settings ====================
                        // ==================== TAB 7: Settings ====================
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->badge(fn(Forms\Get $get): ?string => !$get('is_active') ? __('owner.halls.badges.inactive') : null)
                            ->badgeColor('danger')
                            ->schema([
                                // ===========================================
                                // SECTION 1: STATUS SETTINGS
                                // ===========================================
                                Forms\Components\Section::make(__('owner.halls.sections.status'))
                                    ->description(__('owner.halls.sections.status_desc'))
                                    ->icon('heroicon-o-signal')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        // Hall Active Status Toggle
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('owner.halls.fields.is_active'))
                                            ->helperText(__('owner.halls.helpers.is_active'))
                                            ->default(true)
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ->live(),

                                        // Featured Status - Display Only (Admin Controlled)
                                        Forms\Components\Placeholder::make('featured_status')
                                            ->label(__('owner.halls.fields.is_featured'))
                                            ->content(function ($record): \Illuminate\Support\HtmlString {
                                                if (!$record) {
                                                    return new \Illuminate\Support\HtmlString(
                                                        '<span class="text-sm text-gray-500">' .
                                                            __('owner.halls.placeholders.not_available') .
                                                            '</span>'
                                                    );
                                                }

                                                $isFeatured = (bool) ($record->is_featured ?? false);

                                                if ($isFeatured) {
                                                    return new \Illuminate\Support\HtmlString(
                                                        '<div class="flex items-center gap-2">' .
                                                            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">' .
                                                            '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>' .
                                                            __('owner.halls.status.featured') .
                                                            '</span></div>'
                                                    );
                                                }

                                                return new \Illuminate\Support\HtmlString(
                                                    '<span class="text-sm text-gray-500">' .
                                                        __('owner.halls.status.not_featured') .
                                                        '</span>'
                                                );
                                            })
                                            ->hint(__('owner.halls.helpers.featured_admin_only'))
                                            ->hintIcon('heroicon-o-information-circle')
                                            ->hintColor('info'),

                                        // Status Summary Card - Full Width
                                        Forms\Components\Placeholder::make('status_summary')
                                            ->label('')
                                            ->content(function ($record, Forms\Get $get): \Illuminate\Support\HtmlString {
                                                $isActive = $get('is_active') ?? ($record?->is_active ?? true);

                                                if (!$isActive) {
                                                    return new \Illuminate\Support\HtmlString(
                                                        '<div class="p-4 border rounded-lg bg-danger-50 dark:bg-danger-950 border-danger-200 dark:border-danger-800">' .
                                                            '<div class="flex items-center gap-3">' .
                                                            '<svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
                                                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>' .
                                                            '</svg>' .
                                                            '<div>' .
                                                            '<p class="font-semibold text-danger-700 dark:text-danger-300">' . __('owner.halls.alerts.inactive_title') . '</p>' .
                                                            '<p class="text-sm text-danger-600 dark:text-danger-400">' . __('owner.halls.alerts.inactive_message') . '</p>' .
                                                            '</div></div></div>'
                                                    );
                                                }

                                                return new \Illuminate\Support\HtmlString(
                                                    '<div class="p-4 border rounded-lg bg-success-50 dark:bg-success-950 border-success-200 dark:border-success-800">' .
                                                        '<div class="flex items-center gap-3">' .
                                                        '<svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
                                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' .
                                                        '</svg>' .
                                                        '<div>' .
                                                        '<p class="font-semibold text-success-700 dark:text-success-300">' . __('owner.halls.alerts.active_title') . '</p>' .
                                                        '<p class="text-sm text-success-600 dark:text-success-400">' . __('owner.halls.alerts.active_message') . '</p>' .
                                                        '</div></div></div>'
                                                );
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                // ===========================================
                                // SECTION 2: BOOKING SETTINGS
                                // ===========================================
                                Forms\Components\Section::make(__('owner.halls.sections.booking_settings'))
                                    ->description(__('owner.halls.sections.booking_settings_desc'))
                                    ->icon('heroicon-o-calendar-days')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        // Requires Approval Toggle
                                        Forms\Components\Toggle::make('requires_approval')
                                            ->label(__('owner.halls.fields.requires_approval'))
                                            ->helperText(__('owner.halls.helpers.requires_approval'))
                                            ->default(false)
                                            ->onColor('warning')
                                            ->offColor('gray')
                                            ->inline(false)
                                            ->live()
                                            ->columnSpanFull(),

                                        // Approval Info Card
                                        Forms\Components\Placeholder::make('approval_info')
                                            ->label('')
                                            ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                                                $requiresApproval = $get('requires_approval') ?? false;

                                                if ($requiresApproval) {
                                                    return new \Illuminate\Support\HtmlString(
                                                        '<div class="p-3 text-sm border rounded-lg bg-warning-50 dark:bg-warning-950 border-warning-200 dark:border-warning-800">' .
                                                            '<div class="flex items-start gap-2">' .
                                                            '<svg class="w-5 h-5 text-warning-600 dark:text-warning-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
                                                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>' .
                                                            '</svg>' .
                                                            '<div class="text-warning-700 dark:text-warning-300">' .
                                                            '<strong>' . __('owner.halls.alerts.approval_enabled_title') . '</strong><br>' .
                                                            __('owner.halls.alerts.approval_enabled_message') .
                                                            '</div></div></div>'
                                                    );
                                                }

                                                return new \Illuminate\Support\HtmlString(
                                                    '<div class="p-3 text-sm text-gray-600 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400">' .
                                                        '<strong>' . __('owner.halls.alerts.auto_approval_title') . '</strong><br>' .
                                                        __('owner.halls.alerts.auto_approval_message') .
                                                        '</div>'
                                                );
                                            })
                                            ->columnSpanFull(),

                                        // Cancellation Hours
                                        Forms\Components\TextInput::make('cancellation_hours')
                                            ->label(__('owner.halls.fields.cancellation_hours'))
                                            ->helperText(__('owner.halls.helpers.cancellation_hours'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(720)
                                            ->step(1)
                                            ->default(24)
                                            ->suffix(__('owner.halls.suffixes.hours'))
                                            ->prefixIcon('heroicon-o-clock')
                                            ->required()
                                            ->live(),

                                        // Cancellation Fee Percentage
                                        Forms\Components\TextInput::make('cancellation_fee_percentage')
                                            ->label(__('owner.halls.fields.cancellation_fee'))
                                            ->helperText(__('owner.halls.helpers.cancellation_fee'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.01)
                                            ->default(0)
                                            ->suffix('%')
                                            ->prefixIcon('heroicon-o-receipt-percent')
                                            ->required(),

                                        // Cancellation Policy Summary
                                        Forms\Components\Placeholder::make('cancellation_summary')
                                            ->label(__('owner.halls.fields.cancellation_policy'))
                                            ->content(function (Forms\Get $get): \Illuminate\Support\HtmlString {
                                                $hours = (int) ($get('cancellation_hours') ?? 24);
                                                $fee = (float) ($get('cancellation_fee_percentage') ?? 0);

                                                $days = (int) floor($hours / 24);
                                                $remainingHours = $hours % 24;

                                                // Build time string
                                                $timeString = '';
                                                if ($days > 0) {
                                                    $timeString .= $days . ' ' . __('owner.halls.time.days');
                                                    if ($remainingHours > 0) {
                                                        $timeString .= ' ' . __('owner.halls.time.and') . ' ';
                                                    }
                                                }
                                                if ($remainingHours > 0 || $days === 0) {
                                                    $timeString .= $remainingHours . ' ' . __('owner.halls.time.hours');
                                                }

                                                return new \Illuminate\Support\HtmlString(
                                                    '<div class="p-4 border rounded-lg bg-primary-50 dark:bg-primary-950 border-primary-200 dark:border-primary-800">' .
                                                        '<p class="mb-2 font-medium text-primary-900 dark:text-primary-100">' .
                                                        __('owner.halls.policy.title') .
                                                        '</p>' .
                                                        '<ul class="space-y-1 text-sm list-disc list-inside text-primary-700 dark:text-primary-300">' .
                                                        '<li>' . __('owner.halls.policy.free_before', ['time' => $timeString]) . '</li>' .
                                                        ($fee > 0
                                                            ? '<li>' . __('owner.halls.policy.fee_after', ['fee' => number_format($fee, 2)]) . '</li>'
                                                            : '<li>' . __('owner.halls.policy.no_fee') . '</li>') .
                                                        '</ul></div>'
                                                );
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                // ===========================================
                                // SECTION 3: SEO SETTINGS (Optional/Advanced)
                                // ===========================================
                                Forms\Components\Section::make(__('owner.halls.sections.seo'))
                                    ->description(__('owner.halls.sections.seo_desc'))
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->collapsed()
                                    ->collapsible()
                                    ->schema([
                                        // SEO English Fields
                                        Forms\Components\Fieldset::make(__('owner.halls.fieldsets.seo_english'))
                                            ->schema([
                                                Forms\Components\TextInput::make('meta_title.en')
                                                    ->label(__('owner.halls.fields.meta_title'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_title'))
                                                    ->maxLength(60)
                                                    ->helperText(__('owner.halls.helpers.meta_title')),

                                                Forms\Components\Textarea::make('meta_description.en')
                                                    ->label(__('owner.halls.fields.meta_description'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_description'))
                                                    ->maxLength(160)
                                                    ->rows(3)
                                                    ->helperText(__('owner.halls.helpers.meta_description')),

                                                Forms\Components\TextInput::make('meta_keywords.en')
                                                    ->label(__('owner.halls.fields.meta_keywords'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_keywords'))
                                                    ->maxLength(255)
                                                    ->helperText(__('owner.halls.helpers.meta_keywords')),
                                            ])
                                            ->columns(1),

                                        // SEO Arabic Fields
                                        Forms\Components\Fieldset::make(__('owner.halls.fieldsets.seo_arabic'))
                                            ->schema([
                                                Forms\Components\TextInput::make('meta_title.ar')
                                                    ->label(__('owner.halls.fields.meta_title'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_title_ar'))
                                                    ->maxLength(60)
                                                    ->extraInputAttributes(['dir' => 'rtl'])
                                                    ->helperText(__('owner.halls.helpers.meta_title')),

                                                Forms\Components\Textarea::make('meta_description.ar')
                                                    ->label(__('owner.halls.fields.meta_description'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_description_ar'))
                                                    ->maxLength(160)
                                                    ->rows(3)
                                                    ->extraInputAttributes(['dir' => 'rtl'])
                                                    ->helperText(__('owner.halls.helpers.meta_description')),

                                                Forms\Components\TextInput::make('meta_keywords.ar')
                                                    ->label(__('owner.halls.fields.meta_keywords'))
                                                    ->placeholder(__('owner.halls.placeholders.meta_keywords_ar'))
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(['dir' => 'rtl'])
                                                    ->helperText(__('owner.halls.helpers.meta_keywords')),
                                            ])
                                            ->columns(1),

                                        // SEO Tips Card
                                        Forms\Components\Placeholder::make('seo_tips')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString(
                                                '<div class="p-4 border rounded-lg bg-info-50 dark:bg-info-950 border-info-200 dark:border-info-800">' .
                                                    '<p class="flex items-center gap-2 mb-2 font-medium text-info-900 dark:text-info-100">' .
                                                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
                                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' .
                                                    '</svg>' .
                                                    __('owner.halls.seo.tips_title') .
                                                    '</p>' .
                                                    '<ul class="space-y-1 text-sm list-disc list-inside text-info-700 dark:text-info-300">' .
                                                    '<li>' . __('owner.halls.seo.tip_1') . '</li>' .
                                                    '<li>' . __('owner.halls.seo.tip_2') . '</li>' .
                                                    '<li>' . __('owner.halls.seo.tip_3') . '</li>' .
                                                    '</ul></div>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),

                                // ===========================================
                                // SECTION 4: STATISTICS (Read-Only Display)
                                // ===========================================
                                Forms\Components\Section::make(__('owner.halls.sections.statistics'))
                                    ->description(__('owner.halls.sections.statistics_desc'))
                                    ->icon('heroicon-o-chart-bar')
                                    ->collapsed()
                                    ->collapsible()
                                    ->visible(fn($record): bool => $record !== null)
                                    ->schema([
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\Placeholder::make('total_bookings_stat')
                                                    ->label(__('owner.halls.stats.total_bookings'))
                                                    ->content(fn($record): string => number_format((int) ($record?->total_bookings ?? 0))),

                                                Forms\Components\Placeholder::make('average_rating_stat')
                                                    ->label(__('owner.halls.stats.average_rating'))
                                                    ->content(function ($record): \Illuminate\Support\HtmlString {
                                                        $rating = (float) ($record?->average_rating ?? 0);
                                                        $stars = str_repeat('★', (int) round($rating)) . str_repeat('☆', 5 - (int) round($rating));
                                                        return new \Illuminate\Support\HtmlString(
                                                            '<span class="text-yellow-500">' . $stars . '</span> ' .
                                                                '<span class="text-gray-600 dark:text-gray-400">(' . number_format($rating, 2) . ')</span>'
                                                        );
                                                    }),

                                                Forms\Components\Placeholder::make('total_reviews_stat')
                                                    ->label(__('owner.halls.stats.total_reviews'))
                                                    ->content(fn($record): string => number_format((int) ($record?->total_reviews ?? 0))),

                                                Forms\Components\Placeholder::make('created_at_stat')
                                                    ->label(__('owner.halls.stats.created_at'))
                                                    ->content(fn($record): string => $record?->created_at?->format('M d, Y') ?? '-'),
                                            ]),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    /**
     * Configure the table for listing halls.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Featured Image
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn() => asset('images/hall-placeholder.png')),

                // Hall Name
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.halls.columns.name'))
                    ->searchable(['name->en', 'name->ar'])
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
                    ->description(fn($record): string => $record->city?->getTranslation('name', app()->getLocale()) ?? ''),

                // Capacity
                Tables\Columns\TextColumn::make('capacity_display')
                    ->label(__('owner.halls.columns.capacity'))
                    ->state(fn($record): string => "{$record->capacity_min} - {$record->capacity_max}")
                    ->icon('heroicon-o-users')
                    ->color('gray'),

                // Base Price
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label(__('owner.halls.columns.price'))
                    ->money('OMR')
                    ->sortable()
                    ->color('success'),

                // Rating
                Tables\Columns\TextColumn::make('average_rating')
                    ->label(__('owner.halls.columns.rating'))
                    ->formatStateUsing(fn($state): string => $state > 0 ? number_format((float) $state, 1) . ' ★' : '-')
                    ->color(fn($state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),

                // Bookings Count
                Tables\Columns\TextColumn::make('total_bookings')
                    ->label(__('owner.halls.columns.bookings'))
                    // ->counts('bookings')
                    ->state(fn(Hall $record): int => $record->bookings()->count())
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('info'),

                // Active Status
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('owner.halls.columns.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Featured Badge
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('owner.halls.columns.featured'))
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Active Filter
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('owner.halls.filters.status'))
                    ->boolean()
                    ->trueLabel(__('owner.halls.filters.active'))
                    ->falseLabel(__('owner.halls.filters.inactive'))
                    ->native(false),
            ])
            ->actions([
                // View Action
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                // Edit Action
                Tables\Actions\EditAction::make()
                    ->iconButton(),

                // Availability Action
                Tables\Actions\Action::make('availability')
                    ->label(__('owner.halls.actions.availability'))
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->url(fn($record) => static::getUrl('availability', ['record' => $record])),

                // Toggle Active Status
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn($record): string => $record->is_active
                        ? __('owner.halls.actions.deactivate')
                        : __('owner.halls.actions.activate'))
                    ->icon(fn($record): string => $record->is_active
                        ? 'heroicon-o-pause'
                        : 'heroicon-o-play')
                    ->color(fn($record): string => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update(['is_active' => !$record->is_active]);

                        Notification::make()
                            ->success()
                            ->title($record->is_active
                                ? __('owner.halls.notifications.activated')
                                : __('owner.halls.notifications.deactivated'))
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('owner.halls.bulk.activate'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('owner.halls.bulk.deactivate'))
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->emptyStateHeading(__('owner.halls.empty.heading'))
            ->emptyStateDescription(__('owner.halls.empty.description'))
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('owner.halls.empty.action')),
            ]);
    }

    /**
     * Configure the infolist for viewing hall details.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section with Image
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\ImageEntry::make('featured_image')
                                ->hiddenLabel()
                                ->height(200)
                                ->grow(false),

                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('owner.halls.fields.name'))
                                    ->formatStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->label(__('owner.halls.fields.city'))
                                    ->formatStateUsing(fn($record) => $record->city?->getTranslation('name', app()->getLocale()))
                                    ->icon('heroicon-o-map-pin'),


                                Infolists\Components\Grid::make(4)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('total_bookings')
                                            ->label(__('owner.halls.stats.bookings'))
                                            ->badge()
                                            ->state(function ($record) {
                                                return $record->bookings()->count();
                                            })
                                            ->color('info'),

                                        Infolists\Components\TextEntry::make('average_rating')
                                            ->label(__('owner.halls.stats.rating'))
                                            ->formatStateUsing(fn($state) => $state > 0 ? number_format((float) $state, 1) . ' ★' : '-')
                                            ->badge()
                                            ->color('warning'),

                                        Infolists\Components\TextEntry::make('total_reviews')
                                            ->label(__('owner.halls.stats.reviews'))
                                            ->badge()
                                            ->color('success'),

                                        Infolists\Components\TextEntry::make('price_per_slot')
                                            ->label(__('owner.halls.stats.price'))
                                            ->money('OMR')
                                            ->badge()
                                            ->color('primary'),
                                    ]),
                            ])->grow(),
                        ])->from('md'),
                    ]),

                // Details Section
                Infolists\Components\Section::make(__('owner.halls.sections.details'))
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('owner.halls.fields.description'))
                            ->formatStateUsing(fn($record) => $record->getTranslation('description', app()->getLocale()))
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('capacity_display')
                            ->label(__('owner.halls.fields.capacity'))
                            ->state(fn($record): string => "{$record->capacity_min} - {$record->capacity_max} " . __('owner.halls.guests')),

                        Infolists\Components\TextEntry::make('area')
                            ->label(__('owner.halls.fields.area'))
                            ->suffix(' m²'),

                        Infolists\Components\TextEntry::make('address')
                            ->label(__('owner.halls.fields.address_ar')),
                        //->columnSpanFull(),
                    ]),

                // Status Section
                Infolists\Components\Section::make(__('owner.halls.sections.status'))
                    ->columns(3)
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('owner.halls.fields.is_active'))
                            ->boolean(),

                        Infolists\Components\IconEntry::make('is_featured')
                            ->label(__('owner.halls.fields.is_featured'))
                            ->boolean(),

                        Infolists\Components\IconEntry::make('requires_approval')
                            ->label(__('owner.halls.fields.requires_approval'))
                            ->boolean(),
                    ]),
            ]);
    }

    /**
     * Get the relations for the resource.
     *
     * Note: Images are stored in 'gallery' JSON field, not a separate table
     * Reviews table may not exist yet - uncomment when ready
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\AvailabilitiesRelationManager::class,
            RelationManagers\ExtraServicesRelationManager::class,
            RelationManagers\BookingsRelationManager::class,
            // RelationManagers\ImagesRelationManager::class, // Images are in gallery JSON field
            // RelationManagers\ReviewsRelationManager::class, // Enable when reviews table exists
        ];
    }

    /**
     * Get the pages for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHalls::route('/'),
            'create' => Pages\CreateHall::route('/create'),
            'view' => Pages\ViewHall::route('/{record}'),
            'edit' => Pages\EditHall::route('/{record}/edit'),
            'availability' => Pages\ManageHallAvailability::route('/{record}/availability'),
        ];
    }

    /**
     * Get the Eloquent query with soft deletes.
     */
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }
}
