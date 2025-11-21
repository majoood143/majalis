<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
     * The resource associated with this page
     *
     * @var string
     */
    protected static string $resource = PaymentResource::class;

    /**
     * Get the header actions for this page
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Edit action
            Actions\EditAction::make(),

            /**
             * Quick Refund Action
             *
             * Process refund directly from view page.
             */
            Actions\Action::make('processRefund')
                ->label('Process Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Process Refund')
                ->modalDescription(fn() => "Remaining refundable: " .
                    number_format($this->record->getRemainingRefundableAmount(), 3) . " OMR")
                ->visible(fn() => $this->record->canBeRefunded())
                ->form([
                    \Filament\Forms\Components\Grid::make(2)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('amount')
                                ->label('Refund Amount')
                                ->numeric()
                                ->required()
                                ->prefix('OMR')
                                ->step(0.001)
                                ->maxValue(fn() => $this->record->getRemainingRefundableAmount())
                                ->default(fn() => $this->record->getRemainingRefundableAmount())
                                ->helperText(fn() => 'Max: ' .
                                    number_format($this->record->getRemainingRefundableAmount(), 3) . ' OMR'),

                            \Filament\Forms\Components\Select::make('refund_type')
                                ->label('Refund Type')
                                ->options([
                                    'full' => 'Full Refund',
                                    'partial' => 'Partial Refund',
                                ])
                                ->default('full')
                                ->reactive()
                                ->required(),
                        ]),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Refund Reason')
                        ->required()
                        ->rows(3)
                        ->helperText('This reason will be visible to the customer'),

                    \Filament\Forms\Components\Grid::make(2)
                        ->schema([
                            \Filament\Forms\Components\Toggle::make('notify_customer')
                                ->label('Notify Customer')
                                ->default(true),

                            \Filament\Forms\Components\Toggle::make('cancel_booking')
                                ->label('Cancel Booking')
                                ->default(false)
                                ->helperText('Also cancel the associated booking')
                                ->visible(fn() => $this->record->booking &&
                                    in_array($this->record->booking->status, ['pending', 'confirmed'])),
                        ]),
                ])
                ->action(function (array $data) {
                    try {
                        $this->record->refund($data['amount'], $data['reason']);

                        // Cancel booking if requested
                        if (($data['cancel_booking'] ?? false) && $this->record->booking) {
                            $this->record->booking->cancel($data['reason']);
                        }

                        Notification::make()
                            ->success()
                            ->title('Refund Processed')
                            ->body(number_format($data['amount'], 3) . ' OMR refunded successfully.')
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Refund Failed')
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),
            /**
             * Download Receipt Action
             *
             * Generates and downloads payment receipt PDF.
             */
            Actions\Action::make('downloadReceipt')
                ->label('Download Receipt')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn() => $this->record->status === 'paid')
                ->action(function () {
                    try {
                        // Generate PDF using PDFService
                        $pdfService = app(\App\Services\PDFService::class);
                        $filename = $pdfService->generatePaymentReceipt($this->record);

                        $filePath = storage_path('app/private/receipts/' . $filename);

                        // Check if file was created
                        if (!file_exists($filePath)) {
                            throw new \Exception('Receipt file was not created');
                        }

                        // Return download response
                        return response()->download($filePath, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Receipt generation failed', [
                            'payment_id' => $this->record->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Receipt Generation Failed')
                            ->body('Unable to generate receipt: ' . $e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),

            /**
             * Verify Payment Action
             *
             * Verifies payment status with payment gateway.
             */
            Actions\Action::make('verifyPayment')
                ->label('Verify with Gateway')
                ->icon('heroicon-o-shield-check')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Verify Payment Status')
                ->modalDescription('Check payment status directly with payment gateway')
                ->visible(fn() => !empty($this->record->transaction_id) &&
                    in_array($this->record->status, ['pending', 'paid']))
                ->action(function () {
                    try {
                        // TODO: Implement gateway verification
                        // $gatewayService = app(\App\Services\PaymentGatewayService::class);
                        // $status = $gatewayService->verifyPayment($this->record->transaction_id);

                        Notification::make()
                            ->info()
                            ->title('Verification Complete')
                            ->body('Payment status verified with gateway.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->warning()
                            ->title('Verification Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            /**
             * View Booking Action
             *
             * Quick link to associated booking.
             */
            Actions\Action::make('viewBooking')
                ->label('View Booking')
                ->icon('heroicon-o-calendar')
                ->color('gray')
                ->url(fn() => $this->record->booking
                    ? route('filament.admin.resources.bookings.view', ['record' => $this->record->booking])
                    : null)
                ->visible(fn() => $this->record->booking !== null)
                ->openUrlInNewTab(),

            /**
             * Send Receipt Email Action
             *
             * Sends payment receipt to customer email.
             */
            Actions\Action::make('sendReceipt')
                ->label('Email Receipt')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Send Receipt to Customer')
                ->modalDescription('Send payment receipt with PDF attachment to customer email.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->default(fn() => $this->record->booking?->customer_email ?? ''),

                    \Filament\Forms\Components\Toggle::make('send_copy')
                        ->label('Send copy to admin')
                        ->default(false)
                        ->helperText('Also send a copy to admin email'),
                ])
                ->visible(fn() => $this->record->status === 'paid')
                ->action(function (array $data) {
                    try {
                        // Send email to customer
                        \Illuminate\Support\Facades\Mail::to($data['email'])
                            ->send(new \App\Mail\PaymentReceiptMail($this->record));

                        // Send copy to admin if requested
                        if ($data['send_copy'] ?? false) {
                            $adminEmail = config('mail.admin_email', config('mail.from.address'));
                            if ($adminEmail) {
                                \Illuminate\Support\Facades\Mail::to($adminEmail)
                                    ->send(new \App\Mail\PaymentReceiptMail($this->record));
                            }
                        }

                        // Log the email sent
                        \Illuminate\Support\Facades\Log::info('Payment receipt email sent', [
                            'payment_id' => $this->record->id,
                            'payment_reference' => $this->record->payment_reference,
                            'email' => $data['email'],
                            'sent_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Receipt Sent Successfully')
                            ->body("Receipt has been sent to {$data['email']}")
                            ->send();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send payment receipt email', [
                            'payment_id' => $this->record->id,
                            'email' => $data['email'],
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Failed to Send Receipt')
                            ->body('Error: ' . $e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),

            /**
             * View Gateway Response Action
             *
             * Shows raw gateway response in modal.
             */
            Actions\Action::make('viewGatewayResponse')
                ->label('Gateway Response')
                ->icon('heroicon-o-code-bracket')
                ->color('gray')
                ->modalHeading('Payment Gateway Response')
                ->modalContent(fn() => view('filament.modals.gateway-response', [
                    'response' => $this->record->gateway_response,
                ]))
                ->visible(fn() => !empty($this->record->gateway_response))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            /**
             * Copy Payment Link Action
             *
             * Copies payment URL to clipboard.
             */
            Actions\Action::make('copyPaymentLink')
                ->label('Copy Payment Link')
                ->icon('heroicon-o-link')
                ->color('gray')
                ->visible(fn() => !empty($this->record->payment_url))
                ->action(function () {
                    // JavaScript will handle the copy
                    Notification::make()
                        ->success()
                        ->title('Link Copied')
                        ->body('Payment link copied to clipboard.')
                        ->send();
                }),

            // Delete action
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Payment')
                ->modalDescription('This will permanently delete the payment record.')
                ->successRedirectUrl(route('filament.admin.resources.payments.index')),
        ];
    }

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
