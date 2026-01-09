<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

/**
 * ViewBooking Page
 *
 * Displays detailed information about a booking with actions to manage booking lifecycle.
 * Supports status transitions: pending -> confirmed -> completed or cancelled.
 *
 * @package App\Filament\Admin\Resources\BookingResource\Pages
 */
class ViewBooking extends ViewRecord
{
    /**
     * The resource associated with this page
     *
     * @var string
     */
    protected static string $resource = BookingResource::class;

    /**
     * Get the header actions available on this page
     *
     * Provides actions for:
     * - Editing booking details
     * - Confirming pending bookings
     * - Cancelling bookings (with reason)
     * - Completing confirmed bookings
     * - Downloading/generating invoices
     * - Sending reminders to customers
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Standard edit action
            Actions\EditAction::make(),

            /**
             * Confirm Booking Action
             *
             * Transitions booking from 'pending' to 'confirmed' status.
             * Only visible for pending bookings.
             */
            Actions\Action::make('confirm')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    // Call the confirm method on the booking model
                    $this->record->confirm();
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Booking confirmed successfully')
                        ->success()
                        ->send();
                })
                // FIX: Removed ->value since status is already a string
                ->visible(fn() => $this->record->status === 'pending'),

            /**
             * Cancel Booking Action
             *
             * Allows cancellation of pending or confirmed bookings with a reason.
             * Opens a form modal to collect cancellation reason.
             */
            Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Cancellation Reason')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    // Call the cancel method with the provided reason
                    $this->record->cancel($data['reason']);
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Booking cancelled successfully')
                        ->success()
                        ->send();
                })
                // FIX: Removed ->value since status is already a string
                ->visible(fn() => in_array($this->record->status, ['pending', 'confirmed'])),

            /**
             * Complete Booking Action
             *
             * Marks a confirmed booking as completed after the event date has passed.
             * Only visible for confirmed bookings where the booking date is in the past.
             */
            Actions\Action::make('complete')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    // Call the complete method on the booking model
                    $this->record->complete();
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Booking completed successfully')
                        ->success()
                        ->send();
                })
                // FIX: Removed ->value since status is already a string
                ->visible(fn() => $this->record->status === 'confirmed' &&
                    $this->record->booking_date->isPast()),

            /**
             * Download Invoice Action
             *
             * Downloads the previously generated invoice PDF.
             * Only visible when an invoice exists.
             */
            Actions\Action::make('downloadInvoice')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    // Check if invoice exists
                    if ($this->record->invoice_path) {
                        // Return download response from private storage
                        return response()->download(
                            storage_path('app/private/' . $this->record->invoice_path)
                        );
                    } else {
                        // Show warning if invoice not available
                        \Filament\Notifications\Notification::make()
                            ->title('Invoice not available')
                            ->warning()
                            ->send();
                    }
                })
                ->visible(fn() => !empty($this->record->invoice_path)),

            /**
             * Generate Invoice Action
             *
             * Creates a new PDF invoice for the booking using PDFService.
             * Only visible for confirmed bookings without an existing invoice.
             * Includes comprehensive error handling and logging.
             */
            Actions\Action::make('generateInvoice')
                ->icon('heroicon-o-document-plus')
                ->color('gray')
                ->action(function () {
                    try {
                        // Get the PDF service from container
                        $pdfService = app(\App\Services\PDFService::class);

                        // Generate the invoice and get filename
                        $filename = $pdfService->generateBookingInvoice($this->record);

                        // Refresh the record to get updated invoice_path
                        $this->record->refresh();

                        // Verify the invoice was saved properly
                        if (empty($this->record->invoice_path)) {
                            throw new \Exception('Invoice was not saved properly');
                        }

                        // Send success notification with filename
                        \Filament\Notifications\Notification::make()
                            ->title('Invoice generated successfully')
                            ->body('Invoice saved as: ' . $filename)
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Illuminate\Support\Facades\Log::error('Invoice generation failed in action', [
                            'booking_id' => $this->record->id,
                            'error' => $e->getMessage()
                        ]);

                        // Send error notification to user
                        \Filament\Notifications\Notification::make()
                            ->title('Invoice generation failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                // FIX: Removed ->value since status is already a string
                ->visible(fn() => empty($this->record->invoice_path) &&
                    $this->record->status === 'confirmed'),

            /**
             * Send Reminder Action
             *
             * Sends a booking reminder notification to the customer.
             * Only visible for confirmed future bookings.
             */
            Actions\Action::make('sendReminder')
                ->icon('heroicon-o-bell')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    // Get the notification service from container
                    $notificationService = app(\App\Services\NotificationService::class);

                    // Send the reminder notification
                    $notificationService->sendBookingReminderNotification($this->record);

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Reminder sent successfully')
                        ->success()
                        ->send();
                })
                // FIX: Removed ->value since status is already a string
                ->visible(fn() => $this->record->status === 'confirmed' &&
                    $this->record->booking_date->isFuture()),

            /**
             * Mark Balance as Paid Action
             *
             * Allows admin to mark the balance amount as paid for advance payment bookings.
             * Only visible for advance payment bookings where balance is still pending.
             * Opens a form to collect payment method, reference, and date.
             */
            Actions\Action::make('mark_balance_paid')
                ->label(__('advance_payment.mark_balance_as_paid'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->isAdvancePayment() && $this->record->isBalancePending())
                ->form([
                    \Filament\Forms\Components\Select::make('payment_method')
                        ->label(__('advance_payment.balance_payment_method'))
                        ->options([
                            'bank_transfer' => __('advance_payment.bank_transfer'),
                            'cash' => __('advance_payment.cash'),
                            'card' => __('advance_payment.card'),
                        ])
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('reference')
                        ->label(__('advance_payment.balance_payment_reference'))
                        ->placeholder('Transaction ID or Receipt Number')
                        ->maxLength(255),

                    \Filament\Forms\Components\DateTimePicker::make('paid_at')
                        ->label(__('Payment Date'))
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'balance_paid_at' => $data['paid_at'],
                        'balance_payment_method' => $data['payment_method'],
                        'balance_payment_reference' => $data['reference'] ?? null,
                        'payment_status' => 'paid',
                    ]);

                    $this->record->refresh();

                    \Filament\Notifications\Notification::make()
                        ->title(__('advance_payment.balance_marked_as_paid'))
                        ->body(__('advance_payment.balance_payment_recorded'))
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Build the infolist for displaying booking details
     *
     * Organizes booking information into logical sections:
     * - Booking status and identification
     * - Hall and event details
     * - Customer information
     * - Pricing breakdown with commission
     * - Extra services list
     * - Timestamps and audit trail
     * - Cancellation details (if applicable)
     * - Admin notes
     *
     * @param Infolist $infolist
     * @return Infolist
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                /**
                 * Booking Information Section
                 *
                 * Displays the booking number, status badges, and payment status.
                 */
                Infolists\Components\Section::make('Booking Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('booking_number')
                                    ->label('Booking Number')
                                    ->copyable()
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('payment_status')
                                    ->badge(),
                            ]),
                    ]),

                /**
                 * Hall & Date Information Section
                 *
                 * Shows the booked hall, location, date, time slot, and event details.
                 */
                Infolists\Components\Section::make('Hall & Date Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label('Hall')
                                    ->formatStateUsing(fn($record) => $record->hall->name),

                                Infolists\Components\TextEntry::make('hall.city.name')
                                    ->label('Location')
                                    ->formatStateUsing(
                                        fn($record) =>
                                        $record->hall->city->name . ', ' .
                                            $record->hall->city->region->name
                                    ),

                                Infolists\Components\TextEntry::make('booking_date')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        ucfirst(str_replace('_', ' ', $state))
                                    ),

                                Infolists\Components\TextEntry::make('number_of_guests')
                                    ->suffix(' guests')
                                    ->icon('heroicon-o-users'),

                                Infolists\Components\TextEntry::make('event_type')
                                    ->formatStateUsing(
                                        fn(?string $state): string =>
                                        $state ? ucfirst($state) : '-'
                                    ),
                            ]),
                    ])->columns(2),

                /**
                 * Customer Details Section
                 *
                 * Displays customer contact information and notes.
                 */
                Infolists\Components\Section::make('Customer Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('customer_email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),

                                Infolists\Components\TextEntry::make('customer_phone')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                            ]),

                        Infolists\Components\TextEntry::make('customer_notes')
                            ->columnSpanFull()
                            ->placeholder('No notes provided'),
                    ]),

                /**
                 * Pricing Breakdown Section
                 *
                 * Shows detailed pricing including:
                 * - Base hall price
                 * - Services price
                 * - Subtotal
                 * - Platform commission
                 * - Total amount
                 * - Owner payout (after commission)
                 */
                Infolists\Components\Section::make('Pricing Breakdown')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall_price')
                                    ->money('OMR')
                                    ->label('Hall Price'),

                                Infolists\Components\TextEntry::make('services_price')
                                    ->money('OMR')
                                    ->label('Services'),

                                Infolists\Components\TextEntry::make('subtotal')
                                    ->money('OMR')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('commission_amount')
                                    ->money('OMR')
                                    ->label('Platform Fee')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('owner_payout')
                                    ->money('OMR')
                                    ->label('Owner Payout')
                                    ->color('info'),
                            ]),
                    ])->columns(3),

                /**
                 * ✅ ENHANCED: Advance Payment Details Section
                 *
                 * Shows comprehensive advance payment information:
                 * - Payment type (Full/Advance) with color-coded badge
                 * - Advance amount paid (large, bold, warning color)
                 * - Balance due (large, bold, red if pending, green if paid)
                 * - Balance paid date (if applicable)
                 * - Balance payment status badge
                 * - Payment method and reference (if balance paid)
                 *
                 * Only visible for bookings with advance payment.
                 * Positioned right after pricing for logical flow.
                 */
                Infolists\Components\Section::make('Advance Payment Details')
                    ->description(fn() => $this->record->isAdvancePayment()
                        ? ($this->record->isBalancePending()
                            ? '⚠️ This booking requires advance payment. Customer must pay remaining balance before the event.'
                            : '✅ This booking required advance payment. Balance has been paid.')
                        : 'This is a full payment booking. Customer pays the entire amount.')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // Payment Type Badge
                                Infolists\Components\TextEntry::make('payment_type')
                                    ->label(__('advance_payment.payment_type'))
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(string $state): string => match ($state) {
                                        'full' => 'success',
                                        'advance' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string =>
                                        __('advance_payment.payment_type_' . $state)
                                    ),

                                // Advance Amount Paid (prominent display)
                                Infolists\Components\TextEntry::make('advance_amount')
                                    ->label(__('advance_payment.advance_paid'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('warning')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('warning'),

                                // Balance Due (color-coded by status)
                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label(__('advance_payment.balance_due'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color(fn() => $this->record->isBalancePending() ? 'danger' : 'success')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->icon(fn() => $this->record->isBalancePending()
                                        ? 'heroicon-o-exclamation-triangle'
                                        : 'heroicon-o-check-circle')
                                    ->iconColor(fn() => $this->record->isBalancePending() ? 'danger' : 'success'),

                                // Balance Payment Status Badge
                                Infolists\Components\TextEntry::make('balance_payment_status')
                                    ->label(__('advance_payment.balance_payment_status'))
                                    ->badge()
                                    ->size('lg')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->getStateUsing(fn() => $this->record->balance_paid_at
                                        ? __('advance_payment.balance_paid')
                                        : __('advance_payment.balance_pending'))
                                    ->color(fn() => $this->record->balance_paid_at ? 'success' : 'danger')
                                    ->icon(fn() => $this->record->balance_paid_at
                                        ? 'heroicon-o-check-badge'
                                        : 'heroicon-o-clock'),

                                // Balance Paid Date
                                Infolists\Components\TextEntry::make('balance_paid_at')
                                    ->label(__('advance_payment.balance_paid_on'))
                                    ->dateTime('M j, Y \a\t g:i A')
                                    ->placeholder(__('advance_payment.balance_not_paid'))
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->color(fn() => $this->record->balance_paid_at ? 'success' : 'gray')
                                    ->icon(fn() => $this->record->balance_paid_at ? 'heroicon-o-calendar-days' : null)
                                    ->iconColor('success'),

                                // Balance Payment Method
                                Infolists\Components\TextEntry::make('balance_payment_method')
                                    ->label(__('advance_payment.balance_payment_method'))
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn() => $this->record->balance_paid_at !== null)
                                    ->formatStateUsing(fn(string $state): string =>
                                        __('advance_payment.' . $state)
                                    ),

                                // Balance Payment Reference
                                Infolists\Components\TextEntry::make('balance_payment_reference')
                                    ->label(__('advance_payment.balance_payment_reference'))
                                    ->placeholder('-')
                                    ->visible(fn() => $this->record->balance_paid_at !== null)
                                    ->copyable()
                                    ->copyMessage('Reference copied!')
                                    ->icon('heroicon-o-document-text'),
                            ]),
                    ])
                    ->columns(3)
                    ->visible(fn() => $this->record->payment_type !== null)
                    ->collapsible()
                    ->collapsed(fn() => !$this->record->isAdvancePayment()),

                /**
                 * Extra Services Section
                 *
                 * Lists all additional services booked with quantities and prices.
                 * Only visible if the booking has extra services.
                 */
                Infolists\Components\Section::make('Extra Services')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('pivot.service_name')
                                    ->label('Service'),
                                Infolists\Components\TextEntry::make('pivot.unit_price')
                                    ->money('OMR')
                                    ->label('Unit Price'),
                                Infolists\Components\TextEntry::make('pivot.quantity')
                                    ->label('Quantity'),
                                Infolists\Components\TextEntry::make('pivot.total_price')
                                    ->money('OMR')
                                    ->label('Total'),
                            ])
                            ->columns(4),
                    ])
                    ->visible(fn() => $this->record->extraServices->count() > 0),

                /**
                 * Timestamps Section
                 *
                 * Shows audit trail of when actions occurred:
                 * - Created at
                 * - Confirmed at
                 * - Completed at
                 * - Cancelled at (if applicable)
                 *
                 * Collapsed by default to reduce clutter.
                 */
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('confirmed_at')
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->dateTime()
                                    ->placeholder('-')
                                    ->visible(fn() => $this->record->cancelled_at),
                            ]),
                    ])
                    ->collapsed(),

                /**
                 * Cancellation Details Section
                 *
                 * Shows cancellation reason and refund amount.
                 * Only visible for cancelled bookings.
                 */
                Infolists\Components\Section::make('Cancellation Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('cancellation_reason')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('refund_amount')
                            ->money('OMR'),
                    ])
                    // FIX: Removed ->value since status is already a string
                    ->visible(fn() => $this->record->status === 'cancelled'),

                /**
                 * Admin Notes Section
                 *
                 * Displays internal notes for administrators.
                 * Collapsed by default and only visible if notes exist.
                 */
                Infolists\Components\Section::make('Admin Notes')
                    ->schema([
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->columnSpanFull()
                            ->placeholder('No admin notes'),
                    ])
                    ->collapsed()
                    ->visible(fn() => !empty($this->record->admin_notes)),
            ]);
    }
}
