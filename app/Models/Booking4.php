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
class Booking4 extends Model
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
     * Ensures hall relationship is loaded and handles null cases safely.
     *
     * @return void
     */
    public function calculateAdvancePayment(): void
    {
        // Load hall relationship if not already loaded
        if (!$this->relationLoaded('hall')) {
            $this->load('hall');
        }

        // Safety check: if no hall or hall doesn't require advance
        if (!$this->hall || !method_exists($this->hall, 'requiresAdvancePayment') || !$this->hall->requiresAdvancePayment()) {
            $this->payment_type = 'full';
            $this->advance_amount = null;
            $this->balance_due = null;
            return;
        }

        // Calculate advance based on total (hall + services)
        // Cast to float immediately to handle string values from database (strict types compatibility)
        $totalAmount = (float) ($this->subtotal ?? $this->total_amount ?? 0);

        // Set payment type to advance
        $this->payment_type = 'advance';

        // Calculate advance and balance using LOCAL variables (avoids Eloquent cast string conversion cycle)
        // This prevents TypeError when strict_types=1 is enabled
        $advanceAmount = $this->hall->calculateAdvanceAmount($totalAmount);
        $balanceDue = $this->hall->calculateBalanceDue($totalAmount, $advanceAmount);

        // Assign to model properties at the end (single round-trip through cast system)
        $this->advance_amount = $advanceAmount;
        $this->balance_due = $balanceDue;
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

    // ==================== STATUS TRANSITION METHODS ====================

    /**
     * Confirm the booking.
     *
     * Transitions booking status from 'pending' to 'confirmed'.
     * Sets the confirmed_at timestamp.
     *
     * @return bool Success
     */
    public function confirm(): bool
    {
        // Only confirm if currently pending
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'confirmed';
        $this->confirmed_at = now();

        return $this->save();
    }

    /**
     * Cancel the booking.
     *
     * Marks the booking as cancelled with a reason.
     * Sets the cancelled_at timestamp.
     *
     * @param string $reason The reason for cancellation
     * @param float|null $refundAmount Optional refund amount
     * @return bool Success
     */
    public function cancel(string $reason, ?float $refundAmount = null): bool
    {
        // Can only cancel pending or confirmed bookings
        if (!in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }

        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;

        if ($refundAmount !== null) {
            $this->refund_amount = $refundAmount;
        }

        return $this->save();
    }

    /**
     * Complete the booking.
     *
     * Marks the booking as completed after the event has occurred.
     * Sets the completed_at timestamp.
     *
     * @return bool Success
     */
    public function complete(): bool
    {
        // Only complete if currently confirmed
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Optional: Check if booking date has passed
        if ($this->booking_date->isFuture()) {
            return false;
        }

        $this->status = 'completed';
        $this->completed_at = now();

        return $this->save();
    }

    /**
     * Check if booking can be confirmed.
     *
     * @return bool
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking can be cancelled.
     *
     * @return bool
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking can be completed.
     *
     * @return bool
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'confirmed' && $this->booking_date->isPast();
    }

    /**
     * Alias for user relationship to maintain consistency
     * with customer terminology in the UI
     */
    public function customer(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Get customer display name (registered user or guest)
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->customer_name ?? 'Guest';
    }

    /**
     * Check if booking is from a registered user
     */
    public function isRegisteredCustomer(): bool
    {
        return $this->user_id !== null;
    }
}
