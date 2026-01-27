<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\HallAvailability;
use App\Models\Region;
use App\Models\City;
use App\Models\ExtraService;
use App\Services\InvoiceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Filament\Traits\HasTranslations;
use Filament\Tables\Actions\Action;
use App\Filament\Components\GuestBookingComponents;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;



class BookingResource extends Resource
{
    use HasTranslations;

    // ✅ Use METHOD override (not property) - avoids PHP trait conflicts
    protected static function getTranslationNamespace(): string
    {
        return 'booking';
    }

    public static function getModelLabel(): string
    {
        return static::trans('resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::trans('resource.plural_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return static::trans('resource.navigation_group');
    }

    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Booking Management';

    protected static ?int $navigationSort = 1;

    // Specify the translation file namespace
    protected static ?string $translationNamespace = 'booking';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Hall Selection Section with Region & City Filters
                Forms\Components\Section::make(__('booking.sections.hall_selection.title'))
                    ->description(__('booking.sections.hall_selection.description'))
                    ->schema([
                        // Forms\Components\Select::make('region_id')
                        //     ->label(__('booking.fields.region_id.label'))
                        //     ->options(fn() => Region::where('is_active', true)->ordered()->pluck('name', 'id'))
                        //     ->searchable()
                        //     ->preload()
                        //     ->live()
                        //     ->afterStateUpdated(function (Set $set) {
                        //         // Reset dependent fields when region changes
                        //         $set('city_id', null);
                        //         $set('hall_id', null);
                        //         $set('booking_date', null);
                        //         $set('time_slot', null);
                        //         $set('hall_price', 0);
                        //     })
                        //     ->helperText(__('booking.fields.region_id.helper')),

                        Forms\Components\Select::make('region_id')
                            ->label(__('booking.fields.region_id.label'))
                            ->options(fn() => Region::where('is_active', true)->ordered()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            // ✅ FIX: Hydrate region_id from hall relationship when editing
                            ->afterStateHydrated(function (Forms\Components\Select $component, ?string $state, ?Booking $record) {
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

                        // Forms\Components\Select::make('city_id')
                        //     ->label(__('booking.fields.city_id.label'))
                        //     ->options(
                        //         fn(Get $get): Collection => City::query()
                        //             ->when(
                        //                 $get('region_id'),
                        //                 fn($query, $regionId) =>
                        //                 $query->where('region_id', $regionId)
                        //             )
                        //             ->where('is_active', true)
                        //             ->ordered()
                        //             ->pluck('name', 'id')
                        //     )
                        //     ->searchable()
                        //     ->preload()
                        //     ->live()
                        //     ->afterStateUpdated(function (Set $set) {
                        //         // Reset dependent fields when city changes
                        //         $set('hall_id', null);
                        //         $set('booking_date', null);
                        //         $set('time_slot', null);
                        //         $set('hall_price', 0);
                        //     })
                        //     ->disabled(fn(Get $get): bool => !$get('region_id'))
                        //     ->helperText(__('booking.fields.region_id.helper')),

                        Forms\Components\Select::make('city_id')
                            ->label(__('booking.fields.city_id.label'))
                            ->options(function (Get $get, ?Booking $record): Collection {
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
                            ->afterStateHydrated(function (Forms\Components\Select $component, ?string $state, ?Booking $record) {
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
                            ->disabled(fn(Get $get, ?Booking $record): bool => !$get('region_id') && !$record?->hall_id)
                            ->helperText(__('booking.fields.city_id.helper')),

                        Forms\Components\Select::make('hall_id')
                            ->label(__('booking.fields.hall_id.label'))
                            ->options(
                                fn(Get $get): Collection => Hall::query()
                                    ->when(
                                        $get('city_id'),
                                        fn($query, $cityId) =>
                                        $query->where('city_id', $cityId)
                                    )
                                    ->when(
                                        !$get('city_id') && $get('region_id'),
                                        fn($query) =>
                                        $query->whereHas('city', fn($q) => $q->where('region_id', $get('region_id')))
                                    )
                                    ->where('is_active', true)
                                    ->with(['city', 'owner'])
                                    ->get()
                                    ->mapWithKeys(function ($hall) {
                                        $hallName = is_array($hall->name)
                                            ? ($hall->name['en'] ?? $hall->name['ar'] ?? 'Unnamed Hall')
                                            : $hall->name;

                                        $cityName = is_array($hall->city->name)
                                            ? ($hall->city->name['en'] ?? $hall->city->name['ar'] ?? 'Unknown')
                                            : $hall->city->name;

                                        $ownerName = $hall->owner->name ?? 'Unknown Owner';

                                        return [$hall->id => "{$hallName} - {$cityName} ({$ownerName})"];
                                    })
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                // Reset dependent fields
                                $set('booking_date', null);
                                $set('time_slot', null);
                                $set('hall_price', 0);
                                $set('extra_services', []);

                                // Load hall capacity for validation
                                if ($state) {
                                    $hall = Hall::find($state);
                                    if ($hall) {
                                        $set('number_of_guests', $hall->capacity_min);
                                    }
                                }
                            })
                            ->disabled(fn(Get $get): bool => !$get('city_id') && !$get('region_id'))
                            ->helperText(__('booking.fields.hall_id.helper')),
                    ])->columns(3)
                    ->collapsible(),

                // Booking Details Section
                Forms\Components\Section::make(__('booking.sections.booking_details.title'))
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label(__('booking.fields.booking_number.label'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($context) => $context === 'edit')
                            ->columnSpan(1),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label(__('booking.fields.booking_date.label'))
                            ->required()
                            ->native(false)
                            ->minDate(now())
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                // Reset time slot when date changes
                                $set('time_slot', null);
                                $set('hall_price', 0);

                                // Check if date has any available slots
                                if ($state && $get('hall_id')) {
                                    $availableSlots = static::getAvailableTimeSlots($get('hall_id'), $state);
                                    if (empty($availableSlots)) {
                                        Notification::make()
                                            ->warning()
                                            ->title('No Available Slots')
                                            ->body('All time slots are booked for this date. Please select another date.')
                                            ->send();
                                    }
                                }
                            })
                            ->disabled(fn(Get $get): bool => !$get('hall_id'))
                            ->helperText(fn(Get $get) => static::getDateHelperText($get('hall_id'), $get('booking_date')))
                            ->columnSpan(1),

                        // Forms\Components\Select::make('time_slot')
                        //     ->label(__('booking.fields.time_slot.label'))
                        //     ->options(fn(Get $get): array => static::getAvailableTimeSlots(
                        //         $get('hall_id'),
                        //         $get('booking_date')
                        //     ))
                        //     ->required()
                        //     ->live()
                        //     ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                        //         if ($state && $get('hall_id') && $get('booking_date')) {
                        //             static::updateHallPrice($set, $get('hall_id'), $get('booking_date'), $state);
                        //         }
                        //     })
                        //     ->disabled(fn(Get $get): bool => !$get('booking_date'))
                        //     ->helperText(fn(Get $get) => static::getTimeSlotHelperText($get('hall_id'), $get('booking_date')))
                        //     ->columnSpan(1),

                        Forms\Components\Select::make('time_slot')
                            ->label(__('booking.fields.time_slot.label'))
                            // ✅ FIX: Pass current booking ID to exclude from "booked" filter when editing
                            ->options(fn(Get $get, ?Booking $record): array => static::getAvailableTimeSlots(
                                $get('hall_id'),
                                $get('booking_date'),
                                $record?->id // Pass current booking ID to exclude from booked slots
                            ))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('hall_id') && $get('booking_date')) {
                                    static::updateHallPrice($set, $get('hall_id'), $get('booking_date'), $state);
                                }
                            })
                            ->disabled(fn(Get $get): bool => !$get('booking_date'))
                            ->helperText(fn(Get $get) => static::getTimeSlotHelperText($get('hall_id'), $get('booking_date')))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('number_of_guests')
                            ->label(__('booking.fields.number_of_guests.label'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Validate against hall capacity
                                if ($get('hall_id') && $state) {
                                    $hall = Hall::find($get('hall_id'));
                                    if ($hall) {
                                        if ($state < $hall->capacity_min) {
                                            $set('number_of_guests', $hall->capacity_min);
                                        } elseif ($state > $hall->capacity_max) {
                                            $set('number_of_guests', $hall->capacity_max);
                                        }
                                    }
                                }
                            })
                            ->helperText(fn(Get $get) => static::getCapacityHelperText($get('hall_id')))
                            ->columnSpan(1),
                    ])->columns(2),

                // Customer Information Section
                Forms\Components\Section::make(__('booking.sections.customer_details'))
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label(__('booking.fields.customer_name.label'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label(__('booking.fields.customer_email.label'))
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label(__('booking.fields.customer_phone.label'))
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('customer_notes')

                            ->label(__('booking.fields.customer_notes.label'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder(__('booking.fields.customer_notes.placeholder')),
                    ])->columns(3),

                // Extra Services Section
                // Extra Services Section
                Forms\Components\Section::make(__('booking.sections.extra_services'))
                    ->description(__('booking.sections.extra_services_description'))
                    ->schema([
                        Forms\Components\Repeater::make('extra_services')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('service_id')
                                    ->label(__('booking.fields.service_id.label'))
                                    ->options(fn(Get $get) => static::getExtraServicesOptions($get('../../hall_id')))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($state) {
                                            $service = ExtraService::find($state);
                                            if ($service) {
                                                // Store the full name array (not just English)
                                                $set('service_name', $service->name); // This is already an array
                                                $set('unit_price', $service->price);
                                                $set('quantity', $service->minimum_quantity ?? 1);
                                                $set('unit', $service->unit);

                                                // Calculate line total
                                                static::calculateServiceTotal($set, $get);

                                                // Calculate overall totals
                                                static::calculateAllTotals($set, fn($key) => $get("../../{$key}"));
                                            }
                                        }
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('booking.fields.quantity.label'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        // Calculate line total
                                        static::calculateServiceTotal($set, $get);

                                        // Calculate overall totals
                                        static::calculateAllTotals($set, fn($key) => $get("../../{$key}"));
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('booking.fields.unit_price.label'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->required()
                                    ->readOnly()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('total_price')
                                    ->label(__('booking.fields.total_price.label'))
                                    ->numeric()
                                    ->prefix('OMR')
                                    ->required()
                                    ->readOnly()
                                    ->columnSpan(1),

                                // Hidden fields for data storage
                                Forms\Components\Hidden::make('service_name'),
                                Forms\Components\Hidden::make('unit'),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel(__('booking.actions.add_service'))
                            ->reorderable(false)
                            ->collapsible()
                            ->visible(fn(Get $get): bool => $get('hall_id') !== null)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // This triggers when items are added/removed
                                static::calculateAllTotals($set, $get);
                            })
                            ->deleteAction(
                                fn($action) => $action->after(function (Set $set, Get $get) {
                                    // Recalculate when service is deleted
                                    static::calculateAllTotals($set, $get);
                                })
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(fn(Get $get): bool => !$get('hall_id')),
                // Pricing Section
                Forms\Components\Section::make(__('booking.sections.pricing_breakdown'))
                    ->schema([
                        Forms\Components\TextInput::make('hall_price')
                            ->label(__('booking.fields.hall_price.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->helperText(fn(Get $get) => static::getPriceHelperText(
                                $get('hall_id'),
                                $get('booking_date'),
                                $get('time_slot')
                            ))
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('services_price')
                            ->label(__('booking.fields.services_price.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->readOnly()

                            ->live()
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('booking.fields.subtotal.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('commission_amount')
                            ->label(__('booking.fields.commission_amount.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(Set $set, Get $get) => static::calculateAllTotals($set, $get)),

                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('booking.fields.total_amount.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-xl font-bold text-green-600']),

                        Forms\Components\TextInput::make('owner_payout')
                            ->label(__('booking.fields.owner_payout.label'))
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-lg']),
                    ])->columns(3)
                    ->collapsible(),

                // ✅ NEW: Advance Payment Details Section
                Forms\Components\Section::make(__('booking.sections.advance_payment_details'))
                    ->description(__('booking.sections.advance_payment_details_description'))
                    ->schema([
                        Forms\Components\Select::make('payment_type')
                            ->label(__('advance_payment.payment_type'))
                            ->options([
                                'full' => __('advance_payment.payment_type_full'),
                                'advance' => __('advance_payment.payment_type_advance'),
                            ])
                            ->default('full')
                            ->disabled()
                            ->dehydrated()
                            ->helperText(__('booking.sections.payment_type_helper')),

                        Forms\Components\TextInput::make('advance_amount')
                            ->label(__('advance_payment.advance_amount'))
                            ->numeric()
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn(Get $get) => $get('payment_type') === 'advance')
                            ->helperText('Amount paid upfront'),

                        Forms\Components\TextInput::make('balance_due')
                            ->label(__('advance_payment.balance_due'))
                            ->numeric()
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn(Get $get) => $get('payment_type') === 'advance')
                            ->helperText('Amount to be paid before event'),

                        Forms\Components\DateTimePicker::make('balance_paid_at')
                            ->label(__('advance_payment.balance_paid_at'))
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn(Get $get) => $get('payment_type') === 'advance')
                            ->helperText('When balance was paid'),

                        Forms\Components\Placeholder::make('advance_payment_note')
                            ->label('')
                            ->content(fn(Get $get) => $get('payment_type') === 'advance'
                                ? '⚠️ Advance payment booking. Customer paid ' . number_format((float)($get('advance_amount') ?? 0), 3) . ' OMR upfront. Balance of ' . number_format((float)($get('balance_due') ?? 0), 3) . ' OMR must be paid before the event.'
                                : '✅ Full payment booking. Customer pays the entire amount.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn(Get $get) => $get('payment_type') !== 'advance'),

                // Event Details Section
                Forms\Components\Section::make(__('booking.infolist.event_details'))
                    ->schema([
                        Forms\Components\Select::make('event_type')
                            ->label(__('booking.fields.event_type.label'))
                            ->options([
                                'wedding' => 'Wedding',
                                'birthday' => 'Birthday Party',
                                'corporate' => 'Corporate Event',
                                'conference' => 'Conference',
                                'graduation' => 'Graduation',
                                'engagement' => 'Engagement',
                                'other' => 'Other',
                            ])
                            ->searchable(),

                        Forms\Components\Textarea::make('event_details')
                            ->label(__('booking.infolist.event_details'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder(__('booking.infolist.event_details_placeholder')),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Status Section (Only visible when editing)
                Forms\Components\Section::make(__('booking.fields.status.label'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('booking.fields.status.label'))
                            ->options([
                                'pending' => __('booking.statuses.pending'),
                                'confirmed' => __('booking.statuses.confirmed'),
                                'completed' => __('booking.statuses.completed'),
                                'cancelled' => __('booking.statuses.cancelled'),
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label(__('booking.fields.payment_status.label'))
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'partially_paid' => 'Partially Paid',
                                'refunded' => 'Refunded',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2)
                    ->visible(fn($context) => $context === 'edit')
                    ->collapsible(),
            ]);
    }

    /**
     * Get available time slots from HallAvailabilities and exclude booked slots
     */
    // protected static function getAvailableTimeSlots($hallId, $bookingDate): array
    // {
    //     if (!$hallId || !$bookingDate) {
    //         return [];
    //     }

    //     // Get slots marked as available in HallAvailabilities
    //     $availableSlots = HallAvailability::where('hall_id', $hallId)
    //         ->where('date', $bookingDate)
    //         ->where('is_available', true)
    //         ->get()
    //         ->pluck('time_slot')
    //         ->toArray();

    //     // Get already booked slots (confirmed or pending bookings)
    //     $bookedSlots = Booking::where('hall_id', $hallId)
    //         ->where('booking_date', $bookingDate)
    //         ->whereIn('status', ['pending', 'confirmed'])
    //         ->pluck('time_slot')
    //         ->toArray();

    //     // Remove booked slots from available slots
    //     $actuallyAvailableSlots = array_diff($availableSlots, $bookedSlots);

    //     // Map to user-friendly labels
    //     $slotLabels = [
    //         'morning' => 'Morning (8 AM - 12 PM)',
    //         'afternoon' => 'Afternoon (12 PM - 5 PM)',
    //         'evening' => 'Evening (5 PM - 11 PM)',
    //         'full_day' => 'Full Day (8 AM - 11 PM)',
    //     ];

    //     $options = [];
    //     foreach ($actuallyAvailableSlots as $slot) {
    //         $options[$slot] = $slotLabels[$slot] ?? ucfirst(str_replace('_', ' ', $slot));
    //     }

    //     return $options;
    // }

    /**
     * Get available time slots from HallAvailabilities and exclude booked slots
     *
     * @param int|string|null $hallId       The hall ID to check availability for
     * @param string|null     $bookingDate  The date to check availability for
     * @param int|null        $excludeBookingId Optional booking ID to exclude from "booked" filter (for editing)
     * @return array<string, string> Array of available slot values => labels
     */
    protected static function getAvailableTimeSlots($hallId, $bookingDate, ?int $excludeBookingId = null): array
    {
        // ✅ Early return if required parameters are missing
        if (!$hallId || !$bookingDate) {
            return [];
        }

        // Get slots marked as available in HallAvailabilities
        $availableSlots = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('is_available', true)
            ->get()
            ->pluck('time_slot')
            ->toArray();

        // Get already booked slots (confirmed or pending bookings)
        // ✅ FIX: Exclude current booking when editing so its slot remains selectable
        $bookedSlots = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeBookingId, function ($query, $excludeBookingId) {
                // Exclude current booking from the "booked" list when editing
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->pluck('time_slot')
            ->toArray();

        // Remove booked slots from available slots
        $actuallyAvailableSlots = array_diff($availableSlots, $bookedSlots);

        // Map to user-friendly labels
        $slotLabels = [
            'morning' => __('booking.time_slots.morning', [], 'en') ?: 'Morning (8 AM - 12 PM)',
            'afternoon' => __('booking.time_slots.afternoon', [], 'en') ?: 'Afternoon (12 PM - 5 PM)',
            'evening' => __('booking.time_slots.evening', [], 'en') ?: 'Evening (5 PM - 11 PM)',
            'full_day' => __('booking.time_slots.full_day', [], 'en') ?: 'Full Day (8 AM - 11 PM)',
        ];

        $options = [];
        foreach ($actuallyAvailableSlots as $slot) {
            $options[$slot] = $slotLabels[$slot] ?? ucfirst(str_replace('_', ' ', $slot));
        }

        return $options;
    }

    /**
     * Get helper text for time slot field
     */
    protected static function getTimeSlotHelperText($hallId, $bookingDate): string
    {
        if (!$hallId || !$bookingDate) {
            return __('booking.fields.hall_id.helper');
        }

        // Get slots marked as available
        $availableCount = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('is_available', true)
            ->count();

        // Get already booked slots
        $bookedCount = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $actuallyAvailable = $availableCount - $bookedCount;

        if ($actuallyAvailable === 0) {
            return __('booking.fields.time_slot.helper_all_booked');
            //return '⚠️ All slots are booked for this date';
        }

        //return "✓ {$actuallyAvailable} slot(s) available";
        //return '✓ Slots available';
        return __('booking.fields.time_slot.helper_available', ['count' => $actuallyAvailable]);
    }

    /**
     * Get capacity helper text
     */
    protected static function getCapacityHelperText($hallId): string
    {
        if (!$hallId) {
            return __('booking.fields.hall_id.helper');
        }

        $hall = Hall::find($hallId);
        if (!$hall) {
            return '';
        }

        //return "Capacity: {$hall->capacity_min} - {$hall->capacity_max} guests";
        return __('booking.fields.number_of_guests.helper', [
            'min' => $hall->capacity_min,
            'max' => $hall->capacity_max,
        ]);
    }

    /**
     * Get extra services options for selected hall
     */
    protected static function getExtraServicesOptions($hallId): Collection
    {
        if (!$hallId) {
            return collect();
        }

        return ExtraService::where('hall_id', $hallId)
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->mapWithKeys(function ($service) {
                $name = is_array($service->name)
                    ? ($service->name['en'] ?? $service->name['ar'] ?? 'Unnamed Service')
                    : $service->name;

                $unit = match ($service->unit) {
                    'per_person' => 'per person',
                    'per_item' => 'per item',
                    'per_hour' => 'per hour',
                    'fixed' => 'fixed',
                    default => $service->unit,
                };

                $price = number_format($service->price, 3);

                return [$service->id => "{$name} ({$price} OMR / {$unit})"];
            });
    }

    /**
     * Calculate service line total
     */
    protected static function calculateServiceTotal(Set $set, Get $get): void
    {
        $quantity = (float) ($get('quantity') ?? 1);
        $unitPrice = (float) ($get('unit_price') ?? 0);

        $total = $quantity * $unitPrice;
        $set('total_price', $total);
    }

    protected static function updateHallPrice(Set $set, $hallId, $bookingDate, $timeSlot): void
    {
        if (!$hallId || !$bookingDate || !$timeSlot) {
            return;
        }

        // First, check if there's a custom price in HallAvailability
        $availability = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($availability && $availability->custom_price !== null) {
            // Use custom price from HallAvailability
            $hallPrice = (float) $availability->custom_price;
        } else {
            // Use default Hall price for this time slot
            $hall = Hall::find($hallId);
            if ($hall) {
                $hallPrice = $hall->getPriceForSlot($timeSlot);
            } else {
                $hallPrice = 0;
            }
        }

        $set('hall_price', $hallPrice);
        $set('services_price', 0);
        $set('subtotal', $hallPrice);
        $set('commission_amount', 0);
        $set('total_amount', $hallPrice);
        $set('owner_payout', $hallPrice);
    }

    /**
     * Calculate all totals
     */
    protected static function calculateAllTotals(Set $set, $get): void
    {
        // Handle both regular $get and nested repeater $get
        $getValue = function ($key) use ($get) {
            if (is_callable($get)) {
                try {
                    return $get($key);
                } catch (\Exception $e) {
                    return null;
                }
            }
            return null;
        };

        $hallPrice = (float) ($getValue('hall_price') ?? 0);
        $extraServices = $getValue('extra_services') ?? [];
        $commissionAmount = (float) ($getValue('commission_amount') ?? 0);

        // Calculate services total from repeater
        $servicesPrice = 0;
        if (is_array($extraServices)) {
            foreach ($extraServices as $service) {
                if (is_array($service)) {
                    $servicesPrice += (float) ($service['total_price'] ?? 0);
                }
            }
        }

        $subtotal = $hallPrice + $servicesPrice;
        $totalAmount = $subtotal + $commissionAmount;
        $ownerPayout = $subtotal - $commissionAmount;

        $set('services_price', $servicesPrice);
        $set('subtotal', $subtotal);
        $set('total_amount', $totalAmount);
        $set('owner_payout', max(0, $ownerPayout));
    }

    /**
     * Get helper text for price field
     */
    protected static function getPriceHelperText($hallId, $bookingDate, $timeSlot): string
    {
        if (!$hallId || !$bookingDate || !$timeSlot) {
            return __('booking.fields.hall_id.select_hall_first');
        }

        $availability = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($availability && $availability->custom_price !== null) {
            return '✓ Custom price for this date/slot';
        }

        return 'Default hall price for ' . ucfirst(str_replace('_', ' ', $timeSlot));
    }

    /**
     * Get helper text for date field
     */
    protected static function getDateHelperText($hallId, $bookingDate): string
    {
        if (!$hallId) {
            return __('booking.fields.hall_id.helper');
        }

        if (!$bookingDate) {
            return 'Select date to see available time slots';
        }

        $availableCount = HallAvailability::where('hall_id', $hallId)
            ->where('date', $bookingDate)
            ->where('is_available', true)
            ->count();

        $bookedCount = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $actuallyAvailable = $availableCount - $bookedCount;

        if ($actuallyAvailable === 0) {
            return '❌ Fully booked';
        }

        return __('booking.fields.time_slot.helper_available', ['count' => $actuallyAvailable]);
    }

    // ... Rest of your table, infolist, and pages methods remain the same ...

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(static::columnLabel('booking_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(static::columnLabel('hall'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label(static::columnLabel('customer_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->label(static::columnLabel('booking_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_slot')
                    ->label(static::columnLabel('time_slot'))
                    ->badge()
                    ->colors([
                        'primary' => 'morning',
                        'success' => 'afternoon',
                        'warning' => 'evening',
                        'info' => 'full_day',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'morning' => __('booking.time_slots.morning'),
                            'afternoon' => __('booking.time_slots.afternoon'),
                            'evening' => __('booking.time_slots.evening'),
                            'full_day' => __('booking.time_slots.full_day'),
                            default => ucfirst(str_replace('_', ' ', $state)),
                        }
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(static::columnLabel('total_amount'))

                    ->money('OMR')
                    ->sortable()
                    ->description(
                        fn($record) => $record->isAdvancePayment()
                            ? '⚡ Advance: ' . number_format($record->advance_amount, 3) . ' OMR'
                            : null
                    )
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->label(static::columnLabel('status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable()
                    ->formatStateUsing(
                        fn(string $state): string =>
                        __('booking.statuses.' . $state)
                    ),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(static::columnLabel('payment_status'))
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'info' => 'partially_paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ])
                    ->sortable()
                    ->formatStateUsing(
                        fn(string $state): string =>
                        __('booking.statuses.' . $state)
                    ),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label(__('advance_payment.payment_type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'full' => 'success',
                        'advance' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn(string $state): string =>
                        __('advance_payment.payment_type_' . $state)
                    ),

                Tables\Columns\TextColumn::make('advance_amount')
                    ->label(__('advance_payment.advance_paid'))
                    ->money('OMR', 3)
                    ->visible(fn($record) => $record && $record->isAdvancePayment()),

                Tables\Columns\TextColumn::make('balance_due')
                    ->label(__('advance_payment.balance_due'))
                    ->money('OMR', 3)
                    ->color(fn($record) => $record && $record->isBalancePending() ? 'danger' : 'success')
                    ->visible(fn($record) => $record && $record->isAdvancePayment()),

                Tables\Columns\IconColumn::make('balance_paid')
                    ->label(__('advance_payment.balance_payment_status'))
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->balance_paid_at !== null)
                    ->visible(fn($record) => $record && $record->isAdvancePayment()),

                // Add guest badge after customer name
                GuestBookingComponents::guestBadgeColumn(),

                // Optionally add token column for admins
                GuestBookingComponents::guestTokenColumn(),
            ])
            ->filters([
                //
                // Add booking type filter
                GuestBookingComponents::bookingTypeFilter(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    // In your BookingResource table or view page
                    Tables\Actions\Action::make('mark_balance_paid')
                        ->label(__('advance_payment.mark_balance_as_paid'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Booking $record) => $record->isBalancePending())
                        ->form([
                            Forms\Components\Select::make('payment_method')
                                ->label(__('advance_payment.balance_payment_method'))
                                ->options([
                                    'bank_transfer' => __('advance_payment.bank_transfer'),
                                    'cash' => __('advance_payment.cash'),
                                    'card' => __('advance_payment.card'),
                                ])
                                ->required(),

                            Forms\Components\TextInput::make('reference')
                                ->label(__('advance_payment.balance_payment_reference'))
                                ->placeholder('Transaction ID or Receipt Number')
                                ->maxLength(255),

                            Forms\Components\DateTimePicker::make('paid_at')
                                ->label(__('Payment Date'))
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function (Booking $record, array $data) {
                            $record->markBalanceAsPaid(
                                method: $data['payment_method'],
                                reference: $data['reference'] ?? null
                            );

                            // Update payment timestamp if provided
                            if (isset($data['paid_at'])) {
                                $record->balance_paid_at = $data['paid_at'];
                                $record->save();
                            }

                            Notification::make()
                                ->success()
                                ->title(__('advance_payment.balance_marked_as_paid'))
                                ->send();
                        }),

                    // ✅ PDF Invoice Download Actions
                    Tables\Actions\Action::make('download_advance_invoice')
                        ->label(__('Advance Invoice PDF'))
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->visible(fn(Booking $record): bool => $record->isAdvancePayment())
                        ->action(function (Booking $record) {
                            try {
                                $invoiceService = app(InvoiceService::class);
                                $pdf = $invoiceService->generateAdvanceInvoice($record);

                                // Return the response directly
                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    "advance-invoice-{$record->booking_number}.pdf",
                                    [
                                        'Content-Type' => 'application/pdf',
                                        'Content-Disposition' => 'attachment; filename="advance-invoice-' . $record->booking_number . '.pdf"',
                                    ]
                                );
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('PDF Generation Failed')
                                    ->body($e->getMessage())
                                    ->send();
                                return null;
                            }
                        })
                        ->requiresConfirmation(false),

                    Tables\Actions\Action::make('download_balance_invoice')
                        ->label(__('Balance Due PDF'))
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->visible(
                            fn(Booking $record): bool =>
                            $record->isAdvancePayment() && $record->isBalancePending()
                        )
                        ->action(function (Booking $record) {
                            try {
                                $invoiceService = app(InvoiceService::class);
                                $pdf = $invoiceService->generateBalanceInvoice($record);

                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    "balance-invoice-{$record->booking_number}.pdf",
                                    [
                                        'Content-Type' => 'application/pdf',
                                        'Content-Disposition' => 'attachment; filename="balance-invoice-' . $record->booking_number . '.pdf"',
                                    ]
                                );
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('PDF Generation Failed')
                                    ->body($e->getMessage())
                                    ->send();
                                return null;
                            }
                        })
                        ->requiresConfirmation(false),

                    Tables\Actions\Action::make('download_receipt')
                        ->label(__('Full Receipt PDF'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Booking $record): bool => $record->isFullyPaid())
                        ->action(function (Booking $record) {
                            try {
                                $invoiceService = app(InvoiceService::class);
                                $pdf = $invoiceService->generateFullReceipt($record);

                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    "receipt-{$record->booking_number}.pdf",
                                    [
                                        'Content-Type' => 'application/pdf',
                                        'Content-Disposition' => 'attachment; filename="receipt-' . $record->booking_number . '.pdf"',
                                    ]
                                );
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('PDF Generation Failed')
                                    ->body($e->getMessage())
                                    ->send();
                                return null;
                            }
                        })
                        ->requiresConfirmation(false),
                    // =========================================================
                    // 📄 INVOICE ACTIONS FOR CONFIRMED BOOKINGS
                    // =========================================================

                    /**
                     * Download Invoice Action
                     *
                     * Downloads the booking invoice PDF for confirmed/completed bookings.
                     * Uses the InvoiceService to generate a professional invoice.
                     */
                    Tables\Actions\Action::make('download_invoice')
                        ->label(__('booking.actions.download_invoice'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->visible(
                            fn(Booking $record): bool =>
                            in_array($record->status, ['confirmed', 'completed']) &&
                                $record->payment_status === 'paid'
                        )
                        ->action(function (Booking $record) {
                            try {
                                $invoiceService = app(InvoiceService::class);
                                $pdf = $invoiceService->generateInvoice($record);

                                return response()->streamDownload(
                                    fn() => print($pdf->output()),
                                    "invoice-{$record->booking_number}.pdf",
                                    ['Content-Type' => 'application/pdf']
                                );
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('booking.notifications.pdf_generation_failed'))
                                    ->body($e->getMessage())
                                    ->send();
                                return null;
                            }
                        }),

                    /**
                     * Print Invoice Action
                     *
                     * Opens invoice in new tab for printing.
                     * Uses a dedicated print route with print-optimized CSS.
                     */
                    Tables\Actions\Action::make('print_invoice')
                        ->label(__('booking.actions.print_invoice'))
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->visible(
                            fn(Booking $record): bool =>
                            in_array($record->status, ['confirmed', 'completed']) &&
                                $record->payment_status === 'paid'
                        )
                        ->url(
                            fn(Booking $record): string =>
                            route('bookings.invoice.print', ['booking' => $record->id])
                        )
                        ->openUrlInNewTab(),

                    /**
                     * Send Invoice via Email Action
                     *
                     * Sends invoice PDF to customer email with customizable message.
                     * Queues the email job for better performance.
                     */
                    Tables\Actions\Action::make('send_invoice_email')
                        ->label(__('booking.actions.send_invoice_email'))
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->visible(
                            fn(Booking $record): bool =>
                            in_array($record->status, ['confirmed', 'completed']) &&
                                $record->payment_status === 'paid' &&
                                !empty($record->customer_email)
                        )
                        ->form([
                            Forms\Components\TextInput::make('email')
                                ->label(__('booking.form.recipient_email'))
                                ->email()
                                ->required()
                                ->default(fn(Booking $record): string => $record->customer_email ?? ''),

                            Forms\Components\TextInput::make('subject')
                                ->label(__('booking.form.email_subject'))
                                ->required()
                                ->default(
                                    fn(Booking $record): string =>
                                    __('booking.email.invoice_subject', ['number' => $record->booking_number])
                                ),

                            Forms\Components\Textarea::make('message')
                                ->label(__('booking.form.email_message'))
                                ->rows(4)
                                ->default(__('booking.email.invoice_default_message'))
                                ->helperText(__('booking.form.email_message_helper')),

                            Forms\Components\Toggle::make('attach_pdf')
                                ->label(__('booking.form.attach_pdf'))
                                ->default(true),
                        ])
                        ->action(function (Booking $record, array $data) {
                            try {
                                // Dispatch email job (recommended for production)
                                \App\Jobs\SendBookingInvoiceEmail::dispatch(
                                    $record,
                                    $data['email'],
                                    $data['subject'],
                                    $data['message'],
                                    $data['attach_pdf']
                                );

                                Notification::make()
                                    ->success()
                                    ->title(__('booking.notifications.invoice_email_queued'))
                                    ->body(__('booking.notifications.invoice_email_queued_body', [
                                        'email' => $data['email']
                                    ]))
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('booking.notifications.email_failed'))
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        })
                        ->modalHeading(__('booking.modals.send_invoice_email'))
                        ->modalSubmitActionLabel(__('booking.actions.send_email')),

                    // =========================================================
                    // 🔄 STATUS TRANSITION ACTIONS
                    // =========================================================

                    /**
                     * Confirm Booking Action
                     *
                     * Transitions pending booking to confirmed status.
                     */
                    Tables\Actions\Action::make('confirm_booking')
                        ->label(__('booking.actions.confirm'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Booking $record): bool => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading(__('booking.modals.confirm_booking'))
                        ->modalDescription(__('booking.modals.confirm_booking_description'))
                        ->action(function (Booking $record) {
                            $record->confirm();

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.booking_confirmed'))
                                ->send();
                        }),

                    /**
                     * Cancel Booking Action
                     *
                     * Cancels booking with reason and optional refund calculation.
                     */
                    Tables\Actions\Action::make('cancel_booking')
                        ->label(__('booking.actions.cancel'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(
                            fn(Booking $record): bool =>
                            in_array($record->status, ['pending', 'confirmed'])
                        )
                        ->requiresConfirmation()
                        ->modalHeading(__('booking.modals.cancel_booking'))
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label(__('booking.form.cancellation_reason'))
                                ->required()
                                ->rows(3)
                                ->placeholder(__('booking.form.cancellation_reason_placeholder')),
                        ])
                        ->action(function (Booking $record, array $data) {
                            $record->cancel($data['reason']);

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.booking_cancelled'))
                                ->send();
                        }),

                    /**
                     * Complete Booking Action
                     *
                     * Marks confirmed booking as completed (post-event).
                     */
                    Tables\Actions\Action::make('complete_booking')
                        ->label(__('booking.actions.complete'))
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->visible(
                            fn(Booking $record): bool =>
                            $record->status === 'confirmed' &&
                                $record->booking_date->isPast()
                        )
                        ->requiresConfirmation()
                        ->modalHeading(__('booking.modals.complete_booking'))
                        ->modalDescription(__('booking.modals.complete_booking_description'))
                        ->action(function (Booking $record) {
                            $record->complete();

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.booking_completed'))
                                ->send();
                        }),

                    // =========================================================
                    // 📱 COMMUNICATION ACTIONS
                    // =========================================================

                    /**
                     * Send Reminder SMS/Email Action
                     *
                     * Sends booking reminder to customer.
                     */
                    Tables\Actions\Action::make('send_reminder')
                        ->label(__('booking.actions.send_reminder'))
                        ->icon('heroicon-o-bell-alert')
                        ->color('warning')
                        ->visible(
                            fn(Booking $record): bool =>
                            $record->status === 'confirmed' &&
                                $record->booking_date->isFuture() &&
                                $record->booking_date->diffInDays(now()) <= 7
                        )
                        ->form([
                            Forms\Components\CheckboxList::make('channels')
                                ->label(__('booking.form.notification_channels'))
                                ->options([
                                    'email' => __('booking.form.channel_email'),
                                    'sms' => __('booking.form.channel_sms'),
                                ])
                                ->default(['email'])
                                ->required(),

                            Forms\Components\Textarea::make('custom_message')
                                ->label(__('booking.form.custom_message'))
                                ->rows(3)
                                ->placeholder(__('booking.form.custom_message_placeholder')),
                        ])
                        ->action(function (Booking $record, array $data) {
                            // Dispatch reminder notification job
                            \App\Jobs\SendBookingReminder::dispatch(
                                $record,
                                $data['channels'],
                                $data['custom_message'] ?? null
                            );

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.reminder_sent'))
                                ->send();
                        })
                        ->modalHeading(__('booking.modals.send_reminder')),

                    /**
                     * Contact Customer Action
                     *
                     * Quick links to call or WhatsApp customer.
                     */
                    Tables\Actions\Action::make('contact_customer')
                        ->label(__('booking.actions.contact_customer'))
                        ->icon('heroicon-o-phone')
                        ->color('gray')
                        ->visible(fn(Booking $record): bool => !empty($record->customer_phone))
                        ->url(
                            fn(Booking $record): string =>
                            "https://wa.me/{$record->customer_phone}?text=" .
                                urlencode(__('booking.whatsapp.greeting', [
                                    'name' => $record->customer_name,
                                    'booking' => $record->booking_number
                                ]))
                        )
                        ->openUrlInNewTab(),

                    // =========================================================
                    // 📋 ADMINISTRATIVE ACTIONS
                    // =========================================================

                    /**
                     * Add Admin Note Action
                     *
                     * Allows adding internal notes to the booking.
                     */
                    Tables\Actions\Action::make('add_admin_note')
                        ->label(__('booking.actions.add_note'))
                        ->icon('heroicon-o-pencil-square')
                        ->color('gray')
                        ->form([
                            Forms\Components\Textarea::make('admin_notes')
                                ->label(__('booking.form.admin_notes'))
                                ->rows(4)
                                ->default(fn(Booking $record): ?string => $record->admin_notes)
                                ->placeholder(__('booking.form.admin_notes_placeholder')),
                        ])
                        ->action(function (Booking $record, array $data) {
                            $record->update(['admin_notes' => $data['admin_notes']]);

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.note_saved'))
                                ->send();
                        })
                        ->modalHeading(__('booking.modals.admin_notes')),

                    /**
                     * Duplicate Booking Action
                     *
                     * Creates a new booking with same details (for repeat customers).
                     */
                    Tables\Actions\Action::make('duplicate_booking')
                        ->label(__('booking.actions.duplicate'))
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->visible(
                            fn(Booking $record): bool =>
                            in_array($record->status, ['completed', 'confirmed'])
                        )
                        ->requiresConfirmation()
                        ->modalHeading(__('booking.modals.duplicate_booking'))
                        ->modalDescription(__('booking.modals.duplicate_booking_description'))
                        ->form([
                            Forms\Components\DatePicker::make('new_booking_date')
                                ->label(__('booking.form.new_booking_date'))
                                ->required()
                                ->minDate(now()->addDay())
                                ->default(now()->addMonth()),
                        ])
                        ->action(function (Booking $record, array $data) {
                            $newBooking = $record->replicate([
                                'booking_number',
                                'status',
                                'payment_status',
                                'confirmed_at',
                                'completed_at',
                                'cancelled_at',
                                'cancellation_reason',
                                'invoice_path',
                                'balance_paid_at',
                                'balance_payment_method',
                                'balance_payment_reference',
                            ]);

                            $newBooking->booking_date = $data['new_booking_date'];
                            $newBooking->status = 'pending';
                            $newBooking->payment_status = 'pending';
                            $newBooking->save();

                            // Copy extra services
                            foreach ($record->extraServices as $service) {
                                $newBooking->extraServices()->attach($service->id, [
                                    'quantity' => $service->pivot->quantity,
                                    'unit_price' => $service->pivot->unit_price,
                                    'total_price' => $service->pivot->total_price,
                                ]);
                            }

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.booking_duplicated'))
                                ->body(__('booking.notifications.booking_duplicated_body', [
                                    'number' => $newBooking->booking_number
                                ]))
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('view')
                                        ->label(__('booking.actions.view_new_booking'))
                                        ->url(BookingResource::getUrl('view', ['record' => $newBooking]))
                                ])
                                ->send();
                        }),

                    /**
                     * Request Review Action
                     *
                     * Sends review request to customer after completed booking.
                     */
                    Tables\Actions\Action::make('request_review')
                        ->label(__('booking.actions.request_review'))
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->visible(
                            fn(Booking $record): bool =>
                            $record->status === 'completed' &&
                                !$record->review()->exists()
                        )
                        ->requiresConfirmation()
                        ->modalHeading(__('booking.modals.request_review'))
                        ->modalDescription(__('booking.modals.request_review_description'))
                        ->action(function (Booking $record) {
                            \App\Jobs\SendReviewRequest::dispatch($record);

                            Notification::make()
                                ->success()
                                ->title(__('booking.notifications.review_request_sent'))
                                ->send();
                        }),
                ActivityLogTimelineTableAction::make('Activities')
                    ->timelineIcons([
                        'created' => 'heroicon-m-check-badge',
                        'updated' => 'heroicon-m-pencil-square',
                    ])
                    ->timelineIconColors([
                        'created' => 'info',
                        'updated' => 'warning',
                    ]),

                        ])
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Configure the infolist for viewing booking details
     *
     * Displays comprehensive booking information including advance payment details
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Hall & Customer Information
                Infolists\Components\Section::make('Booking Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('booking_number')
                            ->label('Booking Number')
                            ->badge()
                            ->color('primary')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('hall.name')
                            ->label('Hall'),

                        Infolists\Components\TextEntry::make('customer_name')
                            ->label('Customer Name'),

                        Infolists\Components\TextEntry::make('customer_phone')
                            ->label('Customer Phone'),

                        Infolists\Components\TextEntry::make('booking_date')
                            ->label('Date')
                            ->date(),

                        Infolists\Components\TextEntry::make('time_slot')
                            ->label('Time Slot')
                            ->badge(),

                        Infolists\Components\TextEntry::make('status')
                            ->badge(),

                        Infolists\Components\TextEntry::make('payment_status')
                            ->badge(),
                    ])
                    ->columns(2),

                // Pricing Information
                Infolists\Components\Section::make('Pricing Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('hall_price')
                            ->label('Hall Price')
                            ->money('OMR', 3),

                        Infolists\Components\TextEntry::make('services_price')
                            ->label('Services Total')
                            ->money('OMR', 3),

                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('OMR', 3),

                        Infolists\Components\TextEntry::make('commission_amount')
                            ->label('Platform Fee')
                            ->money('OMR', 3),

                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('OMR', 3)
                            ->weight('bold')
                            ->size('lg')
                            ->color('success'),
                    ])
                    ->columns(3),

                // ✅ Advance Payment Section
                Infolists\Components\Section::make('Advance Payment Details')
                    ->description(fn($record) => $record->isAdvancePayment()
                        ? 'This booking requires advance payment. Customer must pay balance before the event.'
                        : 'This is a full payment booking.')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_type')
                            ->label(__('advance_payment.payment_type'))
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'full' => 'success',
                                'advance' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(
                                fn(string $state): string =>
                                __('advance_payment.payment_type_' . $state)
                            )
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('advance_amount')
                            ->label(__('advance_payment.advance_paid'))
                            ->money('OMR', 3)
                            ->visible(fn($record) => $record->isAdvancePayment())
                            ->color('warning')
                            ->weight('bold')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('balance_due')
                            ->label(__('advance_payment.balance_due'))
                            ->money('OMR', 3)
                            ->visible(fn($record) => $record->isAdvancePayment())
                            ->color(fn($record) => $record->isBalancePending() ? 'danger' : 'success')
                            ->weight('bold')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('balance_paid_at')
                            ->label(__('advance_payment.balance_paid_at'))
                            ->dateTime()
                            ->visible(fn($record) => $record->isAdvancePayment() && $record->balance_paid_at)
                            ->color('success'),

                        Infolists\Components\TextEntry::make('balance_payment_status')
                            ->label(__('advance_payment.balance_payment_status'))
                            ->badge()
                            ->visible(fn($record) => $record->isAdvancePayment())
                            ->getStateUsing(fn($record) => $record->balance_paid_at ? 'Paid' : 'Pending')
                            ->color(fn($record) => $record->balance_paid_at ? 'success' : 'danger'),
                    ])
                    ->columns(3)
                    ->visible(fn($record) => $record->payment_type !== null),

                // Extra Services
                Infolists\Components\Section::make('Extra Services')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('booking.fields.service_id.label')),

                                Infolists\Components\TextEntry::make('pivot.quantity')
                                    ->label(__('booking.fields.quantity.label')),

                                Infolists\Components\TextEntry::make('pivot.unit_price')
                                    ->label(__('booking.fields.unit_price.label'))
                                    ->money('OMR', 3),

                                Infolists\Components\TextEntry::make('pivot.total_price')
                                    ->label(__('booking.fields.total_price.label'))
                                    ->money('OMR', 3)
                                    ->weight('bold'),
                            ])
                            ->columns(4),
                    ])
                    ->visible(fn($record) => $record->extraServices->count() > 0)
                    ->collapsible(),

                // Event Details
                Infolists\Components\Section::make(__('booking.infolist.event_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('event_type')
                            ->label('Event Type')
                            ->badge(),

                        Infolists\Components\TextEntry::make('event_details')
                            ->label('Event Details')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
}
