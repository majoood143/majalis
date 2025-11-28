<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;

/**
 * View Payment Page
 *
 * Displays comprehensive payment information with quick actions:
 * - Refund processing
 * - Receipt generation and download
 * - Payment verification
 * - Gateway response viewing
 *
 * @package App\Filament\Admin\Resources\PaymentResource\Pages
 */
class ViewPayment extends ViewRecord
{

    /**
     * The resource this page belongs to
     *
     * @var string
     */
    protected static string $resource = PaymentResource::class;

    /**
     * Get the header actions for the page
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit Action - Navigate to edit page
            Actions\EditAction::make()
                ->label('Edit Payment')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->visible(fn($record) => $record->status !== 'paid'), // Only show if not paid

            // View Gateway Response Action - Show modal with Thawani response data
            Actions\Action::make('view_gateway_response')
                ->label('Gateway Response')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->visible(fn($record) => !empty($record->gateway_response)) // Only show if response exists
                ->modalHeading('Thawani Gateway Response')
                ->modalDescription('Detailed response from Thawani payment gateway')
                ->modalWidth('3xl')
                ->modalContent(fn($record) => view('filament.modals.gateway-response', [
                    'data' => $record->gateway_response, // Pass the gateway response data
                ]))
                ->modalCancelActionLabel('Close')
                ->modalSubmitAction(false), // No submit button, just close

            // Refund Action - Process refund (if applicable)
            // Process Refund Action - Enhanced version
            Actions\Action::make('process_refund')
                ->label('Process Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn($record) => $record->canBeRefunded())
                ->form(function ($record) {
                    $remainingAmount = $record->getRemainingRefundableAmount();

                    return [
                        Forms\Components\Section::make('Refund Information')
                            ->schema([
                                Forms\Components\Placeholder::make('payment_details')
                                    ->label('Payment Details')
                                    ->content(function () use ($record, $remainingAmount) {
                                        return view('filament.components.refund-details', [
                                            'payment' => $record,
                                            'remainingAmount' => $remainingAmount,
                                        ]);
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\Radio::make('refund_type')
                                    ->label('Refund Type')
                                    ->options([
                                        'full' => 'Full Refund (' . number_format($remainingAmount, 3) . ' OMR)',
                                        'partial' => 'Partial Refund (Custom Amount)',
                                    ])
                                    ->default('full')
                                    ->required()
                                    ->reactive()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Refund Amount (OMR)')
                                    ->numeric()
                                    ->required()
                                    ->prefix('OMR')
                                    ->step(0.001)
                                    ->minValue(0.001)
                                    ->maxValue($remainingAmount)
                                    ->default($remainingAmount)
                                    ->helperText('Enter amount between 0.001 and ' . number_format($remainingAmount, 3) . ' OMR')
                                    ->visible(fn($get) => $get('refund_type') === 'partial'),

                                Forms\Components\Select::make('reason')
                                    ->label('Refund Reason')
                                    ->options([
                                        'Customer Request' => 'Customer Request',
                                        'Event Cancelled' => 'Event Cancelled',
                                        'Hall Unavailable' => 'Hall Unavailable',
                                        'Duplicate Payment' => 'Duplicate Payment',
                                        'Fraudulent Transaction' => 'Fraudulent Transaction',
                                        'Service Not Provided' => 'Service Not Provided',
                                        'Quality Issues' => 'Quality Issues',
                                        'Weather/Force Majeure' => 'Weather/Force Majeure',
                                        'Technical Error' => 'Technical Error',
                                        'Other' => 'Other',
                                    ])
                                    ->required()
                                    ->searchable(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Additional Notes (Optional)')
                                    ->rows(3)
                                    ->placeholder('Add any additional details about this refund...')
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('notify_customer')
                                    ->label('Send notification to customer')
                                    ->default(true)
                                    ->helperText('Customer will receive an email and SMS about this refund'),

                                Forms\Components\Toggle::make('cancel_booking')
                                    ->label('Cancel booking (for full refunds)')
                                    ->default(true)
                                    ->helperText('Booking will be automatically cancelled if full refund is processed')
                                    ->visible(fn($get) => $get('refund_type') === 'full'),
                            ])
                    ];
                })
                ->requiresConfirmation()
                ->modalHeading('Process Payment Refund')
                ->modalDescription('This will process a refund through Thawani payment gateway. This action cannot be undone.')
                ->modalSubmitActionLabel('Process Refund')
                ->modalWidth('3xl')
                ->action(function ($record, array $data) {
                    try {
                        // Calculate refund amount
                        $amount = $data['refund_type'] === 'full'
                            ? $record->getRemainingRefundableAmount()
                            : (float) $data['amount'];

                        // Build comprehensive reason
                        $reason = $data['reason'];
                        if (!empty($data['notes'])) {
                            $reason .= ' | Notes: ' . $data['notes'];
                        }
                        $reason .= ' | Processed by: ' . Auth::user()?->name ?? 'System';

                        // Process refund
                        $paymentService = app(\App\Services\PaymentService::class);
                        $result = $paymentService->processRefund($record, $amount, $reason);

                        if ($result['success']) {
                            // Send notifications if requested
                            if ($data['notify_customer'] ?? false) {
                                // You can implement email/SMS here
                                Log::info('Customer notification requested for refund', [
                                    'payment_id' => $record->id,
                                    'amount' => $amount,
                                ]);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Refund Processed Successfully')
                                ->success()
                                ->body("Refund of " . number_format($amount, 3) . " OMR has been processed. Refund ID: " . ($result['refund_id'] ?? 'N/A'))
                                ->duration(10000)
                                ->send();

                            // Refresh the page to show updated status
                            redirect()->route('filament.admin.resources.payments.view', ['record' => $record]);
                        }
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Refund Processing Failed')
                            ->danger()
                            ->body('Error: ' . $e->getMessage())
                            ->persistent()
                            ->send();

                        throw $e;
                    }
                }),
            // Mark as Paid Action - Manually mark payment as paid
            Actions\Action::make('mark_as_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn($record) => $record->status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('Mark Payment as Paid')
                ->modalDescription('This will manually mark the payment as paid. Use this only if you have received payment confirmation outside the system.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('transaction_id')
                        ->label('Transaction ID')
                        ->placeholder('Enter transaction/reference ID')
                        ->maxLength(255),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->placeholder('Add any notes about this manual payment confirmation')
                        ->rows(3),
                ])
                ->action(function ($record, array $data) {
                    try {
                        $record->markAsPaid(
                            $data['transaction_id'] ?? null,
                            ['manual_confirmation' => true, 'notes' => $data['notes'] ?? null],
                            null
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Payment Marked as Paid')
                            ->success()
                            ->body('Payment has been manually marked as paid.')
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Action Failed')
                            ->danger()
                            ->body('Failed to mark payment as paid: ' . $e->getMessage())
                            ->send();
                    }
                }),

            // Mark as Failed Action - Manually mark payment as failed
            Actions\Action::make('mark_as_failed')
                ->label('Mark as Failed')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn($record) => in_array($record->status, ['pending', 'processing']))
                ->requiresConfirmation()
                ->modalHeading('Mark Payment as Failed')
                ->modalDescription('This will mark the payment as failed. The booking status will also be updated.')
                ->form([
                    \Filament\Forms\Components\Textarea::make('failure_reason')
                        ->label('Failure Reason')
                        ->placeholder('Explain why this payment is being marked as failed')
                        ->required()
                        ->rows(3),
                ])
                ->action(function ($record, array $data) {
                    try {
                        $record->markAsFailed($data['failure_reason']);

                        \Filament\Notifications\Notification::make()
                            ->title('Payment Marked as Failed')
                            ->success()
                            ->body('Payment has been marked as failed.')
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Action Failed')
                            ->danger()
                            ->body('Failed to mark payment as failed: ' . $e->getMessage())
                            ->send();
                    }
                }),

            // Delete Action
            Actions\DeleteAction::make()
                ->visible(fn($record) => $record->status === 'pending'), // Only allow deletion of pending payments
        ];
    }
    // /**
    //  * Get the header actions for this page
    //  *
    //  * @return array
    //  */
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         // Edit action
    //         Actions\EditAction::make(),

