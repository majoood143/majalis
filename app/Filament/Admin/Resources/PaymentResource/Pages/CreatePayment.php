<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Create Payment Page
 *
 * Handles manual payment record creation with comprehensive validation,
 * automatic reference generation, and booking integration.
 *
 * Features:
 * - Auto-generates unique payment references
 * - Validates payment amounts and booking availability
 * - Automatically sets timestamps based on status
 * - Integrates with booking payment status
 * - Logs activities for audit trail
 * - Captures customer IP and user agent
 *
 * @package App\Filament\Admin\Resources\PaymentResource\Pages
 */
class CreatePayment extends CreateRecord
{
    /**
     * The resource associated with this page
     *
     * @var string
     */
    protected static string $resource = PaymentResource::class;

    /**
     * Redirect to view page after successful creation
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    /**
     * Mutate form data before creating the payment record
     *
     * Performs comprehensive validation and data transformation:
     * - Generates unique payment reference
     * - Validates amount
     * - Checks booking eligibility
     * - Sets appropriate timestamps
     * - Captures request metadata
     *
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Generate unique payment reference using model method
            $data['payment_reference'] = \App\Models\Payment::generatePaymentReference();

            // Validate amount - must be positive
            if (!isset($data['amount']) || $data['amount'] <= 0) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Amount')
                    ->body('Payment amount must be greater than zero.')
                    ->persistent()
                    ->send();

                $this->halt();

                return $data;
            }

            // Validate booking exists and is eligible for payment
            if (isset($data['booking_id'])) {
                $booking = \App\Models\Booking::find($data['booking_id']);

                if (!$booking) {
                    Notification::make()
                        ->danger()
                        ->title('Invalid Booking')
                        ->body('The selected booking does not exist.')
                        ->persistent()
                        ->send();

                    $this->halt();

                    return $data;
                }

                // Check if booking already has a paid payment
                $existingPaidPayment = \App\Models\Payment::where('booking_id', $booking->id)
                    ->where('status', 'paid')
                    ->first();

                if ($existingPaidPayment) {
                    Notification::make()
                        ->warning()
                        ->title('Booking Already Paid')
                        ->body("This booking already has a paid payment (Ref: {$existingPaidPayment->payment_reference})")
                        ->persistent()
                        ->send();

                    // Don't halt, just warn - allow duplicate payments for partial payments scenario
                }

                // Validate amount matches booking if amount not manually adjusted
                if (!isset($data['amount_manually_adjusted'])) {
                    $expectedAmount = $booking->total_amount;

                    if (abs($data['amount'] - $expectedAmount) > 0.001) {
                        Notification::make()
                            ->warning()
                            ->title('Amount Mismatch')
                            ->body("Payment amount ({$data['amount']} OMR) differs from booking total ({$expectedAmount} OMR)")
                            ->persistent()
                            ->send();
                    }
                }
            }

            // Set default currency if not provided
            if (empty($data['currency'])) {
                $data['currency'] = 'OMR';
            }

            // Set default status if not provided
            if (empty($data['status'])) {
                $data['status'] = 'pending';
            }

            // Set appropriate timestamps based on status
            if ($data['status'] === 'paid' && empty($data['paid_at'])) {
                $data['paid_at'] = now();
            }

            if ($data['status'] === 'failed' && empty($data['failed_at'])) {
                $data['failed_at'] = now();
            }

            if (in_array($data['status'], ['refunded', 'partially_refunded']) && empty($data['refunded_at'])) {
                $data['refunded_at'] = now();
            }

            // Capture customer metadata if available from request
            if (empty($data['customer_ip']) && request()) {
                $data['customer_ip'] = request()->ip();
            }

            if (empty($data['user_agent']) && request()) {
                $data['user_agent'] = request()->userAgent();
            }

            // Log the creation attempt
            Log::info('Manual payment creation initiated', [
                'booking_id' => $data['booking_id'] ?? null,
                'amount' => $data['amount'],
                'status' => $data['status'],
                'created_by' => Auth::id(),
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Payment creation validation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            Notification::make()
                ->danger()
                ->title('Validation Error')
                ->body('Failed to validate payment data: ' . $e->getMessage())
                ->persistent()
                ->send();

            $this->halt();

            return $data;
        }
    }

    /**
     * Handle model after creation
     *
     * Updates related booking payment status and performs post-creation tasks.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        DB::beginTransaction();

        try {
            // Update booking payment status if payment is paid
            if ($this->record->status === 'paid' && $this->record->booking) {
                $this->record->booking->update([
                    'payment_status' => 'paid',
                ]);

                // Optionally auto-confirm the booking
                if ($this->record->booking->status === 'pending') {
                    $this->record->booking->confirm();

                    Notification::make()
                        ->success()
                        ->title('Booking Auto-Confirmed')
                        ->body("Booking {$this->record->booking->booking_number} has been automatically confirmed.")
                        ->send();
                }
            }

            // Log activity for audit trail
            activity()
                ->performedOn($this->record)
                ->causedBy(Auth::user())
                ->withProperties([
                    'payment_reference' => $this->record->payment_reference,
                    'booking_id' => $this->record->booking_id,
                    'amount' => $this->record->amount,
                    'status' => $this->record->status,
                ])
                ->log('Payment record created manually');

            // Clear payment-related caches
            Cache::tags(['payments', 'bookings'])->flush();

            // Send notification about successful creation
            Notification::make()
                ->success()
                ->title('Payment Created Successfully')
                ->body("Payment reference: {$this->record->payment_reference}")
                ->persistent()
                ->send();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment after-creation tasks failed', [
                'payment_id' => $this->record->id,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->warning()
                ->title('Partial Success')
                ->body('Payment created but some post-creation tasks failed.')
                ->send();
        }
    }

    /**
     * Customize the page title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Create Payment Record';
    }

    /**
     * Get the page subheading
     *
     * @return string|null
     */
    public function getSubheading(): ?string
    {
        return 'Manually create a payment record for a booking';
    }

    /**
     * Handle save before action
     *
     * Additional validation before save
     *
     * @return void
     */
    protected function beforeCreate(): void
    {
        // Additional pre-creation validation can be added here

        // Check for duplicate transaction IDs if provided
        if (!empty($this->data['transaction_id'])) {
            $existingPayment = \App\Models\Payment::where('transaction_id', $this->data['transaction_id'])
                ->first();

            if ($existingPayment) {
                Notification::make()
                    ->danger()
                    ->title('Duplicate Transaction ID')
                    ->body("This transaction ID already exists for payment {$existingPayment->payment_reference}")
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }
    }
}
