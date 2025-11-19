<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model
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
     * Payment statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if payment is successful
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if payment has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(string $transactionId, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason, ?array $gatewayResponse = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
            'gateway_response' => $gatewayResponse,
            'failed_at' => now(),
        ]);
    }

    /**
     * Scope a query to only include paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }
}
