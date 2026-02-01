<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\BookingResource\Pages;
use App\Filament\Owner\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Events\Booking\BookingApproved;
use App\Events\Booking\BookingRejected;
use App\Filament\Components\GuestBookingComponents;


/**
 * BookingResource for Owner Panel
 *
 * Provides hall owners with read-mostly access to their bookings.
 * Owners can view booking details, approve/reject pending bookings,
 * and mark balance payments as received.
 *
 * @package App\Filament\Owner\Resources
 */
class BookingResource extends OwnerResource
{
    /**
     * The Eloquent model associated with this resource.
     */
    protected static ?string $model = Booking::class;

    /**
     * Navigation icon for the sidebar.
     */
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    /**
     * Navigation group for organizing menu items.
     */

    // public static function getNavigationGroup(): ?string
    // {
    //     return __('owner_booking.navigation.group');
    // }
    //protected static ?string $navigationGroup = ('owner_booking.navigation.group');

    public static function getNavigationGroup(): ?string
    {
        return __('owner_booking.navigation.group');
    }

    /**
     * Sort order within the navigation group.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Get the plural label for this resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('owner_booking.bookings');
    }

    /**
     * Get the singular label for this resource.
     */
    public static function getModelLabel(): string
    {
        return __('owner_booking.booking');
    }

    /**
     * Modify the base query to scope bookings to the authenticated owner's halls.
     * This ensures owners can only see bookings for their own halls.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // ->whereHas('hall', function (Builder $query): void {
            //     $query->where('owner_id', Auth::id());
            // })
            ->with(['hall', 'user', 'extraServices', 'payments']);
    }

    /**
     * Override parent's applyOwnerScope to use hall relationship.
     *
     * This prevents the parent OwnerResource from incorrectly filtering
     * by the user_id column (which is the customer, not the owner).
     * Bookings should be scoped through the hall's owner_id instead.
     *
     * @param Builder $query The Eloquent query builder
     * @param mixed $user The authenticated owner user
     * @return Builder The scoped query
     */
    protected static function applyOwnerScope(Builder $query, $user): Builder
    {
        return $query->whereHas('hall', function (Builder $q) use ($user): void {
            $q->where('owner_id', $user->id);
        });
    }

