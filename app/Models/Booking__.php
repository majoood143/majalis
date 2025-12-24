<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Booking Model
 *
 * Represents a hall booking in the Majalis system.
 * Supports full and partial (advance) payment tracking.
 *
 * Payment Flow:
 * 1. Customer books hall → Creates booking record
 * 2. If hall requires advance:
 *    - payment_type = 'advance'
 *    - advance_amount = calculated from hall settings
 *    - balance_due = total_amount - advance_amount
 *    - payment_status = 'partial'
 * 3. When balance paid (manually marked by admin):
 *    - balance_paid_at = timestamp
 *    - balance_payment_method = 'bank_transfer', 'cash', etc.
 *    - payment_status = 'paid'
 *
 * @property int $id
 * @property string $booking_number
 * @property int $hall_id
 * @property int $user_id
 * @property string $booking_date
 * @property string $time_slot
 * @property int $number_of_guests
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string|null $customer_notes
 * @property string|null $event_type
 * @property array|null $event_details
 * @property float $hall_price
 * @property float $services_price
 * @property float $subtotal
 * @property float $platform_fee
 * @property float $total_amount
 * @property float $commission_amount
 * @property string|null $commission_type
 * @property float|null $commission_value
 * @property float $owner_payout
 * @property string $status
 * @property string $payment_status
 * @property string $payment_type 'full' or 'advance'
 * @property float|null $advance_amount
 * @property float|null $balance_due
 * @property \DateTime|null $balance_paid_at
 * @property string|null $balance_payment_method
 * @property string|null $balance_payment_reference
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_number',
        'hall_id',
        'user_id',
        'booking_date',
        'time_slot',
        'number_of_guests',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_notes',
        'event_type',
        'event_details',
        'hall_price',
        'services_price',
        'subtotal',
        'platform_fee',
        'total_amount',
        'commission_amount',
        'commission_type',
        'commission_value',
        'owner_payout',
        'status',
        'payment_status',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'confirmed_at',
        'completed_at',
        'invoice_path',
        'admin_notes',
        'payment_id',
        // Advance Payment Tracking Fields
        'payment_type',
        'advance_amount',
        'balance_due',
        'balance_paid_at',
        'balance_payment_method',
        'balance_payment_reference',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'hall_price' => 'decimal:3',
        'services_price' => 'decimal:3',
        'subtotal' => 'decimal:3',
        'platform_fee' => 'decimal:3',
        'total_amount' => 'decimal:3',
        'commission_amount' => 'decimal:3',
        'commission_value' => 'decimal:3',
        'owner_payout' => 'decimal:3',
        'refund_amount' => 'decimal:3',
        'event_details' => 'array',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        // Advance Payment Casts
        'advance_amount' => 'decimal:3',
        'balance_due' => 'decimal:3',
        'balance_paid_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the hall that owns the booking.
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Get the user who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment record for this booking.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the extra services attached to this booking.
     */
    public function extraServices(): BelongsToMany
    {
        return $this->belongsToMany(ExtraService::class, 'booking_extra_services')
            ->withPivot(['service_name', 'unit_price', 'quantity', 'total_price'])
            ->withTimestamps();
    }

    /**
     * Get the review for this booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // ==================== ADVANCE PAYMENT METHODS ====================

    /**
     * Check if this booking uses advance payment.
     *
     * @return bool True if advance payment was used
     */
    public function isAdvancePayment(): bool
    {
        return $this->payment_type === 'advance';
    }

    /**
     * Check if this booking is fully paid.
     *
     * @return bool True if payment_status is 'paid' and no balance due
     */
    public function isFullyPaid(): bool
    {
        if ($this->payment_status !== 'paid') {
            return false;
        }

        // If it's an advance payment, check if balance is also paid
        if ($this->isAdvancePayment()) {
            return $this->balance_paid_at !== null;
        }

        return true;
    }

    /**
     * Check if balance payment is pending.
     *
     * @return bool True if advance paid but balance still due
     */
    public function isBalancePending(): bool
    {
        return $this->isAdvancePayment()
            && $this->balance_due > 0
            && $this->balance_paid_at === null;
    }

    /**
     * Mark balance as paid.
     *
     * Updates balance payment details and changes payment status to 'paid'.
     *
     * @param string $method Payment method (bank_transfer, cash, etc.)
     * @param string|null $reference Transaction reference or receipt number
     * @return bool Success
     */
    public function markBalanceAsPaid(string $method, ?string $reference = null): bool
    {
        if (!$this->isAdvancePayment()) {
            return false;
        }

        $this->balance_paid_at = now();
        $this->balance_payment_method = $method;
        $this->balance_payment_reference = $reference;
        $this->payment_status = 'paid';

        return $this->save();
    }

    /**
     * Get payment summary for display.
     *
     * @return array{total: float, advance: float|null, balance: float|null, type: string, fully_paid: bool}
     */
    public function getPaymentSummary(): array
    {
        return [
            'total' => $this->total_amount,
            'advance' => $this->advance_amount,
            'balance' => $this->balance_due,
            'type' => $this->payment_type,
            'fully_paid' => $this->isFullyPaid(),
            'balance_pending' => $this->isBalancePending(),
            'balance_paid_at' => $this->balance_paid_at,
            'balance_payment_method' => $this->balance_payment_method,
        ];
    }

    /**
     * Calculate and set advance payment details from hall settings.
     *
     * Call this method when creating a booking for a hall that requires advance.
     *
     * @return void
     */
    public function calculateAdvancePayment(): void
    {
        // Only calculate if hall requires advance
        if (!$this->hall || !$this->hall->requiresAdvancePayment()) {
            $this->payment_type = 'full';
            $this->advance_amount = null;
            $this->balance_due = null;
            return;
        }

        // Calculate advance based on total (hall + services)
        $totalAmount = $this->subtotal; // subtotal already includes hall + services

        $this->payment_type = 'advance';
        $this->advance_amount = $this->hall->calculateAdvanceAmount($totalAmount);
        $this->balance_due = $this->hall->calculateBalanceDue($totalAmount, $this->advance_amount);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get bookings with pending balance.
     */
    public function scopeWithPendingBalance($query)
    {
        return $query->where('payment_type', 'advance')
            ->where('balance_due', '>', 0)
            ->whereNull('balance_paid_at');
    }

    /**
     * Scope to get fully paid bookings.
     */
    public function scopeFullyPaid($query)
    {
        return $query->where(function ($q) {
            $q->where('payment_type', 'full')
                ->where('payment_status', 'paid');
        })->orWhere(function ($q) {
            $q->where('payment_type', 'advance')
                ->where('payment_status', 'paid')
                ->whereNotNull('balance_paid_at');
        });
    }

    /**
     * Scope to get advance payment bookings.
     */
    public function scopeAdvancePayment($query)
    {
        return $query->where('payment_type', 'advance');
    }

    /**
     * Scope to get full payment bookings.
     */
    public function scopeFullPayment($query)
    {
        return $query->where('payment_type', 'full');
    }
}
