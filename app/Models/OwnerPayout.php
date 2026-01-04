<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * OwnerPayout Model
 *
 * Represents a payout settlement to a hall owner. Each record tracks
 * the financial details of a payout for a specific period.
 *
 * Features:
 * - Automatic payout number generation
 * - Status workflow management
 * - Financial calculations (gross, commission, net)
 * - Audit trail with timestamps
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $payout_number
 * @property int $owner_id
 * @property \Carbon\Carbon $period_start
 * @property \Carbon\Carbon $period_end
 * @property float $gross_revenue
 * @property float $commission_amount
 * @property float $commission_rate
 * @property float $net_payout
 * @property float $adjustments
 * @property int $bookings_count
 * @property PayoutStatus $status
 * @property string|null $payment_method
 * @property array|null $bank_details
 * @property string|null $transaction_reference
 * @property \Carbon\Carbon|null $processed_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon|null $failed_at
 * @property int|null $processed_by
 * @property string|null $notes
 * @property string|null $failure_reason
 * @property string|null $receipt_path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read User $owner
 * @property-read User|null $processor
 * @property-read HallOwner|null $hallOwner
 */
class OwnerPayout extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owner_payouts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payout_number',
        'owner_id',
        'period_start',
        'period_end',
        'gross_revenue',
        'commission_amount',
        'commission_rate',
        'net_payout',
        'adjustments',
        'bookings_count',
        'status',
        'payment_method',
        'bank_details',
        'transaction_reference',
        'processed_at',
        'completed_at',
        'failed_at',
        'processed_by',
        'notes',
        'failure_reason',
        'receipt_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'gross_revenue' => 'decimal:3',
        'commission_amount' => 'decimal:3',
        'commission_rate' => 'decimal:2',
        'net_payout' => 'decimal:3',
        'adjustments' => 'decimal:3',
        'bookings_count' => 'integer',
        'status' => PayoutStatus::class,
        'bank_details' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'bank_details', // Sensitive financial data
    ];

    // ==================== BOOT METHODS ====================

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Generate payout number on creation
        static::creating(function (self $payout): void {
            if (empty($payout->payout_number)) {
                $payout->payout_number = self::generatePayoutNumber();
            }

            // Calculate net payout if not set
            if ($payout->net_payout == 0 && $payout->gross_revenue > 0) {
                $payout->calculateNetPayout();
            }
        });

        // Log status changes
        static::updated(function (self $payout): void {
            if ($payout->isDirty('status')) {
                Log::info('Payout status changed', [
                    'payout_id' => $payout->id,
                    'payout_number' => $payout->payout_number,
                    'old_status' => $payout->getOriginal('status'),
                    'new_status' => $payout->status,
                ]);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the owner (user) for this payout.
     *
     * @return BelongsTo<User, OwnerPayout>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the admin who processed this payout.
     *
     * @return BelongsTo<User, OwnerPayout>
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the hall owner profile.
     *
     * @return BelongsTo<HallOwner, OwnerPayout>
     */
    public function hallOwner(): BelongsTo
    {
        return $this->belongsTo(HallOwner::class, 'owner_id', 'user_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by owner.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope to filter by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param PayoutStatus $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, PayoutStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending payouts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', PayoutStatus::PENDING);
    }

    /**
     * Scope to filter completed payouts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', PayoutStatus::COMPLETED);
    }

    /**
     * Scope to filter by period.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate): void {
            $q->whereBetween('period_start', [$startDate, $endDate])
                ->orWhereBetween('period_end', [$startDate, $endDate]);
        });
    }

    // ==================== STATUS METHODS ====================

    /**
     * Check if payout is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === PayoutStatus::PENDING;
    }

    /**
     * Check if payout is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === PayoutStatus::COMPLETED;
    }

    /**
     * Check if payout can be processed.
     *
     * @return bool
     */
    public function canProcess(): bool
    {
        return $this->status->canProcess();
    }

    /**
     * Check if payout can be cancelled.
     *
     * Delegates to the PayoutStatus enum to determine if the current
     * status allows cancellation. Only PENDING and ON_HOLD payouts
     * can be cancelled.
     *
     * @return bool True if payout can be cancelled
     */
    public function canCancel(): bool
    {
        return $this->status->canCancel();
    }

    /**
     * Mark payout as processing.
     *
     * @param int|null $processedBy Admin user ID
     * @return bool
     */
    public function markAsProcessing(?int $processedBy = null): bool
    {
        if (!$this->canProcess()) {
            return false;
        }

        return $this->update([
            'status' => PayoutStatus::PROCESSING,
            'processed_at' => now(),
            'processed_by' => $processedBy,
        ]);
    }

    /**
     * Mark payout as completed.
     *
     * @param string|null $transactionReference
     * @param string|null $paymentMethod
     * @return bool
     */
    public function markAsCompleted(
        ?string $transactionReference = null,
        ?string $paymentMethod = null
    ): bool {
        if ($this->status !== PayoutStatus::PROCESSING) {
            return false;
        }

        return $this->update([
            'status' => PayoutStatus::COMPLETED,
            'completed_at' => now(),
            'transaction_reference' => $transactionReference ?? $this->transaction_reference,
            'payment_method' => $paymentMethod ?? $this->payment_method,
        ]);
    }

    /**
     * Mark payout as failed.
     *
     * @param string $reason Failure reason
     * @return bool
     */
    public function markAsFailed(string $reason): bool
    {
        return $this->update([
            'status' => PayoutStatus::FAILED,
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Put payout on hold.
     *
     * @param string|null $reason Hold reason
     * @return bool
     */
    public function putOnHold(?string $reason = null): bool
    {
        if (!$this->status->canCancel()) {
            return false;
        }

        return $this->update([
            'status' => PayoutStatus::ON_HOLD,
            'notes' => $reason ? ($this->notes . "\n[ON HOLD] " . $reason) : $this->notes,
        ]);
    }

    /**
     * Cancel the payout.
     *
     * @param string|null $reason Cancellation reason
     * @return bool
     */
    public function cancel(?string $reason = null): bool
    {
        if (!$this->status->canCancel()) {
            return false;
        }

        return $this->update([
            'status' => PayoutStatus::CANCELLED,
            'notes' => $reason ? ($this->notes . "\n[CANCELLED] " . $reason) : $this->notes,
        ]);
    }

    // ==================== CALCULATION METHODS ====================

    /**
     * Calculate net payout from gross revenue and commission.
     *
     * @return void
     */
    public function calculateNetPayout(): void
    {
        $this->net_payout = (float) $this->gross_revenue
            - (float) $this->commission_amount
            + (float) $this->adjustments;
    }

    /**
     * Recalculate all financial values from bookings.
     *
     * @return void
     */
    public function recalculateFromBookings(): void
    {
        $bookings = Booking::whereHas('hall', function ($q): void {
            $q->where('owner_id', $this->owner_id);
        })
            ->whereBetween('booking_date', [
                $this->period_start,
                $this->period_end,
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->get();

        $this->bookings_count = $bookings->count();
        $this->gross_revenue = (float) $bookings->sum('total_amount');
        $this->commission_amount = (float) $bookings->sum('commission_amount');
        $this->net_payout = (float) $bookings->sum('owner_payout') + (float) $this->adjustments;

        // Calculate average commission rate
        if ($this->gross_revenue > 0) {
            $this->commission_rate = ($this->commission_amount / $this->gross_revenue) * 100;
        }
    }

    // ==================== STATIC METHODS ====================

    /**
     * Generate a unique payout number.
     *
     * @return string Payout number (e.g., PO-2025-00001)
     */
    public static function generatePayoutNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'PO-' . $year . '-';

        // Get the last payout number for this year
        $lastPayout = self::where('payout_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastPayout) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastPayout->payout_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create a payout for an owner for a specific period.
     *
     * @param int $ownerId Owner user ID
     * @param string $periodStart Period start date
     * @param string $periodEnd Period end date
     * @return self The created payout
     */
    public static function createForPeriod(
        int $ownerId,
        string $periodStart,
        string $periodEnd
    ): self {
        $payout = new self([
            'owner_id' => $ownerId,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => PayoutStatus::PENDING,
        ]);

        $payout->recalculateFromBookings();
        $payout->save();

        return $payout;
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the formatted gross revenue.
     *
     * @return string
     */
    public function getFormattedGrossRevenueAttribute(): string
    {
        return number_format((float) $this->gross_revenue, 3) . ' OMR';
    }

    /**
     * Get the formatted net payout.
     *
     * @return string
     */
    public function getFormattedNetPayoutAttribute(): string
    {
        return number_format((float) $this->net_payout, 3) . ' OMR';
    }

    /**
     * Get the formatted commission amount.
     *
     * @return string
     */
    public function getFormattedCommissionAttribute(): string
    {
        return number_format((float) $this->commission_amount, 3) . ' OMR';
    }

    /**
     * Get the period as a string.
     *
     * @return string
     */
    public function getPeriodStringAttribute(): string
    {
        return $this->period_start->format('M d, Y') . ' - ' . $this->period_end->format('M d, Y');
    }


}
