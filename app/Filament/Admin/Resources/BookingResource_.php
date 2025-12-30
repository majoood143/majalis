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



class BookingResource_ extends Resource
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
                Forms\Components\Section::make('Hall Selection')
                    ->description('Filter and select the hall for booking')
                    ->schema([
                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(fn () => Region::where('is_active', true)->ordered()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset dependent fields when region changes
                                $set('city_id', null);
                                $set('hall_id', null);
                                $set('booking_date', null);
                                $set('time_slot', null);
                                $set('hall_price', 0);
                            })
                            ->helperText('Select a region to filter cities'),

                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->options(fn (Get $get): Collection => City::query()
                                ->when($get('region_id'), fn ($query, $regionId) =>
                                    $query->where('region_id', $regionId)
                                )
                                ->where('is_active', true)
                                ->ordered()
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset dependent fields when city changes
                                $set('hall_id', null);
                                $set('booking_date', null);
                                $set('time_slot', null);
                                $set('hall_price', 0);
                            })
                            ->disabled(fn (Get $get): bool => !$get('region_id'))
                            ->helperText('Select a city to filter halls'),

                        Forms\Components\Select::make('hall_id')
                            ->label('Hall')
                            ->options(fn (Get $get): Collection => Hall::query()
                                ->when($get('city_id'), fn ($query, $cityId) =>
                                    $query->where('city_id', $cityId)
                                )
                                ->when(!$get('city_id') && $get('region_id'), fn ($query) =>
                                    $query->whereHas('city', fn ($q) => $q->where('region_id', $get('region_id')))
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
                            ->disabled(fn (Get $get): bool => !$get('city_id') && !$get('region_id'))
                            ->helperText('Select a hall to proceed'),
                    ])->columns(3)
                    ->collapsible(),

                // Booking Details Section
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label('Booking Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($context) => $context === 'edit')
                            ->columnSpan(1),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                Forms\Components\DatePicker::make('booking_date')
                    ->label('Event Date')
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

                        Forms\Components\Select::make('time_slot')
                            ->label('Time Slot')
                            ->options(fn (Get $get): array => static::getAvailableTimeSlots(
                                $get('hall_id'),
                                $get('booking_date')
                            ))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('hall_id') && $get('booking_date')) {
                                    static::updateHallPrice($set, $get('hall_id'), $get('booking_date'), $state);
                                }
                            })
                            ->disabled(fn (Get $get): bool => !$get('booking_date'))
                            ->helperText(fn (Get $get) => static::getTimeSlotHelperText($get('hall_id'), $get('booking_date')))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('number_of_guests')
                            ->label('Number of Guests')
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
                            ->helperText(fn (Get $get) => static::getCapacityHelperText($get('hall_id')))
                            ->columnSpan(1),
                    ])->columns(2),

                // Customer Information Section
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('customer_notes')

                            ->label('Special Requests / Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Any special requirements or notes...'),
                    ])->columns(3),

            // Extra Services Section
            // Extra Services Section
            Forms\Components\Section::make('Extra Services')
                ->description('Select additional services for your booking')
                ->schema([
                    Forms\Components\Repeater::make('extra_services')
                        ->label('')
                        ->schema([
                            Forms\Components\Select::make('service_id')
                                ->label('Service')
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
                                ->label('Quantity')
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
                                ->label('Unit Price')
                                ->numeric()
                                ->prefix('OMR')
                                ->required()
                                ->readOnly()
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('total_price')
                                ->label('Total')
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
                        ->addActionLabel('Add Service')
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
                Forms\Components\Section::make('Pricing Summary')
                    ->schema([
                        Forms\Components\TextInput::make('hall_price')
                            ->label('Hall Price')
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->helperText(fn (Get $get) => static::getPriceHelperText(
                                $get('hall_id'),
                                $get('booking_date'),
                                $get('time_slot')
                            ))
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('services_price')
                            ->label('Services Total')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->readOnly()

                            ->live()
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-lg font-semibold']),

                        Forms\Components\TextInput::make('commission_amount')
                            ->label('Platform Fee')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::calculateAllTotals($set, $get)),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('OMR')
                            ->required()
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-xl font-bold text-green-600']),

                        Forms\Components\TextInput::make('owner_payout')
                            ->label('Owner Payout')
                            ->numeric()
                            ->prefix('OMR')
                            ->default(0)
                            ->readOnly()
                            ->extraAttributes(['class' => 'text-lg']),
                    ])->columns(3)
                    ->collapsible(),

                // ✅ NEW: Advance Payment Details Section
                Forms\Components\Section::make('Advance Payment Details')
                    ->description('Advance payment information (auto-calculated for advance payment bookings)')
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
                            ->helperText('Set by hall configuration'),

                        Forms\Components\TextInput::make('advance_amount')
                            ->label(__('advance_payment.advance_amount'))
                            ->numeric()
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (Get $get) => $get('payment_type') === 'advance')
                            ->helperText('Amount paid upfront'),

                        Forms\Components\TextInput::make('balance_due')
                            ->label(__('advance_payment.balance_due'))
                            ->numeric()
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (Get $get) => $get('payment_type') === 'advance')
                            ->helperText('Amount to be paid before event'),

                        Forms\Components\DateTimePicker::make('balance_paid_at')
                            ->label(__('advance_payment.balance_paid_at'))
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn (Get $get) => $get('payment_type') === 'advance')
                            ->helperText('When balance was paid'),

                        Forms\Components\Placeholder::make('advance_payment_note')
                            ->label('')
                            ->content(fn (Get $get) => $get('payment_type') === 'advance'
                                ? '⚠️ Advance payment booking. Customer paid ' . number_format((float)($get('advance_amount') ?? 0), 3) . ' OMR upfront. Balance of ' . number_format((float)($get('balance_due') ?? 0), 3) . ' OMR must be paid before the event.'
                                : '✅ Full payment booking. Customer pays the entire amount.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn (Get $get) => $get('payment_type') !== 'advance'),

                // Event Details Section
                Forms\Components\Section::make('Event Details')
                    ->schema([
                        Forms\Components\Select::make('event_type')
                            ->label('Event Type')
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
                            ->label('Event Details')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Describe your event...'),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),

                // Status Section (Only visible when editing)
                Forms\Components\Section::make('Booking Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Select::make('payment_status')
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
                    ->visible(fn ($context) => $context === 'edit')
                    ->collapsible(),
            ]);
    }

    /**
     * Get available time slots from HallAvailabilities and exclude booked slots
     */
    protected static function getAvailableTimeSlots($hallId, $bookingDate): array
    {
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
        $bookedSlots = Booking::where('hall_id', $hallId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('time_slot')
            ->toArray();

        // Remove booked slots from available slots
        $actuallyAvailableSlots = array_diff($availableSlots, $bookedSlots);

        // Map to user-friendly labels
        $slotLabels = [
            'morning' => 'Morning (8 AM - 12 PM)',
            'afternoon' => 'Afternoon (12 PM - 5 PM)',
            'evening' => 'Evening (5 PM - 11 PM)',
            'full_day' => 'Full Day (8 AM - 11 PM)',
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
            return 'Select hall and date first';
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
            return '⚠️ All slots are booked for this date';
        }

        return "✓ {$actuallyAvailable} slot(s) available";
    }

    /**
     * Get capacity helper text
     */
    protected static function getCapacityHelperText($hallId): string
    {
        if (!$hallId) {
            return 'Select a hall first';
        }

        $hall = Hall::find($hallId);
        if (!$hall) {
            return '';
        }

        return "Capacity: {$hall->capacity_min} - {$hall->capacity_max} guests";
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

                $unit = match($service->unit) {
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
            return 'Select hall, date, and time slot to see pricing';
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
            return 'Select a hall first';
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

        return "✓ {$actuallyAvailable} slot(s) available";
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_slot')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('OMR', 3)
                    ->sortable()
                    ->description(fn ($record) => $record->isAdvancePayment()
                        ? '⚡ Advance: ' . number_format($record->advance_amount, 3) . ' OMR'
                        : null
                    )
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge(),
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
            ])
            ->filters([
                //
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
                    ->visible(fn (Booking $record): bool => $record->isAdvancePayment())
                    ->action(function (Booking $record) {
                        $invoiceService = app(InvoiceService::class);
                        return $invoiceService->generateAdvanceInvoice($record);
                    })
                    ->requiresConfirmation(false),

                Tables\Actions\Action::make('download_balance_invoice')
                    ->label(__('Balance Due PDF'))
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool =>
                        $record->isAdvancePayment() && $record->isBalancePending()
                    )
                    ->action(function (Booking $record) {
                        $invoiceService = app(InvoiceService::class);
                        return $invoiceService->generateBalanceInvoice($record);
                    })
                    ->requiresConfirmation(false),

                Tables\Actions\Action::make('download_receipt')
                    ->label(__('Full Receipt PDF'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->isFullyPaid())
                    ->action(function (Booking $record) {
                        $invoiceService = app(InvoiceService::class);
                        return $invoiceService->generateFullReceipt($record);
                    })
                    ->requiresConfirmation(false),

                ])
            ])
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
                    ->description(fn ($record) => $record->isAdvancePayment()
                        ? 'This booking requires advance payment. Customer must pay balance before the event.'
                        : 'This is a full payment booking.')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_type')
                            ->label(__('advance_payment.payment_type'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'full' => 'success',
                                'advance' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string =>
                                __('advance_payment.payment_type_' . $state)
                            )
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('advance_amount')
                            ->label(__('advance_payment.advance_paid'))
                            ->money('OMR', 3)
                            ->visible(fn ($record) => $record->isAdvancePayment())
                            ->color('warning')
                            ->weight('bold')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('balance_due')
                            ->label(__('advance_payment.balance_due'))
                            ->money('OMR', 3)
                            ->visible(fn ($record) => $record->isAdvancePayment())
                            ->color(fn ($record) => $record->isBalancePending() ? 'danger' : 'success')
                            ->weight('bold')
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('balance_paid_at')
                            ->label(__('advance_payment.balance_paid_at'))
                            ->dateTime()
                            ->visible(fn ($record) => $record->isAdvancePayment() && $record->balance_paid_at)
                            ->color('success'),

                        Infolists\Components\TextEntry::make('balance_payment_status')
                            ->label(__('advance_payment.balance_payment_status'))
                            ->badge()
                            ->visible(fn ($record) => $record->isAdvancePayment())
                            ->getStateUsing(fn ($record) => $record->balance_paid_at ? 'Paid' : 'Pending')
                            ->color(fn ($record) => $record->balance_paid_at ? 'success' : 'danger'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->payment_type !== null),

                // Extra Services
                Infolists\Components\Section::make('Extra Services')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Service'),

                                Infolists\Components\TextEntry::make('pivot.quantity')
                                    ->label('Quantity'),

                                Infolists\Components\TextEntry::make('pivot.unit_price')
                                    ->label('Unit Price')
                                    ->money('OMR', 3),

                                Infolists\Components\TextEntry::make('pivot.total_price')
                                    ->label('Total')
                                    ->money('OMR', 3)
                                    ->weight('bold'),
                            ])
                            ->columns(4),
                    ])
                    ->visible(fn ($record) => $record->extraServices->count() > 0)
                    ->collapsible(),

                // Event Details
                Infolists\Components\Section::make('Event Details')
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
