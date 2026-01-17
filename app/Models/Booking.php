<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SendBookingNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\HasGuestBooking;


/**
 * Booking Model
 *
 * Represents a hall booking in the Majalis platform.
 *
 * @property int $id
 * @property string $booking_number
 * @property int $hall_id
 * @property int $user_id
 * @property \Carbon\Carbon $booking_date
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
 * @property string $payment_type
 * @property float|null $advance_amount
 * @property float|null $balance_due
 * @property \Carbon\Carbon|null $balance_paid_at
 * @property string|null $balance_payment_method
 * @property string|null $balance_payment_reference
 * @property \Carbon\Carbon|null $cancelled_at
 * @property string|null $cancellation_reason
 * @property float|null $refund_amount
 * @property \Carbon\Carbon|null $confirmed_at
 * @property \Carbon\Carbon|null $completed_at
 * @property string|null $invoice_path
 * @property string|null $admin_notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Hall $hall
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<Payment> $payments
 * @property-read \Illuminate\Database\Eloquent\Collection<BookingExtraService> $extraServices
 */
class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasGuestBooking;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

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
        'payment_type',
        'advance_amount',
        'balance_due',
        'balance_paid_at',
        'balance_payment_method',
        'balance_payment_reference',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'confirmed_at',
        'completed_at',
        'invoice_path',
        'admin_notes',

        // Guest booking fields
        'is_guest_booking',
        'guest_token',
        'guest_token_expires_at',
        'account_created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'event_details' => 'array',
        'hall_price' => 'decimal:2',
        'services_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'owner_payout' => 'decimal:2',
        'advance_amount' => 'decimal:3',
        'balance_due' => 'decimal:3',
        'refund_amount' => 'decimal:2',
        'balance_paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',

        // Guest booking casts
        'is_guest_booking' => 'boolean',
        'guest_token_expires_at' => 'datetime',
        'account_created_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate booking number on creation
        static::creating(function (Booking $booking): void {
            if (empty($booking->booking_number)) {
                $booking->booking_number = self::generateBookingNumber();
            }
        });
    }

    /**
     * Generate a unique booking number.
     *
     * @return string
     */
    public static function generateBookingNumber(): string
    {
        $year = date('Y');
        $lastBooking = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastBooking
            ? ((int) substr($lastBooking->booking_number, -5)) + 1
            : 1;

        return sprintf('BK-%s-%05d', $year, $sequence);
    }

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Get the hall associated with this booking.
     *
     * @return BelongsTo<Hall, Booking>
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Get the user who made this booking.
     *
     * @return BelongsTo<User, Booking>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all payments for this booking.
     *
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasMany
    {
        return $this->payments();
    }

    /**
     * Get the extra services for this booking.
     *
     * @return HasMany<BookingExtraService>
     */
    public function extraServices(): HasMany
    {
        return $this->hasMany(BookingExtraService::class);
    }

    /**
     * Get the notifications for this booking.
     *
     * @return HasMany<BookingNotification>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(SendBookingNotification::class);
    }

    /**
     * Get the review for this booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Review>
     */
    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    // =========================================================
    // ACCESSORS & MUTATORS
    // =========================================================

    /**
     * Check if the booking is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the booking is confirmed.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if the booking is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the booking is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if payment is complete.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the booking is upcoming (future date).
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->booking_date->isFuture() || $this->booking_date->isToday();
    }

    /**
     * Check if the booking is past.
     *
     * @return bool
     */
    public function isPast(): bool
    {
        return $this->booking_date->isPast() && !$this->booking_date->isToday();
    }

    public function getDaysUntilBooking(): int
    {
        //return now()->diffInDays($this->booking_date, false);
        return (int) now()->diffInDays($this->booking_date, false);
    }

    /**
     * Check if balance payment is due (for advance payment bookings).
     *
     * @return bool
     */
    public function hasBalanceDue(): bool
    {
        return $this->payment_type === 'advance'
            && (float) ($this->balance_due ?? 0) > 0
            && $this->balance_paid_at === null;
    }

    /**
     * Get the formatted time slot label.
     *
     * @return string
     */
    public function getTimeSlotLabelAttribute(): string
    {
        return match ($this->time_slot) {
            'morning' => __('Morning'),
            'afternoon' => __('Afternoon'),
            'evening' => __('Evening'),
            'full_day' => __('Full Day'),
            default => ucfirst(str_replace('_', ' ', $this->time_slot)),
        };
    }

    /**
     * Get the formatted status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('Pending'),
            'confirmed' => __('Confirmed'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the status color for UI.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Scope to filter bookings by status.
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
     * Scope to filter upcoming bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope to filter past bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString());
    }

    /**
     * Scope to filter bookings for a specific hall.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $hallId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    /**
     * Scope to filter bookings for halls owned by a specific owner.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOwner($query, int $ownerId)
    {
        return $query->whereHas('hall', fn($q) => $q->where('owner_id', $ownerId));
    }

    /**
     * Scope to filter bookings with balance due.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithBalanceDue($query)
    {
        return $query->where('payment_type', 'advance')
            ->where('balance_due', '>', 0)
            ->whereNull('balance_paid_at');
    }

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


    // Action Methods
    public function confirm(): void
    {
        $this->update([
            'status' => BookingStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the booking with a reason.
     *
     * Transitions booking status to 'cancelled'.
     * Records the cancellation reason and timestamp.
     * Calculates refund amount based on cancellation policy if applicable.
     *
     * @param string|null $reason The reason for cancellation
     * @return void
     */
    public function cancel(?string $reason = null): void
    {
        // Calculate refund amount if booking was paid
        $refundAmount = null;

        if ($this->isPaid()) {
            // Load hall relationship if not already loaded for cancellation policy
            if (!$this->relationLoaded('hall')) {
                $this->load('hall');
            }

            // Calculate refund based on hall's cancellation fee percentage
            // Default to full refund if no cancellation policy exists
            $cancellationFeePercentage = (float) ($this->hall?->cancellation_fee_percentage ?? 0);
            $totalAmount = (float) ($this->total_amount ?? 0);

            $cancellationFee = ($totalAmount * $cancellationFeePercentage) / 100;
            $refundAmount = $totalAmount - $cancellationFee;
        }

        $this->update([
            'status' => BookingStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'refund_amount' => $refundAmount,
        ]);
    }

    /**
     * Complete the booking.
     *
     * Transitions booking status from 'confirmed' to 'completed'.
     * Sets the completed_at timestamp.
     * Should only be called after the event date has passed.
     *
     * @return void
     */
    public function complete(): void
    {
        $this->update([
            'status' => BookingStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
