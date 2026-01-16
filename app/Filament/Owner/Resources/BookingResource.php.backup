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
    protected static ?string $navigationGroup = 'Bookings';

    /**
     * Sort order within the navigation group.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Get the plural label for this resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('Bookings');
    }

    /**
     * Get the singular label for this resource.
     */
    // public static function getModelLabel(): string
    // {
    //     return __('Booking');
    // }

    /**
     * Modify the base query to scope bookings to the authenticated owner's halls.
     * This ensures owners can only see bookings for their own halls.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('hall', function (Builder $query): void {
                $query->where('owner_id', Auth::id());
            })
            ->with(['hall', 'user', 'extraServices', 'payments']);
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
                Forms\Components\Section::make(__('Booking Information'))
                    ->description(__('Basic booking details'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label(__('Booking Number'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('hall.name')
                            ->label(__('Hall'))
                            ->formatStateUsing(function ($record) {
                                if (!$record?->hall) {
                                    return 'N/A';
                                }
                                $name = $record->hall->name;
                                return $name[app()->getLocale()] ?? $name['en'] ?? 'N/A';
                            })
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label(__('Event Date'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('time_slot')
                            ->label(__('Time Slot'))
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'morning' => __('Morning'),
                                'afternoon' => __('Afternoon'),
                                'evening' => __('Evening'),
                                'full_day' => __('Full Day'),
                                default => $state ?? 'N/A',
                            })
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('event_type')
                            ->label(__('Event Type'))
                            ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : 'N/A')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('number_of_guests')
                            ->label(__('Number of Guests'))
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

                // Customer Information Section
                Forms\Components\Section::make(__('Customer Information'))
                    ->description(__('Contact details for the customer'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label(__('Customer Name'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('customer_email')
                            ->label(__('Email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('email')
                                    ->icon('heroicon-o-envelope')
                                    ->url(fn($record) => $record?->customer_email ? "mailto:{$record->customer_email}" : null)
                                    ->openUrlInNewTab()
                            ),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label(__('Phone'))
                            ->disabled()
                            ->dehydrated(false)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('call')
                                    ->icon('heroicon-o-phone')
                                    ->url(fn($record) => $record?->customer_phone ? "tel:{$record->customer_phone}" : null)
                            ),

                        Forms\Components\Textarea::make('customer_notes')
                            ->label(__('Customer Notes'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                // Payment Information Section
                Forms\Components\Section::make(__('Payment Information'))
                    ->description(__('Financial details for this booking'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('hall_price')
                            ->label(__('Hall Price'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('services_price')
                            ->label(__('Services Price'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total Amount'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->extraAttributes(['class' => 'font-bold']),

                        Forms\Components\TextInput::make('owner_payout')
                            ->label(__('Your Earnings'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('Amount after platform commission'))
                            ->extraAttributes(['class' => 'font-bold text-success-600']),

                        // Advance payment fields (conditional)
                        Forms\Components\TextInput::make('advance_amount')
                            ->label(__('Advance Paid'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record?->payment_type === 'advance'),

                        Forms\Components\TextInput::make('balance_due')
                            ->label(__('Balance Due'))
                            ->prefix('OMR')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($record) => $record?->payment_type === 'advance' && (float) ($record?->balance_due ?? 0) > 0)
                            ->extraAttributes(['class' => 'font-bold text-warning-600']),
                    ])
                    ->columns(3),

                // Status Section
                Forms\Components\Section::make(__('Booking Status'))
                    ->schema([
                        Forms\Components\Placeholder::make('status_display')
                            ->label(__('Current Status'))
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
            'pending' => __('This booking is awaiting confirmation. Please review and approve or reject.'),
            'confirmed' => __('This booking is confirmed. The customer has been notified.'),
            'completed' => __('This event has been completed successfully.'),
            'cancelled' => __('This booking was cancelled.') .
                ($record->cancellation_reason ? ' ' . __('Reason:') . ' ' . $record->cancellation_reason : ''),
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
                    ->label(__('Booking #'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('Booking number copied'))
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                // Hall Name (translatable)
                Tables\Columns\TextColumn::make('hall.name')
                    ->label(__('Hall'))
                    ->formatStateUsing(function ($record): string {
                        if (!$record?->hall) {
                            return 'N/A';
                        }
                        $name = $record->hall->name;
                        return $name[app()->getLocale()] ?? $name['en'] ?? 'N/A';
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
                    ->label(__('Customer'))
                    ->searchable()
                    ->sortable()
                    ->description(fn($record): string => $record->customer_phone ?? ''),

                // Event Date with relative time
                Tables\Columns\TextColumn::make('booking_date')
                    ->label(__('Event Date'))
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
                    ->label(__('Time'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'morning' => __('Morning'),
                        'afternoon' => __('Afternoon'),
                        'evening' => __('Evening'),
                        'full_day' => __('Full Day'),
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
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'confirmed' => __('Confirmed'),
                        'completed' => __('Completed'),
                        'cancelled' => __('Cancelled'),
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
                    ->label(__('Your Earnings'))
                    ->money('OMR')
                    ->sortable()
                    ->color('success')
                    ->weight(FontWeight::Bold),

                // Payment Status Badge
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('Payment'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'failed' => __('Failed'),
                        'refunded' => __('Refunded'),
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
                    ->label(__('Balance'))
                    ->money('OMR')
                    ->sortable()
                    ->color('warning')
                    ->visible(fn($record): bool => $record?->payment_type === 'advance' && (float) ($record?->balance_due ?? 0) > 0)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Number of Guests
                Tables\Columns\TextColumn::make('number_of_guests')
                    ->label(__('Guests'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created Date
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Booked On'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Hall Filter
                Tables\Filters\SelectFilter::make('hall_id')
                    ->label(__('Hall'))
                    ->relationship(
                        'hall',
                        'name',
                        fn(Builder $query) => $query->where('owner_id', Auth::id())
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name[app()->getLocale()] ?? $record->name['en'] ?? 'N/A')
                    ->searchable()
                    ->preload(),

                // Status Filter
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Booking Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'confirmed' => __('Confirmed'),
                        'completed' => __('Completed'),
                        'cancelled' => __('Cancelled'),
                    ])
                    ->multiple(),

                // Payment Status Filter
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'failed' => __('Failed'),
                        'refunded' => __('Refunded'),
                    ])
                    ->multiple(),

                // Date Range Filter
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('From Date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('Until Date')),
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
                            $indicators['from'] = __('From') . ' ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('Until') . ' ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                // Quick Filters
                Tables\Filters\TernaryFilter::make('upcoming')
                    ->label(__('Upcoming Events'))
                    ->placeholder(__('All bookings'))
                    ->trueLabel(__('Upcoming only'))
                    ->falseLabel(__('Past only'))
                    ->queries(
                        true: fn(Builder $query) => $query->where('booking_date', '>=', now()->toDateString()),
                        false: fn(Builder $query) => $query->where('booking_date', '<', now()->toDateString()),
                        blank: fn(Builder $query) => $query,
                    ),

                // Needs Action Filter (pending or balance due)
                Tables\Filters\Filter::make('needs_action')
                    ->label(__('Needs Action'))
                    ->query(fn(Builder $query): Builder => $query->where(function (Builder $q) {
                        $q->where('status', 'pending')
                            ->orWhere(function (Builder $q2) {
                                $q2->where('payment_type', 'advance')
                                    ->where('balance_due', '>', 0)
                                    ->whereNull('balance_paid_at');
                            });
                    }))
                    ->toggle(),
            ])
            ->filtersFormColumns(2)
            ->actions([
                // View Action
                Tables\Actions\ViewAction::make()
                    ->iconButton(),

                // Approve Action (for pending bookings that require approval)
                Tables\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve Booking'))
                    ->modalDescription(__('Are you sure you want to approve this booking? The customer will be notified.'))
                    ->modalSubmitActionLabel(__('Yes, Approve'))
                    ->visible(fn(Booking $record): bool =>
                        $record->status === 'pending' &&
                        $record->hall?->requires_approval
                    )
                    ->action(function (Booking $record): void {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);

                        // TODO: Send notification to customer
                        // event(new BookingApproved($record));

                        Notification::make()
                            ->title(__('Booking Approved'))
                            ->body(__('Booking :number has been approved successfully.', ['number' => $record->booking_number]))
                            ->success()
                            ->send();
                    }),

                // Reject Action (for pending bookings that require approval)
                Tables\Actions\Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject Booking'))
                    ->modalDescription(__('Are you sure you want to reject this booking? This action cannot be undone.'))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('Reason for Rejection'))
                            ->required()
                            ->maxLength(500)
                            ->placeholder(__('Please provide a reason for rejecting this booking...')),
                    ])
                    ->visible(fn(Booking $record): bool =>
                        $record->status === 'pending' &&
                        $record->hall?->requires_approval
                    )
                    ->action(function (Booking $record, array $data): void {
                        $record->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancellation_reason' => __('Rejected by hall owner: ') . $data['rejection_reason'],
                        ]);

                        // TODO: Send notification to customer
                        // event(new BookingRejected($record));

                        Notification::make()
                            ->title(__('Booking Rejected'))
                            ->body(__('Booking :number has been rejected.', ['number' => $record->booking_number]))
                            ->warning()
                            ->send();
                    }),

                // Mark Balance Received (for advance payment bookings)
                Tables\Actions\Action::make('mark_balance_received')
                    ->label(__('Record Balance'))
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->modalHeading(__('Record Balance Payment'))
                    ->modalDescription(__('Record that the remaining balance has been received from the customer.'))
                    ->form([
                        Forms\Components\Placeholder::make('balance_info')
                            ->label(__('Balance Due'))
                            ->content(fn(Booking $record): string => 'OMR ' . number_format((float) $record->balance_due, 3)),

                        Forms\Components\Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options([
                                'cash' => __('Cash'),
                                'bank_transfer' => __('Bank Transfer'),
                                'card' => __('Card (POS)'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('reference')
                            ->label(__('Reference/Receipt Number'))
                            ->placeholder(__('Optional reference number'))
                            ->maxLength(100),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->placeholder(__('Any additional notes...'))
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
                                __('Balance of OMR :amount received via :method on :date', [
                                    'amount' => number_format((float) $record->balance_due, 3),
                                    'method' => $data['payment_method'],
                                    'date' => now()->format('d M Y H:i'),
                                ]) .
                                ($data['notes'] ? "\n" . __('Notes: ') . $data['notes'] : ''),
                        ]);

                        Notification::make()
                            ->title(__('Balance Payment Recorded'))
                            ->body(__('Balance payment for booking :number has been recorded.', ['number' => $record->booking_number]))
                            ->success()
                            ->send();
                    }),

                // Contact Customer Action
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('call')
                        ->label(__('Call Customer'))
                        ->icon('heroicon-o-phone')
                        ->url(fn(Booking $record): string => "tel:{$record->customer_phone}")
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('email')
                        ->label(__('Email Customer'))
                        ->icon('heroicon-o-envelope')
                        ->url(fn(Booking $record): string => "mailto:{$record->customer_email}")
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('whatsapp')
                        ->label(__('WhatsApp'))
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->url(fn(Booking $record): string => "https://wa.me/{$record->customer_phone}")
                        ->openUrlInNewTab()
                        ->visible(fn(Booking $record): bool => !empty($record->customer_phone)),
                ])
                    ->label(__('Contact'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                // Owners have limited bulk actions
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label(__('Export Selected'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // TODO: Implement export functionality
                            Notification::make()
                                ->title(__('Export Started'))
                                ->body(__('Your export is being prepared...'))
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('booking_date', 'desc')
            ->poll('30s')
            ->striped()
            ->emptyStateHeading(__('No bookings yet'))
            ->emptyStateDescription(__('When customers book your halls, they will appear here.'))
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
                                    ->label(__('Booking Number'))
                                    ->weight(FontWeight::Bold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->copyable()
                                    ->copyMessage(__('Copied!')),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('Status'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'pending' => __('Pending'),
                                        'confirmed' => __('Confirmed'),
                                        'completed' => __('Completed'),
                                        'cancelled' => __('Cancelled'),
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
                                    ->label(__('Payment'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'pending' => __('Pending'),
                                        'partial' => __('Partial'),
                                        'paid' => __('Paid'),
                                        'failed' => __('Failed'),
                                        'refunded' => __('Refunded'),
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
                                    ->label(__('Booked On'))
                                    ->dateTime('d M Y H:i'),
                            ]),
                    ]),

                // Event Details Section
                Infolists\Components\Section::make(__('Event Details'))
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('Hall'))
                                    ->formatStateUsing(function ($record): string {
                                        if (!$record?->hall) {
                                            return 'N/A';
                                        }
                                        $name = $record->hall->name;
                                        return $name[app()->getLocale()] ?? $name['en'] ?? 'N/A';
                                    }),

                                Infolists\Components\TextEntry::make('booking_date')
                                    ->label(__('Event Date'))
                                    ->date('l, d F Y')
                                    ->color(fn($record): string => match (true) {
                                        $record->booking_date?->isPast() => 'gray',
                                        $record->booking_date?->isToday() => 'success',
                                        default => 'primary',
                                    }),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->label(__('Time Slot'))
                                    ->badge()
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'morning' => __('Morning'),
                                        'afternoon' => __('Afternoon'),
                                        'evening' => __('Evening'),
                                        'full_day' => __('Full Day'),
                                        default => ucfirst($state),
                                    }),

                                Infolists\Components\TextEntry::make('event_type')
                                    ->label(__('Event Type'))
                                    ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : __('Not specified'))
                                    ->placeholder(__('Not specified')),

                                Infolists\Components\TextEntry::make('number_of_guests')
                                    ->label(__('Expected Guests'))
                                    ->numeric()
                                    ->suffix(' ' . __('guests')),
                            ]),
                    ]),

                // Customer Information Section
                Infolists\Components\Section::make(__('Customer Information'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->label(__('Name'))
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('customer_email')
                                    ->label(__('Email'))
                                    ->icon('heroicon-o-envelope')
                                    ->url(fn($record): string => "mailto:{$record->customer_email}")
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('customer_phone')
                                    ->label(__('Phone'))
                                    ->icon('heroicon-o-phone')
                                    ->url(fn($record): string => "tel:{$record->customer_phone}")
                                    ->copyable(),
                            ]),

                        Infolists\Components\TextEntry::make('customer_notes')
                            ->label(__('Customer Notes'))
                            ->placeholder(__('No notes provided'))
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                // Financial Summary Section
                Infolists\Components\Section::make(__('Financial Summary'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall_price')
                                    ->label(__('Hall Price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('services_price')
                                    ->label(__('Services'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label(__('Total Amount'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('owner_payout')
                                    ->label(__('Your Earnings'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                            ]),

                        // Advance Payment Details (if applicable)
                        Infolists\Components\Fieldset::make(__('Advance Payment Details'))
                            ->schema([
                                Infolists\Components\TextEntry::make('advance_amount')
                                    ->label(__('Advance Paid'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label(__('Balance Due'))
                                    ->money('OMR')
                                    ->color(fn($record): string => (float) ($record->balance_due ?? 0) > 0 ? 'warning' : 'success'),

                                Infolists\Components\TextEntry::make('balance_paid_at')
                                    ->label(__('Balance Received'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('Not yet received')),

                                Infolists\Components\TextEntry::make('balance_payment_method')
                                    ->label(__('Payment Method'))
                                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                                        'cash' => __('Cash'),
                                        'bank_transfer' => __('Bank Transfer'),
                                        'card' => __('Card'),
                                        default => $state ?? __('N/A'),
                                    })
                                    ->placeholder(__('N/A')),
                            ])
                            ->columns(4)
                            ->visible(fn($record): bool => $record->payment_type === 'advance'),
                    ]),

                // Extra Services Section
                Infolists\Components\Section::make(__('Extra Services'))
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->schema([
                                Infolists\Components\TextEntry::make('service_name')
                                    ->label(__('Service'))
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
                                    ->label(__('Qty')),

                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('total_price')
                                    ->label(__('Total'))
                                    ->money('OMR')
                                    ->weight(FontWeight::Bold),
                            ])
                            ->columns(4)
                            ->contained(false),
                    ])
                    ->visible(fn($record): bool => $record->extraServices->isNotEmpty())
                    ->collapsible(),

                // Timeline Section
                Infolists\Components\Section::make(__('Booking Timeline'))
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Booked'))
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-o-plus-circle'),

                                Infolists\Components\TextEntry::make('confirmed_at')
                                    ->label(__('Confirmed'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('Not confirmed'))
                                    ->icon('heroicon-o-check-circle'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->label(__('Completed'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('Not completed'))
                                    ->icon('heroicon-o-check-badge'),

                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->label(__('Cancelled'))
                                    ->dateTime('d M Y H:i')
                                    ->placeholder(__('Not cancelled'))
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger'),
                            ]),

                        Infolists\Components\TextEntry::make('cancellation_reason')
                            ->label(__('Cancellation Reason'))
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
        return __('Pending bookings requiring attention');
    }
}