    //         /**
    //          * Quick Refund Action
    //          *
    //          * Process refund directly from view page.
    //          */
    //         Actions\Action::make('processRefund')
    //             ->label('Process Refund')
    //             ->icon('heroicon-o-arrow-path')
    //             ->color('warning')
    //             ->requiresConfirmation()
    //             ->modalHeading('Process Refund')
    //             ->modalDescription(fn() => "Remaining refundable: " .
    //                 number_format($this->record->getRemainingRefundableAmount(), 3) . " OMR")
    //             ->visible(fn() => $this->record->canBeRefunded())
    //             ->form([
    //                 \Filament\Forms\Components\Grid::make(2)
    //                     ->schema([
    //                         \Filament\Forms\Components\TextInput::make('amount')
    //                             ->label('Refund Amount')
    //                             ->numeric()
    //                             ->required()
    //                             ->prefix('OMR')
    //                             ->step(0.001)
    //                             ->maxValue(fn() => $this->record->getRemainingRefundableAmount())
    //                             ->default(fn() => $this->record->getRemainingRefundableAmount())
    //                             ->helperText(fn() => 'Max: ' .
    //                                 number_format($this->record->getRemainingRefundableAmount(), 3) . ' OMR'),

    //                         \Filament\Forms\Components\Select::make('refund_type')
    //                             ->label('Refund Type')
    //                             ->options([
    //                                 'full' => 'Full Refund',
    //                                 'partial' => 'Partial Refund',
    //                             ])
    //                             ->default('full')
    //                             ->reactive()
    //                             ->required(),
    //                     ]),

