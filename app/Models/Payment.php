<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Payment Model
 *
 * Manages payment transactions for bookings including:
 * - Payment tracking and status management
 * - Refund processing (full and partial)
 * - Payment gateway integration
 * - Transaction auditing
 * - Fraud detection (IP and user agent tracking)
 *
 * Payment Status Flow:
 * pending -> paid -> completed
 *        -> failed
 *        -> refunded (from paid)
 *        -> partially_refunded (from paid)
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $booking_id
 * @property string $payment_reference
 * @property string|null $transaction_id
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property string|null $payment_method
 * @property array|null $gateway_response
 * @property string|null $payment_url
 * @property string|null $invoice_id
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $failed_at
 * @property \Carbon\Carbon|null $refunded_at
 * @property float|null $refund_amount
 * @property string|null $refund_reason
 * @property string|null $failure_reason
 * @property string|null $customer_ip
 * @property string|null $user_agent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Booking $booking
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'payment_reference',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'gateway_response',
        'payment_url',
        'invoice_id',
        'paid_at',
        'failed_at',
        'refunded_at',
        'refund_amount',
        'refund_reason',
        'failure_reason',
        'customer_ip',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:3',
        'refund_amount' => 'decimal:3',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'customer_ip',
        'user_agent',
    ];

    /**
     * Payment status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    /**
     * Boot method to set up model event listeners
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate payment reference before creating
        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = self::generatePaymentReference();
            }

            // Set default currency if not provided
            if (empty($payment->currency)) {
                $payment->currency = 'OMR';
            }

            // Set default status if not provided
            if (empty($payment->status)) {
                $payment->status = self::STATUS_PENDING;
            }

            // Capture customer IP and user agent if not set
            if (empty($payment->customer_ip) && request()) {
                $payment->customer_ip = request()->ip();
            }

            if (empty($payment->user_agent) && request()) {
                $payment->user_agent = request()->userAgent();
            }
        });
    }

    /**
     * Generate a unique payment reference number
     *
     * Format: PAY-YYYYMMDD-XXXXXX
     * Example: PAY-20241119-AB12CD
     *
     * @return string
     */
    public static function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('payment_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Relationship: Payment belongs to a Booking
     *
     * @return BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if the payment can be refunded
     *
     * A payment can be refunded if:
     * - Status is 'paid' (full refund available)
     * - Status is 'partially_refunded' (partial refund available)
     * - There is remaining amount to refund
     *
     * @return bool
     */
    /**
     * Check if payment can be refunded
     *
     * @return bool
     */
    public function canBeRefunded(): bool
    {
        try {
            // Only paid payments can be refunded
            if ($this->status !== self::STATUS_PAID) {
                return false;
            }

            // Check if already fully refunded
            if ($this->status === self::STATUS_REFUNDED) {
                return false;
            }

            // Check if refundable amount remains
            $remaining = $this->getRemainingRefundableAmount();

            return $remaining > 0;
        } catch (\Exception $e) {
            Log::error('Error checking if payment can be refunded', [
                'payment_id' => $this->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }




    /**
     * Check if the payment has been refunded (fully or partially)
     *
     * @return bool
     */
    public function isRefunded(): bool
    {
        return in_array($this->status, [self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    /**
     * Check if the payment has been fully refunded
     *
     * @return bool
     */
    public function isFullyRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Check if the payment has been partially refunded
     *
     * @return bool
     */
    public function isPartiallyRefunded(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_REFUNDED;
    }

    /**
     * Check if the payment is paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if the payment is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the payment has failed
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get the remaining refundable amount
     *
     * Calculates how much can still be refunded from the original payment amount.
     *
     * @return float
     */
    /**
     * Get remaining refundable amount
     *
     * @return float
     */
    public function getRemainingRefundableAmount(): float
    {
        try {
            $refundedAmount = (float) ($this->refund_amount ?? 0);
            $originalAmount = (float) $this->amount;

            return max(0, $originalAmount - $refundedAmount);
        } catch (\Exception $e) {
            Log::error('Error calculating remaining refundable amount', [
                'payment_id' => $this->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Process a refund for this payment
     *
     * Handles both full and partial refunds with proper validation,
     * status updates, and transaction safety.
     *
     * @param float $amount The amount to refund
     * @param string $reason The reason for the refund
     * @return bool Returns true if refund was successful
     * @throws \Exception If refund validation fails
     */
    public function refund(float $amount, string $reason): bool
    {
        // Validate that refund is allowed
        if (!$this->canBeRefunded()) {
            throw new \Exception('This payment cannot be refunded. Status: ' . $this->status);
        }

        // Validate refund amount
        if ($amount <= 0) {
            throw new \Exception('Refund amount must be greater than zero.');
        }

        // Get remaining refundable amount
        $remainingAmount = $this->getRemainingRefundableAmount();

        if ($amount > $remainingAmount) {
            throw new \Exception(
                "Refund amount ({$amount} OMR) exceeds remaining refundable amount ({$remainingAmount} OMR)."
            );
        }

        // Use database transaction for data integrity
        DB::beginTransaction();

        try {
            // Calculate new total refund amount
            $newRefundAmount = ($this->refund_amount ?? 0) + $amount;

            // Determine new status
            $newStatus = ($newRefundAmount >= $this->amount)
                ? self::STATUS_REFUNDED
                : self::STATUS_PARTIALLY_REFUNDED;

            // Update payment record
            $this->update([
                'refund_amount' => $newRefundAmount,
                'refund_reason' => $reason,
                'status' => $newStatus,
                'refunded_at' => now(),
            ]);

            // Update related booking payment status if fully refunded
            if ($newStatus === self::STATUS_REFUNDED && $this->booking) {
                $this->booking->update([
                    'payment_status' => 'refunded',
                ]);
            }

            // Log the refund for audit trail
            Log::info('Payment refund processed', [
                'payment_id' => $this->id,
                'payment_reference' => $this->payment_reference,
                'booking_id' => $this->booking_id,
                'refund_amount' => $amount,
                'total_refunded' => $newRefundAmount,
                'original_amount' => $this->amount,
                'reason' => $reason,
                'new_status' => $newStatus,
                'customer_ip' => $this->customer_ip,
                'timestamp' => now(),
            ]);

            // TODO: Integrate with Thawani payment gateway to process actual refund
            // $this->processGatewayRefund($amount);

            DB::commit();

            // Send notification to customer about refund
            // TODO: Implement notification service
            // app(NotificationService::class)->sendRefundNotification($this);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment refund failed', [
                'payment_id' => $this->id,
                'payment_reference' => $this->payment_reference,
                'error' => $e->getMessage(),
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Refund processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a full refund (convenience method)
     *
     * Refunds the entire remaining refundable amount.
     *
     * @param string $reason The reason for the refund
     * @return bool Returns true if refund was successful
     * @throws \Exception If refund validation fails
     */
    public function refundFull(string $reason): bool
    {
        $remainingAmount = $this->getRemainingRefundableAmount();

        return $this->refund($remainingAmount, $reason);
    }

    /**
     * Mark payment as paid
     *
     * Updates status to paid and sets paid_at timestamp.
     * Updates related booking payment status.
     *
     * @param string|null $transactionId Optional transaction ID from gateway
     * @param array|null $gatewayResponse Optional gateway response data
     * @param string|null $invoiceId Optional invoice ID
     * @return bool
     */
    // public function markAsPaid(
    //     ?string $transactionId = null,
    //     ?array $gatewayResponse = null,
    //     ?string $invoiceId = null
    // ): bool {
    //     DB::beginTransaction();

    //     try {
    //         $updateData = [
    //             'status' => self::STATUS_PAID,
    //             'paid_at' => now(),
    //         ];

    //         if ($transactionId) {
    //             $updateData['transaction_id'] = $transactionId;
    //         }

    //         if ($gatewayResponse) {
    //             $updateData['gateway_response'] = $gatewayResponse;
    //         }

    //         if ($invoiceId) {
    //             $updateData['invoice_id'] = $invoiceId;
    //         }

    //         $this->update($updateData);

    //         // Update booking payment status
    //         if ($this->booking) {
    //             $this->booking->update([
    //                 'payment_status' => 'paid',
    //             ]);

    //             // Optionally auto-confirm the booking
    //             // $this->booking->confirm();
    //         }

    //         Log::info('Payment marked as paid', [
    //             'payment_id' => $this->id,
    //             'payment_reference' => $this->payment_reference,
    //             'booking_id' => $this->booking_id,
    //             'amount' => $this->amount,
    //             'transaction_id' => $transactionId,
    //             'invoice_id' => $invoiceId,
    //         ]);

    //         DB::commit();

    //         return true;
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Failed to mark payment as paid', [
    //             'payment_id' => $this->id,
    //             'error' => $e->getMessage(),
    //         ]);

    //         return false;
    //     }
    // }
    /**
     * Mark payment as paid
     *
     * Updates status to paid and sets paid_at timestamp.
     * Updates related booking payment status.
     *
     * @param string|null $transactionId Optional transaction ID from gateway
     * @param array|null $gatewayResponse Optional gateway response data
     * @param string|null $invoiceId Optional invoice ID
     * @return bool
     */
    public function markAsPaid(
        ?string $transactionId = null,
        ?array $gatewayResponse = null,
        ?string $invoiceId = null
    ): bool {
        DB::beginTransaction();

        try {
            $updateData = [
                'status' => self::STATUS_PAID,
                'paid_at' => now(),
            ];

            if ($transactionId) {
                $updateData['transaction_id'] = $transactionId;
            }

            if ($gatewayResponse) {
                // Merge with existing gateway_response
                $existingResponse = $this->gateway_response ?? [];
                $updateData['gateway_response'] = array_merge($existingResponse, $gatewayResponse);
            }

            if ($invoiceId) {
                $updateData['invoice_id'] = $invoiceId;
            }

            $this->update($updateData);

            // Update booking payment status
            if ($this->booking) {
                $this->booking->update([
                    'payment_status' => 'paid',
                ]);

                // Optionally auto-confirm the booking
                if ($this->booking->status === 'pending') {
                    $this->booking->update([
                        'status' => 'confirmed',
                    ]);
                }
            }

            Log::info('Payment marked as paid', [
                'payment_id' => $this->id,
                'payment_reference' => $this->payment_reference,
                'booking_id' => $this->booking_id,
                'amount' => $this->amount,
                'transaction_id' => $transactionId,
                'invoice_id' => $invoiceId,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to mark payment as paid', [
                'payment_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark payment as failed
     *
     * Updates status to failed and records failure reason.
     * Updates related booking payment status.
     *
     * @param string $reason Failure reason
     * @param array|null $gatewayResponse Optional gateway response data
     * @return bool
     */
    public function markAsFailed(string $reason, ?array $gatewayResponse = null): bool
    {
        DB::beginTransaction();

        try {
            $updateData = [
                'status' => self::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => $reason,
            ];

            if ($gatewayResponse) {
                $updateData['gateway_response'] = $gatewayResponse;
            }

            $this->update($updateData);

            // Update booking payment status
            if ($this->booking) {
                $this->booking->update([
                    'payment_status' => 'failed',
                ]);
            }

            Log::warning('Payment marked as failed', [
                'payment_id' => $this->id,
                'payment_reference' => $this->payment_reference,
                'booking_id' => $this->booking_id,
                'reason' => $reason,
                'customer_ip' => $this->customer_ip,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to mark payment as failed', [
                'payment_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Store payment gateway URL
     *
     * Used when redirecting customer to payment gateway.
     *
     * @param string $url Payment gateway URL
     * @return bool
     */
    public function setPaymentUrl(string $url): bool
    {
        try {
            $this->update(['payment_url' => $url]);

            Log::info('Payment URL stored', [
                'payment_id' => $this->id,
                'payment_reference' => $this->payment_reference,
                'url' => $url,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store payment URL', [
                'payment_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get formatted amount with currency
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 3) . ' ' . $this->currency;
    }

    /**
     * Get formatted refund amount with currency
     *
     * @return string|null
     */
    public function getFormattedRefundAmountAttribute(): ?string
    {
        if ($this->refund_amount === null) {
            return null;
        }

        return number_format($this->refund_amount, 3) . ' ' . $this->currency;
    }

    /**
     * Get masked customer IP for display (privacy)
     *
     * Example: 192.168.1.100 becomes 192.168.***.***
     *
     * @return string|null
     */
    public function getMaskedIpAttribute(): ?string
    {
        if (!$this->customer_ip) {
            return null;
        }

        $parts = explode('.', $this->customer_ip);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.***.' . '***';
        }

        // For IPv6 or other formats
        return substr($this->customer_ip, 0, 10) . '***';
    }

    /**
     * Scope: Filter by status
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get paid payments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope: Get pending payments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get failed payments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: Get refunded payments (fully or partially)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefunded($query)
    {
        return $query->whereIn('status', [self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    /**
     * Scope: Filter by date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $from
     * @param string $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope: Filter by IP address (for fraud detection)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $ip
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIp($query, string $ip)
    {
        return $query->where('customer_ip', $ip);
    }

    /**
     * Get all available payment statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_PARTIALLY_REFUNDED => 'Partially Refunded',
        ];
    }

    /**
     * Check if payment is processing
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }





}
