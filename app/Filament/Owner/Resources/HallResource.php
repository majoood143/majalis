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
    protected static ?string $navigationGroup = 'Hall Management';

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
                                                'bold', 'italic', 'underline', 'strike',
                                                'bulletList', 'orderedList',
                                                'h2', 'h3',
                                                'redo', 'undo',
                                            ])
                                            ->columnSpanFull(),

                                        // Description (Arabic)
                                        Forms\Components\RichEditor::make('description.ar')
                                            ->label(__('owner.halls.fields.description_ar'))
                                            ->required()
                                            ->toolbarButtons([
                                                'bold', 'italic', 'underline', 'strike',
                                                'bulletList', 'orderedList',
                                                'h2', 'h3',
                                                'redo', 'undo',
                                            ])
                                            ->extraInputAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),

                                        // City
                                        Forms\Components\Select::make('city_id')
                                            ->label(__('owner.halls.fields.city'))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->options(function () {
                                                return City::with('region')
                                                    ->get()
                                                    ->mapWithKeys(function ($city) {
                                                        $locale = app()->getLocale();
                                                        $cityName = $city->getTranslation('name', $locale);
                                                        $regionName = $city->region?->getTranslation('name', $locale) ?? '';
                                                        return [$city->id => "{$cityName} ({$regionName})"];
                                                    });
                                            }),

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
                                            ->step(0.000001)
                                            ->placeholder('23.588462'),

                                        Forms\Components\TextInput::make('longitude')
                                            ->label(__('owner.halls.fields.longitude'))
                                            ->numeric()
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
                                            ->visible(fn (Forms\Get $get): bool => $get('advance_payment_required'))
                                            ->live(),

                                        Forms\Components\TextInput::make('advance_payment_value')
                                            ->label(fn (Forms\Get $get): string => $get('advance_payment_type') === 'percentage'
                                                ? __('owner.halls.fields.advance_percentage')
                                                : __('owner.halls.fields.advance_amount'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix(fn (Forms\Get $get): string => $get('advance_payment_type') === 'percentage' ? '%' : 'OMR')
                                            ->visible(fn (Forms\Get $get): bool => $get('advance_payment_required')),

                                        Forms\Components\TextInput::make('advance_payment_minimum')
                                            ->label(__('owner.halls.fields.advance_minimum'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.001)
                                            ->prefix('OMR')
                                            ->helperText(__('owner.halls.helpers.advance_minimum'))
                                            ->visible(fn (Forms\Get $get): bool => $get('advance_payment_required') && $get('advance_payment_type') === 'percentage'),
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
                                                    ->orderBy('sort_order')
                                                    ->get()
                                                    ->mapWithKeys(fn ($feature) => [
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
                                            ->visibility('public')
                                            ->maxSize(5120)
                                            ->helperText(__('owner.halls.helpers.featured_image')),

                                        // Gallery
                                        Forms\Components\FileUpload::make('gallery')
                                            ->label(__('owner.halls.fields.gallery'))
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->directory('halls/gallery')
                                            ->visibility('public')
                                            ->maxSize(5120)
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
                        Forms\Components\Tabs\Tab::make(__('owner.halls.tabs.settings'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Forms\Components\Section::make(__('owner.halls.sections.status'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('owner.halls.fields.is_active'))
                                            ->default(true)
                                            ->helperText(__('owner.halls.helpers.is_active')),

                                        Forms\Components\Placeholder::make('featured_note')
                                            ->label(__('owner.halls.fields.is_featured'))
                                            ->content(__('owner.halls.helpers.featured_admin_only')),
                                    ]),

                                Forms\Components\Section::make(__('owner.halls.sections.booking_settings'))
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('requires_approval')
                                            ->label(__('owner.halls.fields.requires_approval'))
                                            ->default(false)
                                            ->helperText(__('owner.halls.helpers.requires_approval')),

                                        Forms\Components\TextInput::make('cancellation_hours')
                                            ->label(__('owner.halls.fields.cancellation_hours'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(24)
                                            ->suffix(__('owner.halls.suffixes.hours'))
                                            ->helperText(__('owner.halls.helpers.cancellation_hours')),

                                        Forms\Components\TextInput::make('cancellation_fee_percentage')
                                            ->label(__('owner.halls.fields.cancellation_fee'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.01)
                                            ->default(0)
                                            ->suffix('%'),
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
                    ->defaultImageUrl(fn () => asset('images/hall-placeholder.png')),

                // Hall Name
                Tables\Columns\TextColumn::make('name')
                    ->label(__('owner.halls.columns.name'))
                    ->searchable(['name->en', 'name->ar'])
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                    ->description(fn ($record): string => $record->city?->getTranslation('name', app()->getLocale()) ?? ''),

                // Capacity
                Tables\Columns\TextColumn::make('capacity_display')
                    ->label(__('owner.halls.columns.capacity'))
                    ->state(fn ($record): string => "{$record->capacity_min} - {$record->capacity_max}")
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
                    ->formatStateUsing(fn ($state): string => $state > 0 ? number_format((float) $state, 1) . ' ★' : '-')
                    ->color(fn ($state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),

                // Bookings Count
                Tables\Columns\TextColumn::make('total_bookings')
                    ->label(__('owner.halls.columns.bookings'))
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
                    ->url(fn ($record) => static::getUrl('availability', ['record' => $record])),

                // Toggle Active Status
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record): string => $record->is_active
                        ? __('owner.halls.actions.deactivate')
                        : __('owner.halls.actions.activate'))
                    ->icon(fn ($record): string => $record->is_active
                        ? 'heroicon-o-pause'
                        : 'heroicon-o-play')
                    ->color(fn ($record): string => $record->is_active ? 'warning' : 'success')
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
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('owner.halls.bulk.deactivate'))
                        ->icon('heroicon-o-x-mark')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
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
                                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('city.name')
                                    ->label(__('owner.halls.fields.city'))
                                    ->formatStateUsing(fn ($record) => $record->city?->getTranslation('name', app()->getLocale()))
                                    ->icon('heroicon-o-map-pin'),

                                Infolists\Components\Grid::make(4)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('total_bookings')
                                            ->label(__('owner.halls.stats.bookings'))
                                            ->badge()
                                            ->color('info'),

                                        Infolists\Components\TextEntry::make('average_rating')
                                            ->label(__('owner.halls.stats.rating'))
                                            ->formatStateUsing(fn ($state) => $state > 0 ? number_format((float) $state, 1) . ' ★' : '-')
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
                            ->formatStateUsing(fn ($record) => $record->getTranslation('description', app()->getLocale()))
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('capacity_display')
                            ->label(__('owner.halls.fields.capacity'))
                            ->state(fn ($record): string => "{$record->capacity_min} - {$record->capacity_max} " . __('owner.halls.guests')),

                        Infolists\Components\TextEntry::make('area_sqm')
                            ->label(__('owner.halls.fields.area'))
                            ->suffix(' m²'),

                        Infolists\Components\TextEntry::make('address')
                            ->label(__('owner.halls.fields.address'))
                            ->columnSpanFull(),
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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
