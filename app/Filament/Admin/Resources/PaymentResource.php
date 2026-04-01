<?php

declare(strict_types=1);

/**
 * Payment Resource for Filament Admin Panel
 *
 * Manages payment records with full CRUD operations, refund processing,
 * and receipt generation (download, print, email) functionality.
 *
 * @package App\Filament\Admin\Resources
 * @version 2.0.0
 * @requires filamentphp/filament ^3.3
 * @requires barryvdh/laravel-dompdf ^2.0
 * @requires PHP ^8.4
 */

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PaymentReceiptMail;
//use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\ActionGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;


/**
 * PaymentResource
 *
 * Filament resource for managing payments with comprehensive features:
 * - View, create, edit payment records
 * - Process refunds (full/partial)
 * - Generate A5-sized receipts (download/print/email)
 * - Filter and search capabilities
 * - Status badges with color coding
 */
class PaymentResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Payment::class;

    /**
     * Navigation icon for the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    /**
     * Navigation group for organizing menu items.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Booking Management';

    /**
     * Sort order within the navigation group.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 2;

    /**
     * Get the singular label for the resource.
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('payment.singular');
    }

    /**
     * Get the plural label for the resource.
     *
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('payment.plural');
    }

    /**
     * Get the navigation label for the resource.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('payment.navigation_label');
    }

    /**
     * Define the form schema for creating/editing payments.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('payment.sections.payment_information'))
                    ->schema([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label(__('payment.fields.payment_reference'))
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'booking_number')
                            ->label(__('payment.fields.booking'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label(__('payment.fields.transaction_id'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('payment.fields.amount'))
                            ->numeric()
                            ->required()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\TextInput::make('currency')
                            ->label(__('payment.fields.currency'))
                            ->default('OMR')
                            ->maxLength(3),

                        Forms\Components\Select::make('status')
                            ->label(__('payment.fields.status'))
                            ->options([
                                'pending' => __('payment.status.pending'),
                                'paid' => __('payment.status.paid'),
                                'failed' => __('payment.status.failed'),
                                'refunded' => __('payment.status.refunded'),
                                'partially_refunded' => __('payment.status.partially_refunded'),
                                'refund_in_progress' => __('payment.status.refund_in_progress'),
                                'retrying' => __('payment.status.retrying'),
                                'reconciliation_pending' => __('payment.status.reconciliation_pending'),
                                'processing' => __('payment.status.processing'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(__('payment.fields.payment_method'))
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make(__('payment.sections.refund_information'))
                    ->schema([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label(__('payment.fields.refund_amount'))
                            ->numeric()
                            ->prefix('OMR')
                            ->step(0.001),

                        Forms\Components\Textarea::make('refund_reason')
                            ->label(__('payment.fields.refund_reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make(__('payment.sections.failure_information'))
                    ->schema([
                        Forms\Components\Textarea::make('failure_reason')
                            ->label(__('payment.fields.failure_reason'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make(__('payment.sections.gateway_response'))
                    ->schema([
                        Forms\Components\KeyValue::make('gateway_response')
                            ->label(__('payment.fields.gateway_response'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    /**
     * Define the table schema for listing payments.
     *
     * Includes columns, filters, and actions for:
     * - Viewing payment details
     * - Processing refunds
     * - Generating receipts (download/print/email)
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            // Eager load relationships for performance
            ->modifyQueryUsing(fn($query) => $query->with('booking'))
            ->columns([
                Tables\Columns\TextColumn::make('payment_reference')
                    ->label(__('payment.columns.payment_reference'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->label(__('payment.columns.booking_number'))
                    ->searchable()
                    ->sortable(),

                // Tables\Columns\TextColumn::make('transaction_id')
                //     ->label(__('payment.columns.transaction_id'))
                //     ->searchable()
                //     ->copyable()
                //     ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('payment.columns.amount'))
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('payment.columns.status'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'paid' => __('payment.status.paid'),
                        'pending' => __('payment.status.pending'),
                        'failed' => __('payment.status.failed'),
                        'refunded' => __('payment.status.refunded'),
                        'partially_refunded' => __('payment.status.partially_refunded'),
                        'refund_in_progress' => __('payment.status.refund_in_progress'),
                        'retrying' => __('payment.status.retrying'),
                        'reconciliation_pending' => __('payment.status.reconciliation_pending'),
                        'processing' => __('payment.status.processing'),
                        'canceled' => __('payment.status.canceled'),
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        'partially_refunded' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('payment.columns.payment_method'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(__('payment.columns.paid_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('payment.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('payment.filters.status'))
                    ->options([
                        'pending' => __('payment.status.pending'),
                        'paid' => __('payment.status.paid'),
                        'failed' => __('payment.status.failed'),
                        'refunded' => __('payment.status.refunded'),
                        'partially_refunded' => __('payment.status.partially_refunded'),
                    ]),

                Tables\Filters\Filter::make('paid_at')
                    ->label(__('payment.filters.paid_at'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('payment.fields.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('payment.fields.to_date')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('paid_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('paid_at', '<=', $data['until']));
                    }),
            ])
            ->actions([

            ActionGroup::make([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ActivityLogTimelineTableAction::make('Activities'),
            ]),
                // =========================================================
                // 📋 STANDARD CRUD ACTIONS
                // =========================================================


                // =========================================================
                // 📄 RECEIPT ACTIONS GROUP
                // =========================================================
                // Provides download, print, and email receipt functionality
                // A5 sized PDF receipts for compact printing
                Tables\Actions\ActionGroup::make([
                    /**
                     * Download Receipt Action
                     *
                     * Generates and downloads A5-sized payment receipt PDF.
                     * Includes payment details, booking info, and customer data.
                     * Only visible for payments that have been processed.
                     */
                    Tables\Actions\Action::make('downloadReceipt')
                        ->label(__('payment.actions.download_receipt'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn (Payment $record): bool =>
                            in_array($record->status, ['paid', 'refunded', 'partially_refunded'])
                        )
                        ->action(function (Payment $record) {
                            try {
                                // Load relationships for the receipt
                                $record->load('booking.hall');

                                // Prepare data for PDF template
                                $data = [
                                    'payment' => $record,
                                    'booking' => $record->booking,
                                    'hall' => $record->booking?->hall,
                                ];

                                // Generate A5 PDF with proper encoding for Arabic support
                                $pdf = Pdf::loadView('pdf.payment-receipt-a5', $data)
                                    ->setPaper('a5', 'portrait')
                                    ->setOption('isHtml5ParserEnabled', true)
                                    ->setOption('isRemoteEnabled', true)
                                    ->setOption('defaultFont', 'DejaVu Sans');

                                // Generate filename with payment reference
                                $filename = 'receipt_' . $record->payment_reference . '.pdf';

                                // Log successful generation
                                Log::info('Payment receipt generated for download', [
                                    'payment_id' => $record->id,
                                    'payment_reference' => $record->payment_reference,
                                    'generated_by' => Auth::user()?->email,
                                ]);

                                // Stream download response
                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    $filename,
                                    [
                                        'Content-Type' => 'application/pdf',
                                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                                    ]
                                );
                            } catch (\Exception $e) {
                                // Log error for debugging
                                Log::error('Receipt download failed', [
                                    'payment_id' => $record->id,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                // Show user-friendly error notification
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title(__('payment.notifications.download_failed'))
                                    ->body(__('payment.notifications.download_failed_body', [
                                        'error' => $e->getMessage()
                                    ]))
                                    ->persistent()
                                    ->send();

                                return null;
                            }
                        }),

                    /**
                     * Print Receipt Action
                     *
                     * Opens receipt in new browser tab for printing.
                     * Uses inline display mode instead of attachment download.
                     * Optimized for A5 paper size.
                     */
                    Tables\Actions\Action::make('printReceipt')
                        ->label(__('payment.actions.print_receipt'))
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->visible(fn (Payment $record): bool =>
                            in_array($record->status, ['paid', 'refunded', 'partially_refunded'])
                        )
                        ->action(function (Payment $record) {
                            try {
                                // Load relationships for the receipt
                                $record->load('booking.hall');

                                // Prepare data for PDF template
                                $data = [
                                    'payment' => $record,
                                    'booking' => $record->booking,
                                    'hall' => $record->booking?->hall,
                                ];

                                // Generate A5 PDF for printing
                                $pdf = Pdf::loadView('pdf.payment-receipt-a5', $data)
                                    ->setPaper('a5', 'portrait')
                                    ->setOption('isHtml5ParserEnabled', true)
                                    ->setOption('isRemoteEnabled', true)
                                    ->setOption('defaultFont', 'DejaVu Sans');

                                $filename = 'receipt_' . $record->payment_reference . '.pdf';

                                // Log print action
                                Log::info('Payment receipt generated for printing', [
                                    'payment_id' => $record->id,
                                    'payment_reference' => $record->payment_reference,
                                ]);

                                // Stream for inline display (opens in browser for printing)
                                return response()->streamDownload(
                                    function () use ($pdf) {
                                        echo $pdf->output();
                                    },
                                    $filename,
                                    [
                                        'Content-Type' => 'application/pdf',
                                        // 'inline' opens in browser instead of downloading
                                        'Content-Disposition' => 'inline; filename="' . $filename . '"',
                                    ]
                                );
                            } catch (\Exception $e) {
                                Log::error('Receipt print failed', [
                                    'payment_id' => $record->id,
                                    'error' => $e->getMessage(),
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title(__('payment.notifications.print_failed'))
                                    ->body(__('payment.notifications.print_failed_body', [
                                        'error' => $e->getMessage()
                                    ]))
                                    ->persistent()
                                    ->send();

                                return null;
                            }
                        }),

                    /**
                     * Email Receipt Action
                     *
                     * Sends payment receipt via email to customer.
                     * Includes PDF attachment with A5-sized receipt.
                     * Uses PaymentReceiptMail mailable class.
                     */
                    Tables\Actions\Action::make('emailReceipt')
                        ->label(__('payment.actions.email_receipt'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->visible(fn (Payment $record): bool =>
                            $record->status === 'paid' && $record->booking !== null
                        )
                        ->requiresConfirmation()
                        ->modalHeading(__('payment.modals.email_receipt.heading'))
                        ->modalDescription(__('payment.modals.email_receipt.description'))
                        ->modalSubmitActionLabel(__('payment.actions.send_email'))
                        ->modalWidth('md')
                        ->form([
                            Forms\Components\Section::make(__('payment.sections.email_details'))
                                ->description(__('payment.descriptions.email_receipt'))
                                ->schema([
                                    // Customer email input (pre-filled from booking)
                                    Forms\Components\TextInput::make('email')
                                        ->label(__('payment.fields.customer_email'))
                                        ->email()
                                        ->required()
                                        ->default(fn (Payment $record): ?string =>
                                            $record->booking?->customer_email
                                        )
                                        ->placeholder('customer@example.com')
                                        ->helperText(__('payment.helpers.email_receipt')),

                                    // Option to send copy to admin
                                    Forms\Components\Toggle::make('send_admin_copy')
                                        ->label(__('payment.fields.send_admin_copy'))
                                        ->default(false)
                                        ->helperText(__('payment.helpers.send_admin_copy')),

                                    // Custom message (optional)
                                    Forms\Components\Textarea::make('custom_message')
                                        ->label(__('payment.fields.custom_message'))
                                        ->rows(3)
                                        ->placeholder(__('payment.placeholders.custom_message'))
                                        ->helperText(__('payment.helpers.custom_message')),
                                ]),
                        ])
                        ->action(function (Payment $record, array $data): void {
                            try {
                                // Load relationships for email
                                $record->load('booking.hall');

                                // Validate email address
                                $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
                                if (!$email) {
                                    throw new \InvalidArgumentException(__('payment.errors.invalid_email'));
                                }

                                // Send email to customer
                                Mail::to($email)
                                    ->send(new PaymentReceiptMail($record));

                                // Log successful send
                                Log::info('Payment receipt email sent', [
                                    'payment_id' => $record->id,
                                    'payment_reference' => $record->payment_reference,
                                    'recipient' => $email,
                                    'sent_by' => Auth::user()?->email,
                                ]);

                                // Send admin copy if requested
                                if ($data['send_admin_copy'] ?? false) {
                                    $adminEmail = config('mail.admin_email', config('mail.from.address'));
                                    if ($adminEmail && $adminEmail !== $email) {
                                        Mail::to($adminEmail)
                                            ->send(new PaymentReceiptMail($record));

                                        Log::info('Payment receipt admin copy sent', [
                                            'payment_id' => $record->id,
                                            'admin_email' => $adminEmail,
                                        ]);
                                    }
                                }

                                // Success notification
                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title(__('payment.notifications.email_sent'))
                                    ->body(__('payment.notifications.email_sent_body', [
                                        'email' => $email
                                    ]))
                                    ->send();

                            } catch (\Exception $e) {
                                // Log error
                                Log::error('Payment receipt email failed', [
                                    'payment_id' => $record->id,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                // Error notification
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title(__('payment.notifications.email_failed'))
                                    ->body(__('payment.notifications.email_failed_body', [
                                        'error' => $e->getMessage()
                                    ]))
                                    ->persistent()
                                    ->send();
                            }
                        }),


                Tables\Actions\Action::make('refund')
                    ->label(__('payment.actions.refund'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(Payment $record): bool => $record->canBeRefunded())
                    ->form(function (Payment $record): array {
                        $remainingAmount = $record->getRemainingRefundableAmount();

                        return [
                            Forms\Components\Section::make(__('payment.sections.refund_details'))
                                ->description(__('payment.descriptions.refund_process'))
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Placeholder::make('payment_reference')
                                                ->label(__('payment.fields.payment_reference'))
                                                ->content($record->payment_reference),

                                            Forms\Components\Placeholder::make('booking_number')
                                                ->label(__('payment.fields.booking'))
                                                ->content($record->booking?->booking_number ?? __('payment.n_a')),

                                            Forms\Components\Placeholder::make('original_amount')
                                                ->label(__('payment.placeholders.original_amount'))
                                                ->content(number_format((float) $record->amount, 3) . ' OMR'),

                                            Forms\Components\Placeholder::make('already_refunded')
                                                ->label(__('payment.placeholders.already_refunded'))
                                                ->content(number_format((float) ($record->refund_amount ?? 0), 3) . ' OMR'),

                                            Forms\Components\Placeholder::make('refundable_amount')
                                                ->label(__('payment.placeholders.refundable_amount'))
                                                ->content(fn() => number_format($remainingAmount, 3) . ' OMR')
                                                ->columnSpanFull()
                                                ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                                        ]),

                                    Forms\Components\Radio::make('refund_type')
                                        ->label(__('payment.fields.refund_type'))
                                        ->options([
                                            'full' => __('payment.options.full_refund', ['amount' => number_format($remainingAmount, 3)]),
                                            'partial' => __('payment.options.partial_refund'),
                                        ])
                                        ->default('full')
                                        ->required()
                                        ->live()
                                        ->columnSpanFull(),

                                    Forms\Components\TextInput::make('amount')
                                        ->label(__('payment.fields.refund_amount_input'))
                                        ->numeric()
                                        ->required()
                                        ->prefix('OMR')
                                        ->step(0.001)
                                        ->minValue(0.001)
                                        ->maxValue($remainingAmount)
                                        ->default($remainingAmount)
                                        ->helperText(__('payment.helpers.max_refund', ['amount' => number_format($remainingAmount, 3)]))
                                        ->visible(fn($get) => $get('refund_type') === 'partial'),

                                    Forms\Components\Select::make('reason')
                                        ->label(__('payment.fields.refund_reason_select'))
                                        ->options([
                                            'Customer Request' => __('payment.refund_reasons.customer_request'),
                                            'Event Cancelled' => __('payment.refund_reasons.event_cancelled'),
                                            'Hall Unavailable' => __('payment.refund_reasons.hall_unavailable'),
                                            'Duplicate Payment' => __('payment.refund_reasons.duplicate_payment'),
                                            'Service Not Provided' => __('payment.refund_reasons.service_not_provided'),
                                            'Quality Issues' => __('payment.refund_reasons.quality_issues'),
                                            'Other' => __('payment.refund_reasons.other'),
                                        ])
                                        ->required()
                                        ->searchable()
                                        ->columnSpanFull(),

                                    Forms\Components\Textarea::make('notes')
                                        ->label(__('payment.fields.additional_notes'))
                                        ->rows(3)
                                        ->placeholder(__('payment.placeholders.additional_notes'))
                                        ->columnSpanFull(),

                                    Forms\Components\Checkbox::make('notify_customer')
                                        ->label(__('payment.fields.notify_customer'))
                                        ->default(true)
                                        ->helperText(__('payment.helpers.notify_customer')),
                                ])
                        ];
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('payment.modals.refund.heading'))
                    ->modalDescription(__('payment.modals.refund.description'))
                    ->modalSubmitActionLabel(__('payment.actions.process_refund'))
                    ->modalWidth('2xl')
                    ->action(function (Payment $record, array $data): void {
                        try {
                            // Determine refund amount
                            $amount = $data['refund_type'] === 'full'
                                ? $record->getRemainingRefundableAmount()
                                : (float) $data['amount'];

                            // Build refund reason
                            $reason = $data['reason'];
                            if (!empty($data['notes'])) {
                                $reason .= ' - ' . $data['notes'];
                            }
                            $reason .= ' | ' . __('payment.processed_by') . ': ' . (Auth::user()?->name ?? 'System');

                            // Process refund
                            $success = $record->refund($amount, $reason);

                            if ($success) {
                                // Send notification if requested
                                if ($data['notify_customer'] ?? false) {
                                    try {
                                        Log::info('Refund notification requested', [
                                            'payment_id' => $record->id,
                                            'amount' => $amount,
                                            'customer_email' => $record->booking?->customer_email,
                                        ]);
                                    } catch (\Exception $e) {
                                        Log::warning('Failed to send refund notification', [
                                            'payment_id' => $record->id,
                                            'error' => $e->getMessage(),
                                        ]);
                                    }
                                }

                                \Filament\Notifications\Notification::make()
                                    ->title(__('payment.notifications.refund_success'))
                                    ->success()
                                    ->body(__('payment.notifications.refund_success_body', ['amount' => number_format($amount, 3)]))
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('payment.notifications.refund_failed'))
                                ->danger()
                                ->body(__('payment.notifications.refund_failed_body', ['error' => $e->getMessage()]))
                                ->persistent()
                                ->send();

                            throw $e;
                        }
                    }),

            ])
                ->label(__('payment.actions.receipt'))
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->dropdown(),

                // =========================================================
                // 💰 REFUND ACTION
                // =========================================================
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Define the infolist schema for viewing payment details.
     *
     * @param Infolist $infolist
     * @return Infolist
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('payment.sections.payment_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_reference')
                            ->label(__('payment.fields.payment_reference'))
                            ->copyable(),
                        Infolists\Components\TextEntry::make('booking.booking_number')
                            ->label(__('payment.fields.booking')),
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label(__('payment.fields.transaction_id'))
                            ->copyable(),
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('payment.fields.amount'))
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('payment.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn($state): string => match ($state) {
                                'paid' => __('payment.status.paid'),
                                'pending' => __('payment.status.pending'),
                                'failed' => __('payment.status.failed'),
                                'refunded' => __('payment.status.refunded'),
                                'partially_refunded' => __('payment.status.partially_refunded'),
                                default => $state,
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                'partially_refunded' => 'info',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label(__('payment.fields.payment_method'))
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('payment.sections.timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_at')
                            ->label(__('payment.fields.paid_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('failed_at')
                            ->label(__('payment.fields.failed_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('refunded_at')
                            ->label(__('payment.fields.refunded_at'))
                            ->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make(__('payment.sections.refund_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('refund_amount')
                            ->label(__('payment.fields.refund_amount'))
                            ->money('OMR'),
                        Infolists\Components\TextEntry::make('refund_reason')
                            ->label(__('payment.fields.refund_reason')),
                    ])
                    ->visible(fn($record) => $record->isRefunded()),
            ]);
    }

    /**
     * Get the relations for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the pages for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    /**
     * Get the navigation badge showing failed payments count.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'failed')->count();
        return $count > 0 ? (string) $count : null;
    }
}