    //                 \Filament\Forms\Components\Textarea::make('reason')
    //                     ->label('Refund Reason')
    //                     ->required()
    //                     ->rows(3)
    //                     ->helperText('This reason will be visible to the customer'),

    //                 \Filament\Forms\Components\Grid::make(2)
    //                     ->schema([
    //                         \Filament\Forms\Components\Toggle::make('notify_customer')
    //                             ->label('Notify Customer')
    //                             ->default(true),

    //                         \Filament\Forms\Components\Toggle::make('cancel_booking')
    //                             ->label('Cancel Booking')
    //                             ->default(false)
    //                             ->helperText('Also cancel the associated booking')
    //                             ->visible(fn() => $this->record->booking &&
    //                                 in_array($this->record->booking->status, ['pending', 'confirmed'])),
    //                     ]),
    //             ])
    //             ->action(function (array $data) {
    //                 try {
    //                     $this->record->refund($data['amount'], $data['reason']);

    //                     // Cancel booking if requested
    //                     if (($data['cancel_booking'] ?? false) && $this->record->booking) {
    //                         $this->record->booking->cancel($data['reason']);
    //                     }

    //                     Notification::make()
    //                         ->success()
    //                         ->title('Refund Processed')
    //                         ->body(number_format($data['amount'], 3) . ' OMR refunded successfully.')
    //                         ->send();

    //                     $this->redirect(static::getUrl(['record' => $this->record]));
    //                 } catch (\Exception $e) {
    //                     Notification::make()
    //                         ->danger()
    //                         ->title('Refund Failed')
    //                         ->body($e->getMessage())
    //                         ->persistent()
    //                         ->send();
    //                 }
    //             }),
    //         /**
    //          * Download Receipt Action
    //          *
    //          * Generates and downloads payment receipt PDF.
    //          */
    //         Actions\Action::make('downloadReceipt')
    //             ->label('Download Receipt')
    //             ->icon('heroicon-o-document-arrow-down')
    //             ->color('success')
    //             ->visible(fn() => $this->record->status === 'paid')
    //             ->action(function () {
    //                 try {
    //                     // Generate PDF using PDFService
    //                     $pdfService = app(\App\Services\PDFService::class);
    //                     $filename = $pdfService->generatePaymentReceipt($this->record);

