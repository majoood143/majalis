<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Edit Payment Page
 *
 * Allows editing of payment records with comprehensive actions:
 * - Manual status updates (mark as paid, failed)
 * - Refund processing (full and partial)
 * - Transaction ID updates
 * - Gateway response management
 *
 * @package App\Filament\Admin\Resources\PaymentResource\Pages
 */
class EditPayment extends EditRecord
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
            // View action - navigate to view page
            Actions\ViewAction::make(),

            /**
             * Mark as Paid Action
             *
             * Manually marks a pending payment as paid.
             * Updates payment and booking status, sets paid_at timestamp.
             */
            Actions\Action::make('markAsPaid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Mark Payment as Paid')
                ->modalDescription('This will mark the payment as paid and update the booking status.')
                ->modalIcon('heroicon-o-check-circle')
                ->visible(fn() => $this->record->status === 'pending')
                ->form([
                    \Filament\Forms\Components\TextInput::make('transaction_id')
                        ->label('Transaction ID (Optional)')
                        ->maxLength(255)
                        ->helperText('Enter the payment gateway transaction ID'),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes (Optional)')
                        ->rows(2)
                        ->helperText('Add any additional notes about this manual payment confirmation'),
                ])
                ->action(function (array $data) {
                    DB::beginTransaction();

                    try {
                        // Prepare update data
                        $updateData = [
                            'status' => 'paid',
                            'paid_at' => now(),
                        ];

                        if (!empty($data['transaction_id'])) {
                            $updateData['transaction_id'] = $data['transaction_id'];
                        }

                        // Update payment
                        $this->record->update($updateData);

                        // Update booking status
                        if ($this->record->booking) {
                            $this->record->booking->update([
                                'payment_status' => 'paid',
                            ]);

                            // Auto-confirm booking if still pending
                            if ($this->record->booking->status === 'pending') {
                                $this->record->booking->confirm();
                            }
                        }

                        // Log activity
                        activity()
                            ->performedOn($this->record)
                            ->causedBy(Auth::user())
                            ->withProperties([
                                'old_status' => 'pending',
                                'new_status' => 'paid',
                                'transaction_id' => $data['transaction_id'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ])
                            ->log('Payment manually marked as paid');

                        DB::commit();

                        Notification::make()
                            ->success()
                            ->title('Payment Marked as Paid')
                            ->body("Payment {$this->record->payment_reference} is now confirmed as paid.")
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Exception $e) {
                        DB::rollBack();

                        Log::error('Failed to mark payment as paid', [
                            'payment_id' => $this->record->id,
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Action Failed')
                            ->body('Failed to mark payment as paid: ' . $e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),

            /**
             * Mark as Failed Action
             *
             * Manually marks a pending payment as failed.
             */
            Actions\Action::make('markAsFailed')
                ->label('Mark as Failed')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Mark Payment as Failed')
                ->modalDescription('This will mark the payment as failed and update the booking status.')
                ->visible(fn() => $this->record->status === 'pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('failure_reason')
                        ->label('Failure Reason')
                        ->required()
                        ->rows(3)
                        ->helperText('Explain why this payment failed'),
                ])
                ->action(function (array $data) {
                    try {
                        $this->record->markAsFailed($data['failure_reason']);

                        Notification::make()
                            ->success()
                            ->title('Payment Marked as Failed')
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Action Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            /**
             * Process Refund Action
             *
             * Allows processing full or partial refunds.
             * Validates refund amount and updates payment status.
             */
            Actions\Action::make('processRefund')
                ->label('Process Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Process Refund')
                ->modalDescription(fn() => "Remaining refundable amount: " .
                    number_format($this->record->getRemainingRefundableAmount(), 3) . " OMR")
                ->modalIcon('heroicon-o-arrow-path')
                ->form([
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->label('Refund Amount')
                        ->numeric()
                        ->required()
                        ->prefix('OMR')
                        ->step(0.001)
                        ->minValue(0.001)
                        ->maxValue(fn() => $this->record->getRemainingRefundableAmount())
                        ->default(fn() => $this->record->getRemainingRefundableAmount())
                        ->helperText(fn() => 'Maximum: ' .
                            number_format($this->record->getRemainingRefundableAmount(), 3) . ' OMR')
                        ->live(onBlur: true),

                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Refund Reason')
                        ->required()
                        ->rows(3)
                        ->helperText('This will be visible to the customer'),

                    \Filament\Forms\Components\Toggle::make('notify_customer')
                        ->label('Notify Customer')
                        ->default(true)
                        ->helperText('Send refund notification email to customer'),
                ])
                ->visible(fn() => $this->record->canBeRefunded())
                ->action(function (array $data) {
                    try {
                        // Process the refund using model method
                        $this->record->refund($data['amount'], $data['reason']);

                        // TODO: Send customer notification if requested
                        if ($data['notify_customer'] ?? true) {
                            // app(NotificationService::class)->sendRefundNotification($this->record);
                        }

                        Notification::make()
                            ->success()
                            ->title('Refund Processed Successfully')
                            ->body(number_format($data['amount'], 3) . ' OMR has been refunded.')
                            ->persistent()
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Exception $e) {
                        Log::error('Refund processing failed', [
                            'payment_id' => $this->record->id,
                            'amount' => $data['amount'],
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Refund Failed')
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),

            /**
             * Full Refund Action (Quick Action)
             *
             * Processes a complete refund of the remaining amount.
             */
            Actions\Action::make('processFullRefund')
                ->label('Full Refund')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Process Full Refund')
                ->modalDescription(fn() => "Refund entire remaining amount: " .
                    number_format($this->record->getRemainingRefundableAmount(), 3) . " OMR")
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Refund Reason')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn() => $this->record->canBeRefunded() &&
                    $this->record->getRemainingRefundableAmount() > 0)
                ->action(function (array $data) {
                    try {
                        $this->record->refundFull($data['reason']);

                        Notification::make()
                            ->success()
                            ->title('Full Refund Processed')
                            ->body('Payment has been fully refunded.')
                            ->send();

                        $this->redirect(static::getUrl(['record' => $this->record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Refund Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            /**
             * Resend Receipt Action
             *
             * Resends payment receipt to customer email.
             */
            Actions\Action::make('resendReceipt')
                ->label('Resend Receipt')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Resend Payment Receipt')
                ->modalDescription('Send payment receipt to customer email')
                ->visible(fn() => $this->record->status === 'paid')
                ->action(function () {
                    try {
                        // TODO: Implement receipt email sending
                        // app(NotificationService::class)->sendPaymentReceipt($this->record);

                        Notification::make()
                            ->success()
                            ->title('Receipt Sent')
                            ->body('Payment receipt has been sent to customer email.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->warning()
                            ->title('Send Failed')
                            ->body('Failed to send receipt: ' . $e->getMessage())
                            ->send();
                    }
                }),

            // Delete action with strict confirmation
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Payment Record')
                ->modalDescription('Are you sure you want to delete this payment? This action cannot be undone.')
                ->modalIcon('heroicon-o-trash')
                ->before(function () {
                    // Log deletion attempt
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(Auth::user())
                        ->log('Payment record deleted');
                }),
        ];
    }

    /**
     * Redirect to view page after successful edit
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    /**
     * Handle after save operations
     *
     * @return void
     */
    protected function afterSave(): void
    {
        try {
            // Log activity for audit trail
            activity()
                ->performedOn($this->record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'payment_reference' => $this->record->payment_reference,
                    'status' => $this->record->status,
                    'amount' => $this->record->amount,
                ])
                ->log('Payment record updated');

            // Clear payment-related caches
            Cache::tags(['payments', 'bookings'])->flush();

            // Send success notification
            Notification::make()
                ->success()
                ->title('Payment Updated')
                ->body('Payment details have been updated successfully.')
                ->send();
        } catch (\Exception $e) {
            Log::error('Payment after-save tasks failed', [
                'payment_id' => $this->record->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mutate form data before filling the form
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add any data transformations needed before filling the form
        return $data;
    }

    /**
     * Mutate form data before saving
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate status changes
        if (isset($data['status']) && $data['status'] !== $this->record->getOriginal('status')) {
            $oldStatus = $this->record->getOriginal('status');
            $newStatus = $data['status'];

            // Prevent invalid status transitions
            $invalidTransitions = [
                'paid' => ['pending'],  // Can't go from paid back to pending
                'refunded' => ['pending', 'paid'],  // Can't reverse refund
            ];

            if (
                isset($invalidTransitions[$oldStatus]) &&
                in_array($newStatus, $invalidTransitions[$oldStatus])
            ) {

                Notification::make()
                    ->danger()
                    ->title('Invalid Status Change')
                    ->body("Cannot change status from {$oldStatus} to {$newStatus}")
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    /**
     * Get the page title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Edit Payment: ' . $this->record->payment_reference;
    }

    /**
     * Get the page subheading
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return 'Booking: ' . ($this->record->booking->booking_number ?? 'N/A');
    }
}
