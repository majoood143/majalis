<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use App\Services\PDFService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Fieldset;
use App\Filament\Admin\Resources\BookingResource;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Exception;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

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
            EditAction::make()
                ->label(__('booking.actions.edit')),

            /**
             * Confirm Booking Action
             *
             * Transitions booking from 'pending' to 'confirmed' status.
             * Only visible for pending bookings.
             */
            Action::make('confirm')
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
                    Notification::make()
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
            Action::make('cancel')
                ->label(__('booking.actions.cancel'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.cancel_modal_heading'))
                ->modalDescription(__('booking.actions.cancel_modal_description'))
                ->schema([
                    Textarea::make('reason')
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
                    Notification::make()
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
            Action::make('complete')
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
                    Notification::make()
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
            Action::make('downloadInvoice')
                ->label(__('booking.actions.download_invoice'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    // Check if invoice exists
                    if ($this->record->invoice_path) {
                        // Try local disk first (where PDFService saves invoices)
                        if (Storage::disk('local')->exists($this->record->invoice_path)) {
                            return response()->download(
                                Storage::disk('local')->path($this->record->invoice_path)
                            );
                        }

                        // Fall back to public disk
                        if (Storage::disk('public')->exists($this->record->invoice_path)) {
                            return response()->download(
                                Storage::disk('public')->path($this->record->invoice_path)
                            );
                        }

                        Notification::make()
                            ->title(__('booking.notifications.invoice_not_available_title'))
                            ->body('Invoice file not found. Please regenerate the invoice.')
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
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
            Action::make('generateInvoice')
                ->label(__('booking.actions.generate_invoice'))
                ->icon('heroicon-o-document-plus')
                ->color('gray')
                ->action(function () {
                    try {
                        // Get the PDF service from container
                        $pdfService = app(PDFService::class);

                        // Generate the invoice and get filename
                        $filename = $pdfService->generateBookingInvoice($this->record);

                        // Refresh the record to get updated invoice_path
                        $this->record->refresh();

                        // Verify the invoice was saved properly
                        if (empty($this->record->invoice_path)) {
                            throw new Exception('Invoice was not saved properly');
                        }

                        // Send success notification with filename
                        Notification::make()
                            ->title(__('booking.notifications.invoice_generated_title'))
                            ->body(__('booking.notifications.invoice_generated_body', ['filename' => $filename]))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        // Log the error for debugging
                        Log::error('Invoice generation failed in action', [
                            'booking_id' => $this->record->id,
                            'error' => $e->getMessage()
                        ]);

                        // Send error notification to user
                        Notification::make()
                            ->title(__('booking.notifications.invoice_generation_failed_title'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn() => $this->record->status === 'confirmed' && (
                    empty($this->record->invoice_path) ||
                    (!Storage::disk('local')->exists($this->record->invoice_path) &&
                     !Storage::disk('public')->exists($this->record->invoice_path))
                )),

            /**
             * Send Payment Link Action
             *
             * Creates a Thawani checkout session and emails the payment URL to the customer.
             * Only visible for pending bookings with a pending payment status and an email address.
             */
            Action::make('sendPaymentLink')
                ->label(__('booking.actions.send_payment_link'))
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.send_payment_link_modal_heading'))
                ->modalDescription(fn() => __('booking.actions.send_payment_link_modal_description', [
                    'email' => $this->record->customer_email,
                    'amount' => number_format(
                        $this->record->isAdvancePayment() && $this->record->advance_amount
                            ? (float) $this->record->advance_amount
                            : (float) $this->record->total_amount,
                        3
                    ),
                ]))
                ->action(function () {
                    try {
                        $this->record->load(['hall']);

                        $paymentService = app(PaymentService::class);
                        $result = $paymentService->createAdminPaymentLink($this->record);

                        if (!$result['success']) {
                            Notification::make()
                                ->danger()
                                ->title(__('booking.notifications.payment_link_failed_title'))
                                ->body($result['message'] ?? __('booking.notifications.payment_link_failed_body'))
                                ->persistent()
                                ->send();
                            return;
                        }

                        $notificationService = app(NotificationService::class);
                        $notificationService->sendPaymentLinkNotification($this->record, $result['payment_url']);

                        $this->record->refresh();

                        Notification::make()
                            ->success()
                            ->title(__('booking.notifications.payment_link_sent_title'))
                            ->body(__('booking.notifications.payment_link_sent_body', [
                                'email' => $this->record->customer_email,
                            ]))
                            ->send();
                    } catch (Exception $e) {
                        Log::error('Send payment link failed', [
                            'booking_id' => $this->record->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title(__('booking.notifications.payment_link_failed_title'))
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();
                    }
                })
                ->visible(fn() =>
                    $this->record->status === 'pending' &&
                    $this->record->payment_status === 'pending' &&
                    !empty($this->record->customer_email)
                ),

            /**
             * Send Reminder Action
             *
             * Sends a booking reminder notification to the customer.
             * Allows adding an optional custom message.
             * Only visible for confirmed future bookings.
             *
             * FIXED: Now properly handles errors and uses the BookingReminderMail mailable
             * with manual flag to allow sending reminders regardless of days until booking.
             */
            Action::make('sendReminder')
                ->label(__('booking.actions.send_reminder'))
                ->icon('heroicon-o-bell')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('booking.actions.send_reminder_modal_heading'))
                ->modalDescription(__('booking.actions.send_reminder_modal_description'))
                ->schema([
                    // Optional custom message field
                    Textarea::make('custom_message')
                        ->label(__('booking.form.reminder_custom_message'))
                        ->placeholder(__('booking.form.reminder_custom_message_placeholder'))
                        ->helperText(__('booking.form.reminder_custom_message_help'))
                        ->rows(3)
                        ->maxLength(500),

                    // Info about booking date
                    Placeholder::make('booking_info')
                        ->label(__('booking.labels.booking_date'))
                        ->content(fn() => $this->record->booking_date->format('l, d M Y') .
                            ' (' . $this->record->getDaysUntilBooking() . ' ' .
                            __('booking.labels.days_away') . ')'),
                ])
                ->action(function (array $data) {
                    try {
                        // Get the notification service from container
                        $notificationService = app(NotificationService::class);

                        // Send the reminder notification with manual flag = true
                        // This bypasses the "1 day before" check for manual reminders
                        $sent = $notificationService->sendBookingReminderNotification(
                            booking: $this->record,
                            manual: true,
                            customMessage: $data['custom_message'] ?? null
                        );

                        if ($sent) {
                            // Send success notification
                            Notification::make()
                                ->title(__('booking.notifications.reminder_sent_title'))
                                ->body(__('booking.notifications.reminder_sent_body', [
                                    'email' => $this->record->customer_email
                                ]))
                                ->success()
                                ->send();
                        } else {
                            // This shouldn't happen with manual=true, but handle it
                            Notification::make()
                                ->title(__('booking.notifications.reminder_not_sent_title'))
                                ->body(__('booking.notifications.reminder_not_sent_body'))
                                ->warning()
                                ->send();
                        }
                    } catch (Exception $e) {
                        // Log the error for debugging
                        Log::error('Failed to send booking reminder from action', [
                            'booking_id' => $this->record->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        // Send error notification to user with specific error message
                        Notification::make()
                            ->title(__('booking.notifications.reminder_failed_title'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
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
            Action::make('mark_balance_paid')
                ->label(__('booking.actions.mark_balance_paid'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->isAdvancePayment() && $this->record->isBalancePending())
                ->schema([
                    Select::make('payment_method')
                        ->label(__('booking.form.balance_payment_method'))
                        ->options([
                            'bank_transfer' => __('booking.payment_methods.bank_transfer'),
                            'cash' => __('booking.payment_methods.cash'),
                            'card' => __('booking.payment_methods.card'),
                        ])
                        ->required(),

                    TextInput::make('reference')
                        ->label(__('booking.form.balance_payment_reference'))
                        ->placeholder(__('booking.form.balance_payment_reference_placeholder'))
                        ->maxLength(255),

                    DateTimePicker::make('paid_at')
                        ->label(__('booking.form.payment_date'))
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'balance_paid_at' => $data['paid_at'],
                        'balance_payment_method' => $data['payment_method'],
                        'balance_payment_reference' => $data['reference'] ?? null,
                    ]);

                    $this->record->refresh();

                    Notification::make()
                        ->title(__('booking.notifications.balance_marked_paid_title'))
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Get the infolist schema for displaying booking details
     *
     * Organizes booking information into logical sections:
     * - Basic Details (status, booking number, dates)
     * - Hall Information
     * - Customer Information
     * - Payment Details
     * - Extra Services
     * - Timestamps
     * - Cancellation Details (if applicable)
     * - Admin Notes
     *
     * @param Schema $infolist
     * @return Schema
     */
    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                /**
                 * Basic Booking Details Section
                 *
                 * Displays core booking information including:
                 * - Status badge with color coding
                 * - Unique booking number
                 * - Booking date and time slot
                 */
                Section::make(__('booking.sections.booking_details'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking_number')
                                    ->label(__('booking.labels.booking_number'))
                                    ->copyable()
                                    ->weight('bold')
                                    ->size('lg')
                                    ->icon('heroicon-o-hashtag'),

                                TextEntry::make('status')
                                    ->label(__('booking.labels.status'))
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        __('booking.statuses.' . $state)
                                    ),

                                TextEntry::make('booking_date')
                                    ->label(__('booking.labels.booking_date'))
                                    ->date('l, d M Y')
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('time_slot')
                                    ->label(__('booking.labels.time_slot'))
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        __('booking.time_slots.' . $state)
                                    ),

                                TextEntry::make('event_type')
                                    ->label(__('booking.labels.event_type'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-sparkles'),

                                TextEntry::make('number_of_guests')
                                    ->label(__('booking.labels.number_of_guests'))
                                    ->numeric()
                                    ->placeholder('-')
                                    ->icon('heroicon-o-user-group'),
                            ]),
                    ]),

                /**
                 * Hall Information Section
                 *
                 * Shows the booked hall details with link to hall resource.
                 */
                Section::make(__('booking.sections.hall_date_information'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('hall.name')
                                    ->label(__('booking.labels.hall'))
                                    ->url(
                                        fn() => $this->record->hall
                                            ? route('filament.admin.resources.halls.view', $this->record->hall)
                                            : null
                                    )
                                    ->color('primary')
                                    ->icon('heroicon-o-building-office-2'),

                                TextEntry::make('hall.city.name')
                                    ->label(__('booking.labels.location'))
                                    ->placeholder('-'),

                                TextEntry::make('hall.capacity_max')
                                    ->label(__('booking.table.columns.number_of_guests'))
                                    ->numeric()
                                    ->suffix(' ' . __('booking.table.columns.number_of_guests')),
                            ]),
                    ])
                    ->collapsible(),

                /**
                 * Customer Information Section
                 *
                 * Displays customer contact details.
                 * Links to user profile if booking is by registered user.
                 */
                Section::make(__('booking.sections.customer_details'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('customer_name')
                                    ->label(__('booking.labels.customer_name'))
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('customer_email')
                                    ->label(__('booking.labels.customer_email'))
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('customer_phone')
                                    ->label(__('booking.labels.customer_phone'))
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),

                                TextEntry::make('user.name')
                                    ->label(__('booking.labels.registered_user'))
                                    ->placeholder(__('booking.placeholders.guest_booking'))
                                    // ->url(fn() => $this->record->user
                                    //     ? route('filament.admin.resources.users.view', $this->record->user)
                                    //     : null
                                    // )
                                    ->color('primary'),

                                TextEntry::make('customer_notes')
                                    ->label(__('booking.labels.customer_notes'))
                                    ->placeholder(__('booking.labels.customer_notes_placeholder'))
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->collapsible(),

                /**
                 * Payment Details Section
                 *
                 * Shows comprehensive payment information including:
                 * - Payment type (full/advance)
                 * - Amounts and status
                 * - Balance due for advance payments
                 * - Payment references
                 */
                Section::make(__('booking.sections.pricing_breakdown'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Payment Type
                                TextEntry::make('payment_type')
                                    ->label(__('booking.labels.payment_type'))
                                    ->badge()
                                    ->color(fn(?string $state): string => match ($state) {
                                        'full' => 'success',
                                        'advance' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(
                                        fn(?string $state): string =>
                                        $state ? __('booking.payment_types.' . $state) : '-'
                                    ),

                                // Payment Status
                                TextEntry::make('payment_status')
                                    ->label(__('booking.labels.payment_status'))
                                    ->badge()
                                    ->color(fn(?string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'partial' => 'info',
                                        'failed' => 'danger',
                                        'refunded' => 'gray',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(
                                        fn(?string $state): string =>
                                        $state ? __('booking.payment_statuses.' . $state) : '-'
                                    ),

                                // Total Amount
                                TextEntry::make('total_amount')
                                    ->label(__('booking.labels.total_amount'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('success')
                                    ->icon('heroicon-o-currency-dollar'),
                                TextEntry::make('commission_amount')
                                    ->label(__('booking.labels.commission_amount'))
                                    ->money('OMR')
                                    ->color('warning'),

                                TextEntry::make('platform_fee')
                                    ->label(__('booking.labels.platform_fee'))
                                    ->money('OMR')
                                    ->color('warning'),

                                TextEntry::make('owner_payout')
                                    ->label(__('booking.labels.owner_payout'))
                                    ->money('OMR')
                                    ->color('info'),
                                TextEntry::make('services_price')
                                    ->label(__('booking.labels.services_price'))
                                    ->money('OMR'),
                            ]),

                        // Advance Payment Details (only visible for advance payment bookings)
                        Grid::make(3)
                            ->schema([
                                // Amount Paid
                                TextEntry::make('amount_paid')
                                    ->label(__('booking.labels.amount_paid'))
                                    ->money('OMR')
                                    ->weight('bold')
                                    ->color('success')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('success'),

                                // Advance Amount
                                TextEntry::make('advance_amount')
                                    ->label(__('booking.labels.advance_amount'))
                                    ->money('OMR')
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->icon('heroicon-o-banknotes')
                                    ->iconColor('warning'),

                                // Balance Due
                                TextEntry::make('balance_due')
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
                                TextEntry::make('balance_payment_status')
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
                                TextEntry::make('balance_paid_at')
                                    ->label(__('booking.labels.balance_paid_at'))
                                    ->dateTime('M j, Y \a\t g:i A')
                                    ->placeholder(__('booking.placeholders.balance_not_paid'))
                                    ->visible(fn() => $this->record->isAdvancePayment())
                                    ->color(fn() => $this->record->balance_paid_at ? 'success' : 'gray')
                                    ->icon(fn() => $this->record->balance_paid_at ? 'heroicon-o-calendar-days' : null)
                                    ->iconColor('success'),

                                // Balance Payment Method
                                TextEntry::make('balance_payment_method')
                                    ->label(__('booking.labels.balance_payment_method'))
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn() => $this->record->balance_paid_at !== null)
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        __('booking.payment_methods.' . $state)
                                    ),

                                // Balance Payment Reference
                                TextEntry::make('balance_payment_reference')
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

                // Promo Code Section
                Section::make('Promo Code')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('promoCode.code')
                                    ->label('Code')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-ticket'),

                                TextEntry::make('promoCode.name')
                                    ->label('Promo Name'),

                                TextEntry::make('promo_discount_label')
                                    ->label('Discount')
                                    ->getStateUsing(fn() => $this->record->promoCode?->discount_label),

                                TextEntry::make('discount_amount')
                                    ->label('Amount Saved')
                                    ->money('OMR', 3)
                                    ->color('success')
                                    ->weight('bold')
                                    ->icon('heroicon-o-arrow-trending-down'),
                            ]),
                    ])
                    ->visible(fn() => $this->record->promo_code_id !== null),



                /**
                 * ============================================================================
                 * VIEWBOOKING.PHP FIX - ADD PAYMENT TRANSACTIONS SECTION
                 * ============================================================================
                 *
                 * This file contains the code to add a Payment Transactions section to the
                 * ViewBooking infolist. This section displays all payment records related
                 * to the booking.
                 *
                 * @package    App\Filament\Admin\Resources\BookingResource\Pages
                 * @author     Fix for Majalis Hall Booking Platform
                 * @version    1.0.0
                 * @requires   Filament 3.3, Laravel 12, PHP 8.4.12
                 */

                // ============================================================================
                // INSTRUCTIONS
                // ============================================================================
                //
                // Add this section AFTER the "Advance Payment Details" section (around line 651)
                // and BEFORE the "Extra Services" section (around line 660).
                //
                // Look for this line in your ViewBooking.php:
                //   ->collapsed(fn() => !$this->record->isAdvancePayment()),
                //
                // And add the Payment Transactions section right after it.
                // ============================================================================

                /**
                 * Payment Transactions Section
                 *
                 * Displays all payment records associated with this booking.
                 * Shows payment reference, amount, status, method, and timestamps.
                 * Supports multiple payment records for bookings with advance + balance payments.
                 *
                 * Features:
                 * - Color-coded status badges (paid=green, pending=yellow, failed=red, refunded=gray)
                 * - Copyable payment references and transaction IDs
                 * - Formatted currency display (OMR with 3 decimals)
                 * - Conditional display of refund information
                 * - Collapsed by default to reduce visual clutter
                 */
                Section::make(__('booking.sections.payment_transactions'))
                    ->description(__('booking.sections.payment_transactions_description'))
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        // Payment Transactions Summary Stats
                        Grid::make(4)
                            ->schema([
                                // Total Payments Count
                                TextEntry::make('payments_count')
                                    ->label(__('booking.labels.total_transactions'))
                                    ->getStateUsing(fn() => $this->record->payments->count())
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-queue-list'),

                                // Total Paid Amount
                                TextEntry::make('total_paid')
                                    ->label(__('booking.labels.total_paid'))
                                    ->getStateUsing(fn() => $this->record->payments
                                        ->where('status', 'paid')
                                        ->sum('amount'))
                                    ->money('OMR')
                                    ->color('success')
                                    ->weight('bold')
                                    ->icon('heroicon-o-banknotes'),

                                // Total Refunded Amount
                                TextEntry::make('total_refunded')
                                    ->label(__('booking.labels.total_refunded'))
                                    ->getStateUsing(fn() => $this->record->payments
                                        ->whereIn('status', ['refunded', 'partially_refunded'])
                                        ->sum('refund_amount'))
                                    ->money('OMR')
                                    ->color('danger')
                                    ->visible(fn() => $this->record->payments
                                        ->whereIn('status', ['refunded', 'partially_refunded'])
                                        ->count() > 0)
                                    ->icon('heroicon-o-arrow-uturn-left'),

                                // Pending Payments
                                TextEntry::make('pending_payments')
                                    ->label(__('booking.labels.pending_payments'))
                                    ->getStateUsing(fn() => $this->record->payments
                                        ->where('status', 'pending')
                                        ->count())
                                    ->badge()
                                    ->color(fn() => $this->record->payments
                                        ->where('status', 'pending')
                                        ->count() > 0 ? 'warning' : 'gray')
                                    ->visible(fn() => $this->record->payments
                                        ->where('status', 'pending')
                                        ->count() > 0)
                                    ->icon('heroicon-o-clock'),
                            ]),

                        // Divider
                        TextEntry::make('divider')
                            ->label('')
                            ->getStateUsing(fn() => '')
                            ->extraAttributes(['class' => 'border-t border-gray-200 dark:border-gray-700 my-4']),

                        // Payment Transactions List (RepeatableEntry)
                        RepeatableEntry::make('payments')
                            ->label(__('booking.labels.transaction_history'))
                            ->schema([
                                // Payment Reference & Transaction ID
                                Grid::make(4)
                                    ->schema([
                                        // Payment Reference
                                        TextEntry::make('payment_reference')
                                            ->label(__('booking.labels.balance_payment_reference'))
                                            ->copyable()
                                            ->copyMessage(__('booking.messages.reference_copied'))
                                            ->weight('bold')
                                            ->icon('heroicon-o-document-text')
                                            ->iconColor('primary'),

                                        // Transaction ID (from gateway)
                                        TextEntry::make('transaction_id')
                                            ->label(__('booking.labels.transaction_id'))
                                            ->limit(30)
                                            ->copyable()
                                            ->copyMessage(__('booking.messages.transaction_id_copied'))
                                            ->placeholder('-')
                                            ->icon('heroicon-o-hashtag'),

                                        // Amount
                                        TextEntry::make('amount')
                                            ->label(__('booking.labels.total_amount'))
                                            ->money('OMR')
                                            ->weight('bold')
                                            ->size('lg')
                                            ->color('success'),

                                        // Status Badge
                                        TextEntry::make('status')
                                            ->label(__('booking.labels.status'))
                                            ->badge()
                                            ->size('lg')
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'pending' => __('booking.payment_statuses.pending'),
                                                'paid' => __('booking.payment_statuses.paid'),
                                                'failed' => __('booking.payment_statuses.failed'),
                                                'refunded' => __('booking.payment_statuses.refunded'),
                                                'partially_refunded' => __('booking.payment_statuses.partially_refunded'),
                                                'cancelled' => __('booking.payment_statuses.cancelled'),
                                                default => ucfirst($state),
                                            })
                                            ->color(fn(string $state): string => match ($state) {
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'gray',
                                                'partially_refunded' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'gray',
                                            })
                                            ->icon(fn(string $state): string => match ($state) {
                                                'pending' => 'heroicon-o-clock',
                                                'paid' => 'heroicon-o-check-circle',
                                                'failed' => 'heroicon-o-x-circle',
                                                'refunded' => 'heroicon-o-arrow-uturn-left',
                                                'partially_refunded' => 'heroicon-o-arrow-path',
                                                'cancelled' => 'heroicon-o-x-mark',
                                                default => 'heroicon-o-question-mark-circle',
                                            }),
                                    ]),

                                // Payment Method & Timestamps Row
                                Grid::make(4)
                                    ->schema([
                                        // Payment Method
                                        TextEntry::make('payment_method')
                                            ->label(__('booking.labels.balance_payment_method'))
                                            ->badge()
                                            ->color('info')
                                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                                'online' => __('booking.payment_methods.online'),
                                                'cash' => __('booking.payment_methods.cash'),
                                                'bank_transfer' => __('booking.payment_methods.bank_transfer'),
                                                'card' => __('booking.payment_methods.card'),
                                                'thawani' => __('booking.payment_methods.thawani'),
                                                default => ucfirst($state ?? '-'),
                                            })
                                            ->placeholder('-'),

                                        // Paid At
                                        TextEntry::make('paid_at')
                                            ->label(__('booking.labels.balance_paid_at'))
                                            ->dateTime('M j, Y \a\t g:i A')
                                            ->placeholder('-')
                                            ->color(fn($state) => $state ? 'success' : 'gray')
                                            ->icon(fn($state) => $state ? 'heroicon-o-check' : null),

                                        // Failed At (only if failed)
                                        TextEntry::make('failed_at')
                                            ->label(__('booking.labels.failed_at'))
                                            ->dateTime('M j, Y \a\t g:i A')
                                            ->placeholder('-')
                                            ->color('danger')
                                            ->visible(fn($record) => $record->failed_at !== null)
                                            ->icon('heroicon-o-exclamation-triangle'),

                                        // Created At
                                        TextEntry::make('created_at')
                                            ->label(__('booking.labels.created_at'))
                                            ->dateTime('M j, Y \a\t g:i A')
                                            ->color('gray')
                                            ->icon('heroicon-o-calendar'),
                                    ]),

                                // Refund Information (only visible if refunded)
                                Fieldset::make(__('booking.labels.refund_details'))
                                    ->schema([
                                        TextEntry::make('refund_amount')
                                            ->label(__('booking.labels.refund_amount'))
                                            ->money('OMR')
                                            ->color('danger')
                                            ->weight('bold'),

                                        TextEntry::make('refund_reason')
                                            ->label(__('booking.labels.refund_reason'))
                                            ->placeholder('-')
                                            ->columnSpan(2),

                                        TextEntry::make('refunded_at')
                                            ->label(__('booking.labels.refunded_at'))
                                            ->dateTime('M j, Y \a\t g:i A'),
                                    ])
                                    ->columns(4)
                                    ->visible(fn($record) => in_array($record->status, ['refunded', 'partially_refunded'])),

                                // Failure Reason (only visible if failed)
                                TextEntry::make('failure_reason')
                                    ->label(__('booking.labels.failure_reason'))
                                    ->color('danger')
                                    ->columnSpanFull()
                                    ->visible(fn($record) => $record->status === 'failed' && !empty($record->failure_reason))
                                    ->icon('heroicon-o-exclamation-circle'),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn() => $this->record->payments->count() > 0)
                    ->collapsible()
                    ->collapsed(false), // Start expanded to show payment details

                // Extra Services Section
                // FIX: Since extraServices() is a HasMany to BookingExtraService model,
                // fields are directly on the model (not pivot). Use service_name, quantity, etc.
                Section::make('Extra Services')
                    ->schema([
                        RepeatableEntry::make('extraServices')
                            ->label('')
                            ->schema([
                                // FIX: service_name is a JSON/array field, needs formatting
                                TextEntry::make('service_name')
                                    ->label(__('booking.fields.service_id.label'))
                                    ->formatStateUsing(function ($state): string {
                                        // Handle JSON string
                                        if (is_string($state)) {
                                            $decoded = json_decode($state, true);
                                            if (is_array($decoded)) {
                                                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $state;
                                            }
                                            return $state;
                                        }
                                        // Handle array (cast by model)
                                        if (is_array($state)) {
                                            return $state[app()->getLocale()] ?? $state['en'] ?? '-';
                                        }
                                        return (string) ($state ?? '-');
                                    }),

                                // FIX: Direct field access — no "pivot." prefix needed
                                TextEntry::make('quantity')
                                    ->label(__('booking.fields.quantity.label')),

                                TextEntry::make('unit_price')
                                    ->label(__('booking.fields.unit_price.label'))
                                    ->money('OMR', 3),

                                TextEntry::make('total_price')
                                    ->label(__('booking.fields.total_price.label'))
                                    ->money('OMR', 3)
                                    ->weight('bold'),
                            ])
                            ->columns(4),
                    ])
                    ->visible(fn($record) => $record->extraServices->count() > 0)
                    ->collapsible(),

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
                Section::make(__('booking.sections.timestamps'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('booking.labels.created_at'))
                                    ->dateTime(),

                                TextEntry::make('confirmed_at')
                                    ->label(__('booking.labels.confirmed_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('completed_at')
                                    ->label(__('booking.labels.completed_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('cancelled_at')
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
                Section::make(__('booking.sections.cancellation_details'))
                    ->schema([
                        TextEntry::make('cancellation_reason')
                            ->label(__('booking.labels.cancellation_reason'))
                            ->columnSpanFull(),

                        TextEntry::make('refund_amount')
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
                Section::make(__('booking.sections.admin_notes'))
                    ->schema([
                        TextEntry::make('admin_notes')
                            ->label(__('booking.labels.admin_notes'))
                            ->columnSpanFull()
                            ->placeholder(__('booking.placeholders.no_admin_notes')),
                    ])
                    ->collapsed()
                    ->visible(fn() => !empty($this->record->admin_notes)),
            ]);
    }
}
