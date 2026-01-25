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
class ViewBooking1 extends ViewRecord
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
            Actions\EditAction::make()
                ->label(__('booking.actions.edit')),

            /**
             * Confirm Booking Action
             *
             * Transitions booking from 'pending' to 'confirmed' status.
             * Only visible for pending bookings.
             */
            Actions\Action::make('confirm')
                ->label(__('booking.actions.confirm'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.confirm_modal_heading'))
                ->modalDescription(__('booking.actions.confirm_modal_description'))
                ->action(function () {
                    // Call the confirm method on the booking model
                    $this->record->confirm();
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title(__('booking.notifications.booking_confirmed_title'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status === 'pending'),

            /**
             * Cancel Booking Action
             *
             * Allows cancellation of pending or confirmed bookings with a reason.
             * Opens a form modal to collect cancellation reason.
             */
            Actions\Action::make('cancel')
                ->label(__('booking.actions.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.cancel_modal_heading'))
                ->modalDescription(__('booking.actions.cancel_modal_description'))
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label(__('booking.form.cancellation_reason'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('booking.form.cancellation_reason_placeholder')),
                ])
                ->action(function (array $data) {
                    // Call the cancel method with the provided reason
                    $this->record->cancel($data['reason']);
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title(__('booking.notifications.booking_cancelled_title'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => in_array($this->record->status, ['pending', 'confirmed'])),

            /**
             * Complete Booking Action
             *
             * Marks a confirmed booking as completed after the event date has passed.
             * Only visible for confirmed bookings where the booking date is in the past.
             */
            Actions\Action::make('complete')
                ->label(__('booking.actions.complete'))
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.complete_modal_heading'))
                ->modalDescription(__('booking.actions.complete_modal_description'))
                ->action(function () {
                    // Call the complete method on the booking model
                    $this->record->complete();
                    $this->record->refresh();

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title(__('booking.notifications.booking_completed_title'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status === 'confirmed' &&
                    $this->record->booking_date->isPast()),

            /**
             * Download Invoice Action
             *
             * Downloads the previously generated invoice PDF.
             * Only visible when an invoice exists.
             */
            Actions\Action::make('downloadInvoice')
                ->label(__('booking.actions.download_invoice'))
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
                            ->title(__('booking.notifications.invoice_not_available_title'))
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
                ->label(__('booking.actions.generate_invoice'))
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
                            ->title(__('booking.notifications.invoice_generated_title'))
                            ->body(__('booking.notifications.invoice_generated_body', ['filename' => $filename]))
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
                            ->title(__('booking.notifications.invoice_generation_failed_title'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn() => empty($this->record->invoice_path) &&
                    $this->record->status === 'confirmed'),

            /**
             * Send Reminder Action
             *
             * Sends a booking reminder notification to the customer.
             * Only visible for confirmed future bookings.
             */
            Actions\Action::make('sendReminder')
                ->label(__('booking.actions.send_reminder'))
                ->icon('heroicon-o-bell')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.send_reminder_modal_heading'))
                ->modalDescription(__('booking.actions.send_reminder_modal_description'))
                ->action(function () {
                    // Get the notification service from container
                    $notificationService = app(\App\Services\NotificationService::class);

                    // Send the reminder notification
                    $notificationService->sendBookingReminderNotification($this->record);

                    // Send success notification
                    \Filament\Notifications\Notification::make()
                        ->title(__('booking.notifications.reminder_sent_title'))
                        ->success()
                        ->send();
                })
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
                ->label(__('booking.actions.mark_balance_paid'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->isAdvancePayment() && $this->record->isBalancePending())
                ->form([
                    \Filament\Forms\Components\Select::make('payment_method')
                        ->label(__('booking.form.balance_payment_method'))
                        ->options([
                            'bank_transfer' => __('booking.payment_methods.bank_transfer'),
                            'cash' => __('booking.payment_methods.cash'),
                            'card' => __('booking.payment_methods.card'),
                        ])
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('reference')
                        ->label(__('booking.form.balance_payment_reference'))
                        ->placeholder(__('booking.form.balance_payment_reference_placeholder'))
                        ->maxLength(255),

                    \Filament\Forms\Components\DateTimePicker::make('paid_at')
                        ->label(__('booking.form.payment_date'))
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
                        ->title(__('booking.notifications.balance_marked_paid_title'))
                        ->body(__('booking.notifications.balance_marked_paid_body'))
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
                Infolists\Components\Section::make(__('booking.sections.booking_information'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('booking_number')
                                    ->label(__('booking.labels.booking_number'))
                                    ->copyable()
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('booking.labels.status'))
                                    ->badge()
                                    ->formatStateUsing(fn($state) => __('booking.statuses.' . $state)),

                                Infolists\Components\TextEntry::make('payment_status')
                                    ->label(__('booking.labels.payment_status'))
                                    ->badge()
                                    ->formatStateUsing(fn($state) => __('booking.payment_statuses.' . $state)),
                            ]),
                    ]),

                /**
                 * Hall & Date Information Section
                 *
                 * Shows the booked hall, location, date, time slot, and event details.
                 */
                Infolists\Components\Section::make(__('booking.sections.hall_date_information'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall.name')
                                    ->label(__('booking.labels.hall'))
                                    ->formatStateUsing(fn($record) => $record->hall->name),

                                Infolists\Components\TextEntry::make('hall.city.name')
                                    ->label(__('booking.labels.location'))
                                    ->formatStateUsing(
                                        fn($record) =>
                                        $record->hall->city->name . ', ' .
                                            $record->hall->city->region->name
                                    ),

                                Infolists\Components\TextEntry::make('booking_date')
                                    ->label(__('booking.labels.booking_date'))
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('time_slot')
                                    ->label(__('booking.labels.time_slot'))
                                    ->badge()
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        __('booking.time_slots.' . $state)
                                    ),

                                Infolists\Components\TextEntry::make('number_of_guests')
                                    ->label(__('booking.labels.number_of_guests'))
                                    ->suffix(__('booking.labels.guests_suffix'))
                                    ->icon('heroicon-o-users'),

                                Infolists\Components\TextEntry::make('event_type')
                                    ->label(__('booking.labels.event_type'))
                                    ->formatStateUsing(
                                        fn(?string $state): string =>
                                        $state ? __('booking.event_types.' . $state) : '-'
                                    ),
                            ]),
                    ])->columns(2),

                /**
                 * Customer Details Section
                 *
                 * Displays customer contact information and notes.
                 */
                Infolists\Components\Section::make(__('booking.sections.customer_details'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('customer_name')
                                    ->label(__('booking.labels.customer_name'))
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('customer_email')
                                    ->label(__('booking.labels.customer_email'))
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),

                                Infolists\Components\TextEntry::make('customer_phone')
                                    ->label(__('booking.labels.customer_phone'))
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                            ]),

                        Infolists\Components\TextEntry::make('customer_notes')
                            ->label(__('booking.labels.customer_notes'))
                            ->columnSpanFull()
                            ->placeholder(__('booking.placeholders.no_notes')),
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
                Infolists\Components\Section::make(__('booking.sections.pricing_breakdown'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('hall_price')
                                    ->label(__('booking.labels.hall_price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('services_price')
                                    ->label(__('booking.labels.services_price'))
                                    ->money('OMR'),

                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label(__('booking.labels.subtotal'))
                                    ->money('OMR')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('commission_amount')
                                    ->label(__('booking.labels.commission_amount'))
                                    ->money('OMR')
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label(__('booking.labels.total_amount'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('owner_payout')
                                    ->label(__('booking.labels.owner_payout'))
                                    ->money('OMR')
                                    ->color('info'),
                            ]),
                    ])->columns(3),

                /**
                 * Advance Payment Details Section
                 */
                Infolists\Components\Section::make(__('booking.sections.advance_payment_details'))
                    ->description(function () {
                        if ($this->record->isAdvancePayment()) {
                            return $this->record->isBalancePending()
                                ? __('booking.descriptions.advance_payment_pending')
                                : __('booking.descriptions.advance_payment_paid');
                        }
                        return __('booking.descriptions.full_payment');
                    })
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                // Payment Type Badge
                                Infolists\Components\TextEntry::make('payment_type')
                                    ->label(__('booking.labels.payment_type'))
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(string $state): string => match ($state) {
                                        'full' => 'success',
                                        'advance' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string =>
                                        __('booking.payment_types.' . $state)
                                    ),

                                // Advance Amount Paid
                                Infolists\Components\TextEntry::make('advance_amount')
                                    ->label(__('booking.labels.advance_amount'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('warning')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('warning'),

                                // Balance Due
                                Infolists\Components\TextEntry::make('balance_due')
                                    ->label(__('booking.labels.balance_due'))
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
                                    ->label(__('booking.labels.balance_payment_status'))
                                    ->badge()
                                    ->size('lg')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->getStateUsing(fn() => $this->record->balance_paid_at
                                        ? __('booking.statuses.balance_paid')
                                        : __('booking.statuses.balance_pending'))
                                    ->color(fn() => $this->record->balance_paid_at ? 'success' : 'danger')
                                    ->icon(fn() => $this->record->balance_paid_at
                                        ? 'heroicon-o-check-badge'
                                        : 'heroicon-o-clock'),

                                // Balance Paid Date
                                Infolists\Components\TextEntry::make('balance_paid_at')
                                    ->label(__('booking.labels.balance_paid_at'))
                                    ->dateTime('M j, Y \a\t g:i A')
                                    ->placeholder(__('booking.placeholders.balance_not_paid'))
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->color(fn() => $this->record->balance_paid_at ? 'success' : 'gray')
                                    ->icon(fn() => $this->record->balance_paid_at ? 'heroicon-o-calendar-days' : null)
                                    ->iconColor('success'),

                                // Balance Payment Method
                                Infolists\Components\TextEntry::make('balance_payment_method')
                                    ->label(__('booking.labels.balance_payment_method'))
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn() => $this->record->balance_paid_at !== null)
                                    ->formatStateUsing(fn(string $state): string =>
                                        __('booking.payment_methods.' . $state)
                                    ),

                                // Balance Payment Reference
                                Infolists\Components\TextEntry::make('balance_payment_reference')
                                    ->label(__('booking.labels.balance_payment_reference'))
                                    ->placeholder('-')
                                    ->visible(fn() => $this->record->balance_paid_at !== null)
                                    ->copyable()
                                    ->copyMessage(__('booking.messages.reference_copied'))
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
                Infolists\Components\Section::make(__('booking.sections.extra_services'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('pivot.service_name')
                                    ->label(__('booking.labels.service_name')),
                                Infolists\Components\TextEntry::make('pivot.unit_price')
                                    ->label(__('booking.labels.unit_price'))
                                    ->money('OMR'),
                                Infolists\Components\TextEntry::make('pivot.quantity')
                                    ->label(__('booking.labels.quantity')),
                                Infolists\Components\TextEntry::make('pivot.total_price')
                                    ->label(__('booking.labels.total_price'))
                                    ->money('OMR'),
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
                Infolists\Components\Section::make(__('booking.sections.timestamps'))
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('booking.labels.created_at'))
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('confirmed_at')
                                    ->label(__('booking.labels.confirmed_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('completed_at')
                                    ->label(__('booking.labels.completed_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('cancelled_at')
                                    ->label(__('booking.labels.cancelled_at'))
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
                Infolists\Components\Section::make(__('booking.sections.cancellation_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('cancellation_reason')
                            ->label(__('booking.labels.cancellation_reason'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('refund_amount')
                            ->label(__('booking.labels.refund_amount'))
                            ->money('OMR'),
                    ])
                    ->visible(fn() => $this->record->status === 'cancelled'),

                /**
                 * Admin Notes Section
                 *
                 * Displays internal notes for administrators.
                 * Collapsed by default and only visible if notes exist.
                 */
                Infolists\Components\Section::make(__('booking.sections.admin_notes'))
                    ->schema([
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label(__('booking.labels.admin_notes'))
                            ->columnSpanFull()
                            ->placeholder(__('booking.placeholders.no_admin_notes')),
                    ])
                    ->collapsed()
                    ->visible(fn() => !empty($this->record->admin_notes)),
            ]);
    }
}
