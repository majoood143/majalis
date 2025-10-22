<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

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

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', PaymentStatus::PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', PaymentStatus::FAILED);
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', [
            PaymentStatus::REFUNDED,
            PaymentStatus::PARTIALLY_REFUNDED
        ]);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = self::generatePaymentReference();
            }
        });
    }

    // Status Methods
    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function isRefunded(): bool
    {
        return in_array($this->status, [
            PaymentStatus::REFUNDED,
            PaymentStatus::PARTIALLY_REFUNDED
        ]);
    }

    // Action Methods
    public function markAsPaid(string $transactionId = null, array $response = []): void
    {
        $this->update([
            'status' => PaymentStatus::PAID,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'gateway_response' => $response,
            'paid_at' => now(),
        ]);

        // Update booking status
        $this->booking->update([
            'payment_status' => PaymentStatus::PAID,
        ]);
    }

    public function markAsFailed(string $reason = null, array $response = []): void
    {
        $this->update([
            'status' => PaymentStatus::FAILED,
            'failure_reason' => $reason,
            'gateway_response' => $response,
            'failed_at' => now(),
        ]);

        // Update booking status
        $this->booking->update([
            'payment_status' => PaymentStatus::FAILED,
        ]);
    }

    public function refund(float $amount = null, string $reason = null): void
    {
        $refundAmount = $amount ?? $this->amount;
        $isPartial = $refundAmount < $this->amount;

        $this->update([
            'status' => $isPartial ? PaymentStatus::PARTIALLY_REFUNDED : PaymentStatus::REFUNDED,
            'refund_amount' => $refundAmount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);

        // Update booking
        $this->booking->update([
            'payment_status' => $isPartial ? PaymentStatus::PARTIALLY_REFUNDED : PaymentStatus::REFUNDED,
            'refund_amount' => $refundAmount,
        ]);
    }

    // Helper Methods
    public static function generatePaymentReference(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 3) . ' ' . $this->currency;
    }

    public function canBeRefunded(): bool
    {
        return $this->isPaid() && !$this->isRefunded();
    }

    public function getRemainingRefundableAmount(): float
    {
        if ($this->isRefunded()) {
            return $this->amount - ($this->refund_amount ?? 0);
        }

        return $this->amount;
    }
}