    /**
     * Define the form schema for viewing/editing bookings.
     * Note: Most fields are disabled as owners have limited edit access.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Booking Information Section
                Forms\Components\Section::make(__('owner_booking.form.sections.booking_information'))
                    ->description(__('owner_booking.form.sections.booking_information_description'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label(__('owner_booking.form.fields.booking_number'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('hall.name')
                            ->label(__('owner_booking.form.fields.hall'))
                            ->formatStateUsing(function ($record) {
                                if (!$record?->hall) {
                                    return __('owner_booking.general.na');
                                }
                                $name = $record->hall->name;
                                return $name[app()->getLocale()] ?? $name['en'] ?? __('owner_booking.general.na');
                            })
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label(__('owner_booking.form.fields.event_date'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('time_slot')
                            ->label(__('owner_booking.form.fields.time_slot'))
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'morning' => __('owner_booking.time_slots.morning'),
                                'afternoon' => __('owner_booking.time_slots.afternoon'),
                                'evening' => __('owner_booking.time_slots.evening'),
                                'full_day' => __('owner_booking.time_slots.full_day'),
                                default => $state ?? __('owner_booking.general.na'),
                            })
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('event_type')
                            ->label(__('owner_booking.form.fields.event_type'))
                            ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : __('owner_booking.general.na'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('number_of_guests')
                            ->label(__('owner_booking.form.fields.number_of_guests'))
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

                // Customer Information Section
                Forms\Components\Section::make(__('owner_booking.form.sections.customer_information'))
                    ->description(__('owner_booking.form.sections.customer_information_description'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label(__('owner_booking.form.fields.customer_name'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('customer_email')
                            ->label(__('owner_booking.form.fields.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->url(fn($record) => $record?->customer_email ? "mailto:{$record->customer_email}" : null)
                                    ->openUrlInNewTab()
                            ),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label(__('owner_booking.form.fields.phone'))
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('call')
                                    ->icon('heroicon-o-phone')
                                    ->url(fn($record) => $record?->customer_phone ? "tel:{$record->customer_phone}" : null)
                            ),

                        Forms\Components\Textarea::make('customer_notes')
                            ->label(__('owner_booking.form.fields.customer_notes'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                // Payment Information Section
                Forms\Components\Section::make(__('owner_booking.form.sections.payment_information'))
                    ->description(__('owner_booking.form.sections.payment_information_description'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('hall_price')
                            ->label(__('owner_booking.form.fields.hall_price'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('services_price')
                            ->label(__('owner_booking.form.fields.services_price'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('owner_booking.form.fields.total_amount'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->extraAttributes(['class' => 'font-bold']),

                        Forms\Components\TextInput::make('owner_payout')
                            ->label(__('owner_booking.form.fields.your_earnings'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('owner_booking.form.fields.your_earnings_help'))
                            ->extraAttributes(['class' => 'font-bold text-success-600']),

                        // Advance payment fields (conditional)
                        Forms\Components\TextInput::make('advance_amount')
                            ->label(__('owner_booking.form.fields.advance_paid'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record?->payment_type === 'advance'),

                        Forms\Components\TextInput::make('balance_due')
                            ->label(__('owner_booking.form.fields.balance_due'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record?->payment_type === 'advance' && (float) ($record?->balance_due ?? 0) > 0)
                            ->extraAttributes(['class' => 'font-bold text-warning-600']),
                    ])
                    ->columns(3),

                // Status Section
                Forms\Components\Section::make(__('owner_booking.form.sections.booking_status'))
                    ->schema([
                        Forms\Components\Placeholder::make('status_display')
                            ->label(__('owner_booking.form.fields.current_status'))
                            ->content(fn($record) => view('filament.components.booking-status-badge', [
                                'status' => $record?->status ?? 'pending',
                                'paymentStatus' => $record?->payment_status ?? 'pending',
                            ])),

                        Forms\Components\Placeholder::make('status_info')
                            ->label('')
                            ->content(fn($record) => self::getStatusInfoText($record))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Get informational text based on booking status.
     */
    private static function getStatusInfoText(?Model $record): string
    {
        if (!$record) {
            return '';
        }

        return match ($record->status) {
            'pending' => __('owner_booking.status.pending_info'),
            'confirmed' => __('owner_booking.status.confirmed_info'),
            'completed' => __('owner_booking.status.completed_info'),
            'cancelled' => __('owner_booking.status.cancelled_info') .
                ($record->cancellation_reason ? ' ' . __('owner_booking.status.cancelled_reason') . ' ' . $record->cancellation_reason : ''),
            default => '',
        };
    }

