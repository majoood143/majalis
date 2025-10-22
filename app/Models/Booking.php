<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    protected $casts = [
        'booking_date' => 'date',
        'event_details' => 'array',
        'number_of_guests' => 'integer',
        'hall_price' => 'decimal:2',
        'services_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'owner_payout' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'status' => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function extraServices(): BelongsToMany
    {
        return $this->belongsToMany(ExtraService::class, 'booking_extra_services')
            ->withPivot(['service_name', 'unit_price', 'quantity', 'total_price'])
            ->withTimestamps();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', BookingStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', BookingStatus::CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', PaymentStatus::PAID);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    public function scopeForOwner($query, int $ownerId)
    {
        return $query->whereHas('hall', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
            ->orderBy('booking_date');
    }

    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString())
            ->orderByDesc('booking_date');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = self::generateBookingNumber();
            }
        });
    }

    // Status Methods
    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    public function canBeCancelled(): bool
    {
        if ($this->isCancelled() || $this->isCompleted()) {
            return false;
        }

        // Check if within cancellation window
        $cancellationDeadline = Carbon::parse($this->booking_date)
            ->subHours($this->hall->cancellation_hours);

        return now()->lt($cancellationDeadline);
    }

    public function canBeReviewed(): bool
    {
        return $this->isCompleted()
            && $this->isPaid()
            && !$this->review()->exists();
    }

    // Action Methods
    public function confirm(): void
    {
        $this->update([
            'status' => BookingStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => BookingStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $refundAmount = 0;

        if ($this->isPaid() && $this->canBeCancelled()) {
            $cancellationFee = ($this->total_amount * $this->hall->cancellation_fee_percentage) / 100;
            $refundAmount = $this->total_amount - $cancellationFee;
        }

        $this->update([
            'status' => BookingStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'refund_amount' => $refundAmount,
        ]);
    }

    // Helper Methods
    public static function generateBookingNumber(): string
    {
        $year = date('Y');
        $lastBooking = self::whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $sequence = $lastBooking ? intval(substr($lastBooking->booking_number, -5)) + 1 : 1;

        return 'BK-' . $year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    public function isUpcoming(): bool
    {
        return $this->booking_date->isFuture() &&
            in_array($this->status, [BookingStatus::PENDING, BookingStatus::CONFIRMED]);
    }

    public function isPast(): bool
    {
        return $this->booking_date->isPast();
    }

    public function getDaysUntilBooking(): int
    {
        return now()->diffInDays($this->booking_date, false);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->booking_date->format('d M Y');
    }

    public function getTimeSlotLabelAttribute(): string
    {
        return __(ucfirst(str_replace('_', ' ', $this->time_slot)));
    }

    // Calculate totals
    public function calculateTotals(array $extraServiceData = []): void
    {
        $hallPrice = $this->hall->getPriceForDate(
            $this->booking_date->format('Y-m-d'),
            $this->time_slot
        );

        $servicesPrice = 0;
        foreach ($extraServiceData as $serviceData) {
            $servicesPrice += $serviceData['total_price'];
        }

        $subtotal = $hallPrice + $servicesPrice;

        // Get commission settings
        $commission = $this->calculateCommission($subtotal);

        $this->hall_price = $hallPrice;
        $this->services_price = $servicesPrice;
        $this->subtotal = $subtotal;
        $this->commission_amount = $commission['amount'];
        $this->commission_type = $commission['type'];
        $this->commission_value = $commission['value'];
        $this->platform_fee = $commission['amount'];
        $this->total_amount = $subtotal + $commission['amount'];
        $this->owner_payout = $subtotal - $commission['amount'];
    }

    protected function calculateCommission(float $amount): array
    {
        // Check for hall-specific commission
        $commissionSetting = CommissionSetting::where('hall_id', $this->hall_id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->first();

        // If no hall-specific, check owner-specific
        if (!$commissionSetting) {
            $commissionSetting = CommissionSetting::where('owner_id', $this->hall->owner_id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('effective_from')
                        ->orWhere('effective_from', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now());
                })
                ->first();
        }

        // If no specific settings, use global default
        if (!$commissionSetting) {
            $commissionSetting = CommissionSetting::whereNull('hall_id')
                ->whereNull('owner_id')
                ->where('is_active', true)
                ->first();
        }

        $commissionType = $commissionSetting->commission_type ?? 'percentage';
        $commissionValue = $commissionSetting->commission_value ?? 10;

        $commissionAmount = $commissionType === 'percentage'
            ? ($amount * $commissionValue) / 100
            : $commissionValue;

        return [
            'amount' => $commissionAmount,
            'type' => $commissionType,
            'value' => $commissionValue,
        ];
    }
}
