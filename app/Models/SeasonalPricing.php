<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;
use Carbon\Carbon;

/**
 * SeasonalPricing Model
 *
 * Manages date-range based pricing rules for halls.
 * Allows owners to set special prices for holidays, weekends,
 * peak seasons, and special events.
 *
 * Pricing Types:
 * - percentage: Increase/decrease by percentage (e.g., +20%)
 * - fixed_increase: Add fixed amount (e.g., +50 OMR)
 * - fixed_price: Set exact price (e.g., 200 OMR)
 *
 * @property int $id
 * @property int $hall_id
 * @property array $name Translatable
 * @property string $type seasonal|holiday|weekend|special_event|early_bird|last_minute
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property bool $is_recurring
 * @property string|null $recurrence_type weekly|yearly|null
 * @property array|null $days_of_week
 * @property string $adjustment_type percentage|fixed_increase|fixed_price
 * @property float $adjustment_value
 * @property array|null $apply_to_slots
 * @property int $priority
 * @property float|null $min_price
 * @property float|null $max_price
 * @property bool $is_active
 * @property string|null $notes
 *
 * @property-read Hall $hall
 */
class SeasonalPricing extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'seasonal_pricing';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'hall_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'is_recurring',
        'recurrence_type',
        'days_of_week',
        'adjustment_type',
        'adjustment_value',
        'apply_to_slots',
        'priority',
        'min_price',
        'max_price',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'name' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'days_of_week' => 'array',
        'adjustment_value' => 'decimal:3',
        'apply_to_slots' => 'array',
        'priority' => 'integer',
        'min_price' => 'decimal:3',
        'max_price' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    /**
     * Translatable attributes.
     */
    public array $translatable = ['name'];

    /**
     * Pricing rule types.
     */
    public const TYPES = [
        'seasonal' => ['en' => 'Seasonal', 'ar' => 'موسمي'],
        'holiday' => ['en' => 'Holiday', 'ar' => 'إجازة/عيد'],
        'weekend' => ['en' => 'Weekend', 'ar' => 'نهاية الأسبوع'],
        'special_event' => ['en' => 'Special Event', 'ar' => 'مناسبة خاصة'],
        'early_bird' => ['en' => 'Early Bird', 'ar' => 'حجز مبكر'],
        'last_minute' => ['en' => 'Last Minute', 'ar' => 'اللحظة الأخيرة'],
    ];

    /**
     * Adjustment types.
     */
    public const ADJUSTMENT_TYPES = [
        'percentage' => ['en' => 'Percentage', 'ar' => 'نسبة مئوية'],
        'fixed_increase' => ['en' => 'Fixed Increase', 'ar' => 'زيادة ثابتة'],
        'fixed_price' => ['en' => 'Fixed Price', 'ar' => 'سعر ثابت'],
    ];

    /**
     * Recurrence types.
     */
    public const RECURRENCE_TYPES = [
        'weekly' => ['en' => 'Weekly', 'ar' => 'أسبوعي'],
        'yearly' => ['en' => 'Yearly', 'ar' => 'سنوي'],
    ];

    /**
     * Get the hall that owns this pricing rule.
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Scope to get active rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get rules applicable for a specific date.
     */
    public function scopeForDate(Builder $query, string|Carbon $date): Builder
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $dateString = $date->toDateString();

        return $query->where(function ($q) use ($date, $dateString) {
            // Non-recurring rules
            $q->where(function ($subQ) use ($dateString) {
                $subQ->where('is_recurring', false)
                    ->whereDate('start_date', '<=', $dateString)
                    ->whereDate('end_date', '>=', $dateString);
            });

            // Weekly recurring (weekends)
            $q->orWhere(function ($subQ) use ($date) {
                $subQ->where('is_recurring', true)
                    ->where('recurrence_type', 'weekly')
                    ->whereJsonContains('days_of_week', $date->dayOfWeek);
            });

            // Yearly recurring (annual holidays)
            $q->orWhere(function ($subQ) use ($date) {
                $subQ->where('is_recurring', true)
                    ->where('recurrence_type', 'yearly')
                    ->whereMonth('start_date', '<=', $date->month)
                    ->whereMonth('end_date', '>=', $date->month)
                    ->whereDay('start_date', '<=', $date->day)
                    ->whereDay('end_date', '>=', $date->day);
            });
        });
    }

    /**
     * Scope to get rules for a specific time slot.
     */
    public function scopeForSlot(Builder $query, string $timeSlot): Builder
    {
        return $query->where(function ($q) use ($timeSlot) {
            $q->whereNull('apply_to_slots')
                ->orWhereJsonContains('apply_to_slots', $timeSlot);
        });
    }

    /**
     * Check if this rule applies to a specific date.
     */
    public function appliesToDate(string|Carbon $date): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Non-recurring
        if (!$this->is_recurring) {
            return $date->between($this->start_date, $this->end_date);
        }

        // Weekly (weekends)
        if ($this->recurrence_type === 'weekly' && $this->days_of_week) {
            return in_array($date->dayOfWeek, $this->days_of_week);
        }

        // Yearly (annual holidays)
        if ($this->recurrence_type === 'yearly') {
            $startMonth = $this->start_date->month;
            $startDay = $this->start_date->day;
            $endMonth = $this->end_date->month;
            $endDay = $this->end_date->day;

            // Simple same-year comparison
            if ($startMonth === $endMonth) {
                return $date->month === $startMonth &&
                       $date->day >= $startDay &&
                       $date->day <= $endDay;
            }

            // Multi-month range
            return ($date->month === $startMonth && $date->day >= $startDay) ||
                   ($date->month === $endMonth && $date->day <= $endDay) ||
                   ($date->month > $startMonth && $date->month < $endMonth);
        }

        return false;
    }

    /**
     * Check if this rule applies to a specific time slot.
     */
    public function appliesToSlot(string $timeSlot): bool
    {
        if (empty($this->apply_to_slots)) {
            return true; // Applies to all slots
        }

        return in_array($timeSlot, $this->apply_to_slots);
    }

    /**
     * Calculate the adjusted price based on this rule.
     *
     * @param float $basePrice The base price before adjustment
     * @return float The adjusted price
     */
    public function calculatePrice(float $basePrice): float
    {
        $adjustedPrice = match ($this->adjustment_type) {
            'percentage' => $basePrice * (1 + ($this->adjustment_value / 100)),
            'fixed_increase' => $basePrice + $this->adjustment_value,
            'fixed_price' => $this->adjustment_value,
            default => $basePrice,
        };

        // Apply min/max constraints
        if ($this->min_price !== null && $adjustedPrice < $this->min_price) {
            $adjustedPrice = $this->min_price;
        }

        if ($this->max_price !== null && $adjustedPrice > $this->max_price) {
            $adjustedPrice = $this->max_price;
        }

        return max(0, round($adjustedPrice, 3));
    }

    /**
     * Get the type label in current locale.
     */
    public function getTypeLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return self::TYPES[$this->type][$locale] ?? $this->type;
    }

    /**
     * Get the adjustment type label in current locale.
     */
    public function getAdjustmentTypeLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return self::ADJUSTMENT_TYPES[$this->adjustment_type][$locale] ?? $this->adjustment_type;
    }

    /**
     * Get human-readable adjustment description.
     */
    public function getAdjustmentDescriptionAttribute(): string
    {
        return match ($this->adjustment_type) {
            'percentage' => ($this->adjustment_value >= 0 ? '+' : '') . $this->adjustment_value . '%',
            'fixed_increase' => ($this->adjustment_value >= 0 ? '+' : '') . number_format($this->adjustment_value, 3) . ' OMR',
            'fixed_price' => number_format($this->adjustment_value, 3) . ' OMR',
            default => '',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'gray';
        }

        $today = now();

        if ($this->is_recurring) {
            return 'success';
        }

        if ($this->end_date->isPast()) {
            return 'gray';
        }

        if ($this->start_date->isFuture()) {
            return 'info';
        }

        return 'success';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return __('owner.pricing.status.inactive');
        }

        if ($this->is_recurring) {
            return __('owner.pricing.status.recurring');
        }

        if ($this->end_date->isPast()) {
            return __('owner.pricing.status.expired');
        }

        if ($this->start_date->isFuture()) {
            return __('owner.pricing.status.scheduled');
        }

        return __('owner.pricing.status.active');
    }
}