    /**
     * Define the table schema for listing bookings.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Booking Number with copy functionality
                Tables\Columns\TextColumn::make('booking_number')
                    ->label(__('owner_booking.table.columns.booking_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('owner_booking.table.copy_messages.booking_number'))
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                // Hall Name (translatable)
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('owner_booking.table.columns.hall'))
                    ->formatStateUsing(function ($record): string {
                        if (!$record?->hall) {
                            return __('owner_booking.general.na');
                        }
                        $name = $record->hall->name;
                        return $name[app()->getLocale()] ?? $name['en'] ?? __('owner_booking.general.na');
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('hall', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->toggleable(),

                // Customer Name
                Tables\Columns\TextColumn::make('customer_name')
                    ->label(__('owner_booking.table.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->description(fn($record): string => $record->customer_phone ?? ''),

                // Event Date with relative time
                Tables\Columns\TextColumn::make('booking_date')
                    ->label(__('owner_booking.table.columns.event_date'))
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn($record): string => $record->booking_date?->diffForHumans() ?? '')
                    ->color(fn($record): string => match (true) {
                        $record->booking_date?->isPast() => 'gray',
                        $record->booking_date?->isToday() => 'success',
                        $record->booking_date?->isTomorrow() => 'warning',
                        default => 'primary',
                    }),

                // Time Slot Badge
                Tables\Columns\TextColumn::make('time_slot')
                    ->label(__('owner_booking.table.columns.time'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'morning' => __('owner_booking.time_slots.morning'),
                        'afternoon' => __('owner_booking.time_slots.afternoon'),
                        'evening' => __('owner_booking.time_slots.evening'),
                        'full_day' => __('owner_booking.time_slots.full_day'),
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'morning' => 'info',
                        'afternoon' => 'warning',
                        'evening' => 'purple',
                        'full_day' => 'success',
                        default => 'gray',
                    }),

                // Booking Status Badge
                Tables\Columns\TextColumn::make('status')
                    ->label(__('owner_booking.table.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('owner_booking.status.pending'),
                        'confirmed' => __('owner_booking.status.confirmed'),
                        'completed' => __('owner_booking.status.completed'),
                        'cancelled' => __('owner_booking.status.cancelled'),
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'confirmed' => 'heroicon-m-check-circle',
                        'completed' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Owner Payout (what owner earns)
                Tables\Columns\TextColumn::make('owner_payout')
                    ->label(__('owner_booking.table.columns.your_earnings'))
                    ->money('OMR')
                    ->sortable()
                    ->color('success')
                    ->weight(FontWeight::Bold),

                // Payment Status Badge
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('owner_booking.table.columns.payment'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('owner_booking.payment.pending'),
                        'partial' => __('owner_booking.payment.partial'),
                        'paid' => __('owner_booking.payment.paid'),
                        'failed' => __('owner_booking.payment.failed'),
                        'refunded' => __('owner_booking.payment.refunded'),
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'partial' => 'info',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'paid' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-clock',
                        'partial' => 'heroicon-m-arrow-path',
                        'failed' => 'heroicon-m-x-circle',
                        'refunded' => 'heroicon-m-arrow-uturn-left',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                // Balance Due (for advance payments)
                Tables\Columns\TextColumn::make('balance_due')
                    ->label(__('owner_booking.table.columns.balance'))
                    ->money('OMR')
                    ->sortable()
                    ->color('warning')
                    ->visible(fn($record): bool => $record?->payment_type === 'advance' && (float) ($record?->balance_due ?? 0) > 0)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Number of Guests
                Tables\Columns\TextColumn::make('number_of_guests')
                    ->label(__('owner_booking.table.columns.guests'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('owner_booking.table.columns.booked_on'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            GuestBookingComponents::guestBadgeColumn(),

            // Optionally add token column for admins
            GuestBookingComponents::guestTokenColumn(),
            ])
            ->filters([
                // Hall Filter
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('owner_booking.filters.hall'))
                    ->relationship(
                        'hall',
                        'name',
                        fn(Builder $query) => $query->where('owner_id', Auth::id())
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name[app()->getLocale()] ?? $record->name['en'] ?? __('owner_booking.general.na'))
                    ->searchable()
                    ->preload(),

                // Status Filter
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('owner_booking.filters.booking_status'))
                    ->options([
                        'pending' => __('owner_booking.status.pending'),
                        'confirmed' => __('owner_booking.status.confirmed'),
                        'completed' => __('owner_booking.status.completed'),
                        'cancelled' => __('owner_booking.status.cancelled'),
                    ])
                    ->multiple(),

                // Payment Status Filter
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('owner_booking.filters.payment_status'))
                    ->options([
                        'pending' => __('owner_booking.payment.pending'),
                        'partial' => __('owner_booking.payment.partial'),
                        'paid' => __('owner_booking.payment.paid'),
                        'failed' => __('owner_booking.payment.failed'),
                        'refunded' => __('owner_booking.payment.refunded'),
                    ])
                    ->multiple(),

                // Date Range Filter
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('owner_booking.filters.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('owner_booking.filters.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = __('owner_booking.filters.from') . ' ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('owner_booking.filters.until') . ' ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                // Quick Filters
                Tables\Filters\TernaryFilter::make('upcoming')
                    ->label(__('owner_booking.filters.upcoming_events'))
                    ->placeholder(__('owner_booking.filters.all_bookings'))
                    ->trueLabel(__('owner_booking.filters.upcoming_only'))
                    ->falseLabel(__('owner_booking.filters.past_only'))
                    ->queries(
                        true: fn(Builder $query) => $query->where('booking_date', '>=', now()->toDateString()),
                        false: fn(Builder $query) => $query->where('booking_date', '<', now()->toDateString()),
                        blank: fn(Builder $query) => $query,
                    ),

                // Needs Action Filter (pending or balance due)
                Tables\Filters\Filter::make('needs_action')
                    ->label(__('owner_booking.filters.needs_action'))
                    ->query(fn(Builder $query): Builder => $query->where(function (Builder $q) {
                        $q->where('status', 'pending')
                            ->orWhere(function (Builder $q2) {
                                $q2->where('payment_type', 'advance')
                                    ->where('balance_due', '>', 0)
                                    ->whereNull('balance_paid_at');
                            });
                    }))
                    ->toggle(),
            // Add booking type filter
            GuestBookingComponents::bookingTypeFilter(),
                    ])
            ->filtersFormColumns(2)
            ->actions([
                // View Action
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

            // Approve Action (for pending bookings that require approval)
            // Tables\Actions\Action::make('approve')
            //     ->label(__('owner_booking.actions.approve.label'))
            //     ->icon('heroicon-o-check-circle')
            //     ->color('success')
            //     ->requiresConfirmation()
            //     ->modalHeading(__('owner_booking.actions.approve.modal_heading'))
            //     ->modalDescription(__('owner_booking.actions.approve.modal_description'))
            //     ->modalSubmitActionLabel(__('owner_booking.actions.approve.modal_submit_label'))
            //     ->visible(fn(Booking $record): bool =>
            //         $record->status === 'pending' &&
            //         $record->hall?->requires_approval
            //     )
            //     ->action(function (Booking $record): void {
            //         $record->update([
            //             'status' => 'confirmed',
            //             'confirmed_at' => now(),
            //         ]);

            //         // TODO: Send notification to customer
            //         // event(new BookingApproved($record));

            //         Notification::make()
            //             ->title(__('owner_booking.notifications.approve.title'))
            //             ->body(__('owner_booking.notifications.approve.body', ['number' => $record->booking_number]))
            //             ->success()
            //             ->send();
            //     }),


            Tables\Actions\Action::make('approve')
                ->label(__('Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('Approve Booking'))
                ->modalDescription(__('Are you sure you want to approve this booking? The customer will receive an email notification.'))
                ->modalSubmitActionLabel(__('Yes, Approve'))
                ->visible(
                    fn(Booking $record): bool =>
                    $record->status === 'pending' &&
                        $record->hall?->requires_approval
                )
                ->action(function (Booking $record): void {
                    $record->update([
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);

                    // Dispatch event - triggers email notification
                    event(new BookingApproved(
                        booking: $record,
                        approvedBy: auth()->id()
                    ));

                    Notification::make()
                        ->title(__('Booking Approved'))
                        ->body(__('Booking :number has been approved. Customer notified via email.', [
                            'number' => $record->booking_number
                        ]))
                        ->success()
                        ->send();
                }),

            // Reject Action (for pending bookings that require approval)
            // Tables\Actions\Action::make('reject')
            //     ->label(__('owner_booking.actions.reject.label'))
            //     ->icon('heroicon-o-x-circle')
            //     ->color('danger')
            //     ->requiresConfirmation()
            //     ->modalHeading(__('owner_booking.actions.reject.modal_heading'))
            //     ->modalDescription(__('owner_booking.actions.reject.modal_description'))
            //     ->form([
            //         Forms\Components\Textarea::make('rejection_reason')
            //             ->label(__('owner_booking.actions.reject.reason_label'))
            //             ->required()
            //             ->maxLength(500)
            //             ->placeholder(__('owner_booking.actions.reject.reason_placeholder')),
            //     ])
            //     ->visible(fn(Booking $record): bool =>
            //         $record->status === 'pending' &&
            //         $record->hall?->requires_approval
            //     )
            //     ->action(function (Booking $record, array $data): void {
            //         $record->update([
            //             'status' => 'cancelled',
            //             'cancelled_at' => now(),
            //             'cancellation_reason' => __('owner_booking.actions.reject.cancellation_reason_prefix') . $data['rejection_reason'],
            //         ]);

            //         // TODO: Send notification to customer
            //         // event(new BookingRejected($record));

            //         Notification::make()
            //             ->title(__('owner_booking.notifications.reject.title'))
            //             ->body(__('owner_booking.notifications.reject.body', ['number' => $record->booking_number]))
            //             ->warning()
            //             ->send();
            //     }),

            Tables\Actions\Action::make('reject')
                ->label(__('Reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Reject Booking'))
                ->modalDescription(__('The customer will be notified via email.'))
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label(__('Reason for Rejection'))
                        ->required()
                        ->maxLength(500)
                        ->helperText(__('This will be included in the customer email.')),
                ])
                ->visible(
                    fn(Booking $record): bool =>
                    $record->status === 'pending' &&
                        $record->hall?->requires_approval
                )
                ->action(function (Booking $record, array $data): void {
                    $record->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => 'Rejected: ' . $data['rejection_reason'],
                    ]);

                    // Dispatch event - triggers email notification
                    event(new BookingRejected(
                        booking: $record,
                        reason: $data['rejection_reason'],
                        rejectedBy: auth()->id()
                    ));

                    Notification::make()
                        ->title(__('Booking Rejected'))
                        ->body(__('Customer notified via email.'))
                        ->warning()
                        ->send();
                }),

                // Mark Balance Received (for advance payment bookings)
                Tables\Actions\Action::make('mark_balance_received')
                    ->label(__('owner_booking.actions.mark_balance.label'))
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->modalHeading(__('owner_booking.actions.mark_balance.modal_heading'))
                    ->modalDescription(__('owner_booking.actions.mark_balance.modal_description'))
                    ->form([
                        Forms\Components\Placeholder::make('balance_info')
                            ->label(__('owner_booking.actions.mark_balance.balance_info'))
                            ->content(fn(Booking $record): string => 'OMR ' . number_format((float) $record->balance_due, 3)),

                        Forms\Components\Select::make('payment_method')
                            ->label(__('owner_booking.actions.mark_balance.payment_method_label'))
                            ->options([
                                'cash' => __('owner_booking.payment_methods.cash'),
                                'bank_transfer' => __('owner_booking.payment_methods.bank_transfer'),
                                'card' => __('owner_booking.payment_methods.card'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('reference')
                            ->label(__('owner_booking.actions.mark_balance.reference_label'))
                            ->placeholder(__('owner_booking.actions.mark_balance.reference_placeholder'))
                            ->maxLength(100),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('owner_booking.actions.mark_balance.notes_label'))
                            ->placeholder(__('owner_booking.actions.mark_balance.notes_placeholder'))
                            ->maxLength(500),
                    ])
                    ->visible(fn(Booking $record): bool =>
                        $record->payment_type === 'advance' &&
                        (float) ($record->balance_due ?? 0) > 0 &&
                        $record->balance_paid_at === null &&
                        in_array($record->status, ['confirmed', 'pending'])
                    )
                    ->action(function (Booking $record, array $data): void {
                        $record->update([
                            'balance_paid_at' => now(),
                            'balance_payment_method' => $data['payment_method'],
                            'balance_payment_reference' => $data['reference'] ?? null,
                            'payment_status' => 'paid',
                            'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n" : '') .
                                __('owner_booking.actions.mark_balance.admin_note', [
                                    'amount' => number_format((float) $record->balance_due, 3),
                                    'method' => $data['payment_method'],
                                    'date' => now()->format('d M Y H:i'),
                                ]) .
                                ($data['notes'] ? "\n" . __('owner_booking.actions.mark_balance.notes_prefix') . $data['notes'] : ''),
                        ]);

                        Notification::make()
                            ->title(__('owner_booking.notifications.mark_balance.title'))
                            ->body(__('owner_booking.notifications.mark_balance.body', ['number' => $record->booking_number]))
                            ->success()
                            ->send();
                    }),

                // Contact Customer Action
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('call')
                        ->label(__('owner_booking.actions.contact.call'))
                        ->icon('heroicon-o-phone')
                        ->url(fn(Booking $record): string => "tel:{$record->customer_phone}")
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('email')
                        ->label(__('owner_booking.actions.contact.email'))
                        ->icon('heroicon-o-envelope')
                        ->url(fn(Booking $record): string => "mailto:{$record->customer_email}")
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('whatsapp')
                        ->label(__('owner_booking.actions.contact.whatsapp'))
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->url(fn(Booking $record): string => "https://api.whatsapp.com/send?phone={$record->customer_phone}")
                        ->openUrlInNewTab()
                        ->visible(fn(Booking $record): bool => !empty($record->customer_phone)),
                ])
                    ->label(__('owner_booking.actions.contact.label'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                // Owners have limited bulk actions
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label(__('owner_booking.actions.bulk.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // TODO: Implement export functionality
                            Notification::make()
                                ->title(__('owner_booking.notifications.export.title'))
                                ->body(__('owner_booking.notifications.export.body'))
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('booking_date', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading(__('owner_booking.table.empty_state.heading'))
            ->emptyStateDescription(__('owner_booking.table.empty_state.description'))
            ->emptyStateIcon('heroicon-o-calendar');
    }

    /**
     * Define the infolist schema for the view page.
     * This provides a comprehensive read-only view of booking details.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section with Status
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('booking_number')
                                    ->label(__('owner_booking.infolist.sections.header.booking_number'))
                                    ->weight(FontWeight::Bold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->copyable()
                                    ->copyMessage(__('owner_booking.infolist.copy_messages.copied')),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('owner_booking.infolist.sections.header.status'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'pending' => __('owner_booking.status.pending'),
                                        'confirmed' => __('owner_booking.status.confirmed'),
                                        'completed' => __('owner_booking.status.completed'),
                                        'cancelled' => __('owner_booking.status.cancelled'),
                                        default => ucfirst($state),
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('payment_status')
                                    ->label(__('owner_booking.infolist.sections.header.payment'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'pending' => __('owner_booking.payment.pending'),
                                        'partial' => __('owner_booking.payment.partial'),
                                        'paid' => __('owner_booking.payment.paid'),
                                        'failed' => __('owner_booking.payment.failed'),
                                        'refunded' => __('owner_booking.payment.refunded'),
                                        default => ucfirst($state),
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'partial' => 'info',
                                        'failed' => 'danger',
                                        'refunded' => 'gray',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('owner_booking.infolist.sections.header.booked_on'))
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ]),

                // Event Details Section
                Infolists\Components\Section::make(__('owner_booking.infolist.sections.event_details'))
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('owner_booking.infolist.sections.event_details.hall'))
                                    ->formatStateUsing(function ($record): string {
                                        if (!$record?->hall) {
                                            return __('owner_booking.general.na');
                                        }
                                        $name = $record->hall->name;
                                        return $name[app()->getLocale()] ?? $name['en'] ?? __('owner_booking.general.na');
                                    }),

                                Infolists\Components\TextEntry::make('booking_date')
                                    ->label(__('owner_booking.infolist.sections.event_details.event_date'))
                                    ->date('l, d F Y')
                                    ->color(fn($record): string => match (true) {
                                        $record->booking_date?->isPast() => 'gray',
                                        $record->booking_date?->isToday() => 'success',
                                        default => 'primary',
                                    }),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->label(__('owner_booking.infolist.sections.event_details.time_slot'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'morning' => __('owner_booking.time_slots.morning'),
                                        'afternoon' => __('owner_booking.time_slots.afternoon'),
                                        'evening' => __('owner_booking.time_slots.evening'),
                                        'full_day' => __('owner_booking.time_slots.full_day'),
                                        default => ucfirst($state),
                                    }),

                                Infolists\Components\TextEntry::make('event_type')
                                    ->label(__('owner_booking.infolist.sections.event_details.event_type'))
                                    ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : __('owner_booking.infolist.placeholders.not_specified'))
                                    ->placeholder(__('owner_booking.infolist.placeholders.not_specified')),

                                Infolists\Components\TextEntry::make('number_of_guests')
                                    ->label(__('owner_booking.infolist.sections.event_details.expected_guests'))
                                    ->numeric()
                                    ->suffix(' ' . __('owner_booking.infolist.sections.event_details.guests_suffix')),
                            ]),
                    ]),

                // Customer Information Section
                Infolists\Components\Section::make(__('owner_booking.infolist.sections.customer_information'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->label(__('owner_booking.infolist.sections.customer_information.name'))
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('customer_email')
                                    ->label(__('owner_booking.infolist.sections.customer_information.email'))
                                    ->icon('heroicon-o-envelope')
                                    ->url(fn($record): string => "mailto:{$record->customer_email}")
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('customer_phone')
                                    ->label(__('owner_booking.infolist.sections.customer_information.phone'))
                                    ->icon('heroicon-o-phone')
                                    ->url(fn($record): string => "tel:{$record->customer_phone}")
                                    ->copyable(),
                            ]),

                        Infolists\Components\TextEntry::make('customer_notes')
                            ->label(__('owner_booking.infolist.sections.customer_information.customer_notes'))
                            ->placeholder(__('owner_booking.infolist.placeholders.no_notes'))
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                // Financial Summary Section
                Infolists\Components\Section::make(__('owner_booking.infolist.sections.financial_summary'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall_price')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.hall_price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('services_price')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.services'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.total_amount'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('owner_payout')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.your_earnings'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                            ]),

                        // Advance Payment Details (if applicable)
                        Infolists\Components\Fieldset::make(__('owner_booking.infolist.sections.financial_summary.advance_payment_details'))
                            ->schema([
                                Infolists\Components\TextEntry::make('advance_amount')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.advance_paid'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.balance_due'))
                                    ->money('OMR')
                                    ->color(fn($record): string => (float) ($record->balance_due ?? 0) > 0 ? 'warning' : 'success'),

                                Infolists\Components\TextEntry::make('balance_paid_at')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.balance_received'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('owner_booking.infolist.placeholders.not_yet_received')),

                                Infolists\Components\TextEntry::make('balance_payment_method')
                                    ->label(__('owner_booking.infolist.sections.financial_summary.payment_method'))
                                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                                        'cash' => __('owner_booking.payment_methods.cash'),
                                        'bank_transfer' => __('owner_booking.payment_methods.bank_transfer'),
                                        'card' => __('owner_booking.payment_methods.card'),
                                        default => $state ?? __('owner_booking.general.na'),
                                    })
                                    ->placeholder(__('owner_booking.general.na')),
                            ])
                            ->columns(4)
                            ->visible(fn($record): bool => $record->payment_type === 'advance'),
                    ]),

                // Extra Services Section
                Infolists\Components\Section::make(__('owner_booking.infolist.sections.extra_services'))
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->schema([
                                Infolists\Components\TextEntry::make('service_name')
                                    ->label(__('owner_booking.infolist.sections.extra_services.service'))
                                    ->formatStateUsing(function ($state): string {
                                        if (is_string($state)) {
                                            $decoded = json_decode($state, true);
                                            if (is_array($decoded)) {
                                                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $state;
                                            }
                                            return trim($state, '"');
                                        }
                                        return (string) $state;
                                    }),

                                Infolists\Components\TextEntry::make('quantity')
                                    ->label(__('owner_booking.infolist.sections.extra_services.qty')),

                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label(__('owner_booking.infolist.sections.extra_services.unit_price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('total_price')
                                    ->label(__('owner_booking.infolist.sections.extra_services.total'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold),
                            ])
                            ->columns(4)
                            ->contained(false),
                    ])
                    ->visible(fn($record): bool => $record->extraServices->isNotEmpty())
                    ->collapsible(),

                // Timeline Section
                Infolists\Components\Section::make(__('owner_booking.infolist.sections.booking_timeline'))
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('owner_booking.infolist.sections.booking_timeline.booked'))
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle'),

                                Infolists\Components\TextEntry::make('confirmed_at')
                                    ->label(__('owner_booking.infolist.sections.booking_timeline.confirmed'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('owner_booking.infolist.placeholders.not_confirmed'))
                                    ->icon('heroicon-o-check-circle'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->label(__('owner_booking.infolist.sections.booking_timeline.completed'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('owner_booking.infolist.placeholders.not_completed'))
                                    ->icon('heroicon-o-check-badge'),

                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->label(__('owner_booking.infolist.sections.booking_timeline.cancelled'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('owner_booking.infolist.placeholders.not_cancelled'))
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger'),
                            ]),

                        Infolists\Components\TextEntry::make('cancellation_reason')
                            ->label(__('owner_booking.infolist.sections.booking_timeline.cancellation_reason'))
                            ->visible(fn($record): bool => !empty($record->cancellation_reason))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Get the relation managers for this resource.
     */
    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    /**
     * Get the pages for this resource.
     * Note: Owners cannot create bookings - they only view and manage existing ones.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }

    /**
     * Determine if the user can create records.
     * Owners cannot create bookings - only customers can.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Determine if the user can edit a record.
     * Owners have very limited edit capabilities (mostly via actions).
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * Determine if the user can delete a record.
     * Owners cannot delete bookings.
     */
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    /**
     * Get the navigation badge showing pending bookings count.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getEloquentQuery()
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? 'warning' : 'gray';
    }

    /**
     * Get the navigation badge tooltip.
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('owner_booking.navigation.badge_tooltip');
    }
}