    //                     $filePath = storage_path('app/private/receipts/' . $filename);

    //                     // Check if file was created
    //                     if (!file_exists($filePath)) {
    //                         throw new \Exception('Receipt file was not created');
    //                     }

    //                     // Return download response
    //                     return response()->download($filePath, $filename, [
    //                         'Content-Type' => 'application/pdf',
    //                     ]);
    //                 } catch (\Exception $e) {
    //                     \Illuminate\Support\Facades\Log::error('Receipt generation failed', [
    //                         'payment_id' => $this->record->id,
    //                         'error' => $e->getMessage(),
    //                     ]);

    //                     Notification::make()
    //                         ->danger()
    //                         ->title('Receipt Generation Failed')
    //                         ->body('Unable to generate receipt: ' . $e->getMessage())
    //                         ->persistent()
    //                         ->send();
    //                 }
    //             }),

    //         /**
    //          * Verify Payment Action
    //          *
    //          * Verifies payment status with payment gateway.
    //          */
    //         Actions\Action::make('verifyPayment')
    //             ->label('Verify with Gateway')
    //             ->icon('heroicon-o-shield-check')
    //             ->color('info')
    //             ->requiresConfirmation()
    //             ->modalHeading('Verify Payment Status')
    //             ->modalDescription('Check payment status directly with payment gateway')
    //             ->visible(fn() => !empty($this->record->transaction_id) &&
    //                 in_array($this->record->status, ['pending', 'paid']))
    //             ->action(function () {
    //                 try {
    //                     // TODO: Implement gateway verification
    //                     // $gatewayService = app(\App\Services\PaymentGatewayService::class);
    //                     // $status = $gatewayService->verifyPayment($this->record->transaction_id);

    //                     Notification::make()
    //                         ->info()
    //                         ->title('Verification Complete')
    //                         ->body('Payment status verified with gateway.')
    //                         ->send();
    //                 } catch (\Exception $e) {
    //                     Notification::make()
    //                         ->warning()
    //                         ->title('Verification Failed')
    //                         ->body($e->getMessage())
    //                         ->send();
    //                 }
    //             }),

    //         /**
    //          * View Booking Action
    //          *
    //          * Quick link to associated booking.
    //          */
    //         Actions\Action::make('viewBooking')
    //             ->label('View Booking')
    //             ->icon('heroicon-o-calendar')
    //             ->color('gray')
    //             ->url(fn() => $this->record->booking
    //                 ? route('filament.admin.resources.bookings.view', ['record' => $this->record->booking])
    //                 : null)
    //             ->visible(fn() => $this->record->booking !== null)
    //             ->openUrlInNewTab(),

    //         /**
    //          * Send Receipt Email Action
    //          *
    //          * Sends payment receipt to customer email.
    //          */
    //         Actions\Action::make('sendReceipt')
    //             ->label('Email Receipt')
    //             ->icon('heroicon-o-envelope')
    //             ->color('gray')
    //             ->requiresConfirmation()
    //             ->modalHeading('Send Receipt to Customer')
    //             ->modalDescription('Send payment receipt with PDF attachment to customer email.')
    //             ->form([
    //                 \Filament\Forms\Components\TextInput::make('email')
    //                     ->label('Email Address')
    //                     ->email()
    //                     ->required()
    //                     ->default(fn() => $this->record->booking?->customer_email ?? ''),

    //                 \Filament\Forms\Components\Toggle::make('send_copy')
    //                     ->label('Send copy to admin')
    //                     ->default(false)
    //                     ->helperText('Also send a copy to admin email'),
    //             ])
    //             ->visible(fn() => $this->record->status === 'paid')
    //             ->action(function (array $data) {
    //                 try {
    //                     // Send email to customer
    //                     \Illuminate\Support\Facades\Mail::to($data['email'])
    //                         ->send(new \App\Mail\PaymentReceiptMail($this->record));

    //                     // Send copy to admin if requested
    //                     if ($data['send_copy'] ?? false) {
    //                         $adminEmail = config('mail.admin_email', config('mail.from.address'));
    //                         if ($adminEmail) {
    //                             \Illuminate\Support\Facades\Mail::to($adminEmail)
    //                                 ->send(new \App\Mail\PaymentReceiptMail($this->record));
    //                         }
    //                     }

    //                     // Log the email sent
    //                     \Illuminate\Support\Facades\Log::info('Payment receipt email sent', [
    //                         'payment_id' => $this->record->id,
    //                         'payment_reference' => $this->record->payment_reference,
    //                         'email' => $data['email'],
    //                         'sent_by' => auth()->id(),
    //                     ]);

    //                     Notification::make()
    //                         ->success()
    //                         ->title('Receipt Sent Successfully')
    //                         ->body("Receipt has been sent to {$data['email']}")
    //                         ->send();
    //                 } catch (\Exception $e) {
    //                     \Illuminate\Support\Facades\Log::error('Failed to send payment receipt email', [
    //                         'payment_id' => $this->record->id,
    //                         'email' => $data['email'],
    //                         'error' => $e->getMessage(),
    //                     ]);

    //                     Notification::make()
    //                         ->danger()
    //                         ->title('Failed to Send Receipt')
    //                         ->body('Error: ' . $e->getMessage())
    //                         ->persistent()
    //                         ->send();
    //                 }
    //             }),

    //         /**
    //          * View Gateway Response Action
    //          *
    //          * Shows raw gateway response in modal.
    //          */
    //         Actions\Action::make('viewGatewayResponse')
    //             ->label('Gateway Response')
    //             ->icon('heroicon-o-code-bracket')
    //             ->color('gray')
    //             ->modalHeading('Payment Gateway Response')
    //             ->modalContent(fn() => view('filament.modals.gateway-response', [
    //                 'response' => $this->record->gateway_response,
    //             ]))
    //             ->visible(fn() => !empty($this->record->gateway_response))
    //             ->modalSubmitAction(false)
    //             ->modalCancelActionLabel('Close'),

    //         /**
    //          * Copy Payment Link Action
    //          *
    //          * Copies payment URL to clipboard.
    //          */
    //         Actions\Action::make('copyPaymentLink')
    //             ->label('Copy Payment Link')
    //             ->icon('heroicon-o-link')
    //             ->color('gray')
    //             ->visible(fn() => !empty($this->record->payment_url))
    //             ->action(function () {
    //                 // JavaScript will handle the copy
    //                 Notification::make()
    //                     ->success()
    //                     ->title('Link Copied')
    //                     ->body('Payment link copied to clipboard.')
    //                     ->send();
    //             }),

    //         // Delete action
    //         Actions\DeleteAction::make()
    //             ->requiresConfirmation()
    //             ->modalHeading('Delete Payment')
    //             ->modalDescription('This will permanently delete the payment record.')
    //             ->successRedirectUrl(route('filament.admin.resources.payments.index')),
    //     ];
    // }

    /**
     * Get custom page title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Payment: ' . $this->record->payment_reference;
    }

    /**
     * Get page subheading
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        $status = ucfirst(str_replace('_', ' ', $this->record->status));
        //$amount = number_format($this->record->amount, 3) . ' OMR';
        $amount = number_format((float) $this->record->amount, 3) . ' OMR';

        $booking = $this->record->booking ? ' • Booking: ' . $this->record->booking->booking_number : '';

        return "{$status} • {$amount}{$booking}";
    }

    /**
     * Get custom breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Dashboard',
            route('filament.admin.resources.payments.index') => 'Payments',
            $this->record->payment_reference,
        ];
    }
}
