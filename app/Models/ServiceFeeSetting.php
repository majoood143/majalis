<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * Service Fee Setting Model
 *
 * Manages customer-facing service fees that are added ON TOP of the booking price.
 * Unlike commissions (deducted from owner payout), service fees are visible to
 * and paid by the customer.
 *
 * Scoping priority:
 *   1. Hall-specific   → fee tied to a specific hall
 *   2. Owner-specific  → fee tied to all halls of an owner
 *   3. Global          → default fallback for all bookings
 *
 * Financial flow:
 *   Customer pays:  subtotal + service_fee = total_amount
 *   Owner receives: total_amount - service_fee - commission = owner_payout
 *
 * @property int         $id
 * @property int|null    $hall_id
 * @property int|null    $owner_id
 * @property CommissionType $fee_type       Uses same enum as commission (percentage|fixed)
 * @property float       $fee_value
 * @property array|null  $name             Translatable {"en": "...", "ar": "..."}
 * @property array|null  $description      Translatable {"en": "...", "ar": "..."}
 * @property bool        $is_active
 * @property \Carbon\Carbon|null $effective_from
 * @property \Carbon\Carbon|null $effective_to
 *
 * @property-read Hall|null $hall
 * @property-read User|null $owner
 * @property-read string $formatted_value  e.g., "5%" or "2.000 OMR"
 * @property-read string $scope_name       e.g., "Global", "Hall: Grand Palace"
 *
 * @package App\Models
 */
class ServiceFeeSetting extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * The table associated with the model.
     */
    protected $table = 'service_fee_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hall_id',
        'owner_id',
        'fee_type',
        'fee_value',
        'name',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name'           => 'array',
        'description'    => 'array',
        'fee_type'       => CommissionType::class,  // Reuse same enum (percentage|fixed)
        'fee_value'      => 'decimal:2',
        'is_active'      => 'boolean',
        'effective_from' => 'date',
        'effective_to'   => 'date',
    ];

    /**
     * Translatable attributes (Spatie).
     *
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description'];

    // ─────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────

    /**
     * The hall this fee applies to (if hall-specific).
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * The owner this fee applies to (if owner-specific).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // ─────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────

    /**
     * Scope: Only active fee settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Global fee settings (no hall/owner).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('hall_id')->whereNull('owner_id');
    }

    /**
     * Scope: Fee settings for a specific hall.
     */
    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    /**
     * Scope: Fee settings for a specific owner.
     */
    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope: Fee settings effective on a given date.
     */
    public function scopeEffectiveOn($query, string $date)
    {
        return $query
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date);
            });
    }

    /**
     * Scope: Fee settings effective today.
     */
    public function scopeCurrent($query)
    {
        return $query->effectiveOn(now()->toDateString());
    }

    // ─────────────────────────────────────────────────────────
    // Helper Methods
    // ─────────────────────────────────────────────────────────

    /**
     * Check if this is a global (platform-wide) fee.
     */
    public function isGlobal(): bool
    {
        return is_null($this->hall_id) && is_null($this->owner_id);
    }

    /**
     * Check if this fee is specific to a hall.
     */
    public function isHallSpecific(): bool
    {
        return !is_null($this->hall_id);
    }

    /**
     * Check if this fee is specific to an owner.
     */
    public function isOwnerSpecific(): bool
    {
        return !is_null($this->owner_id) && is_null($this->hall_id);
    }

    /**
     * Check if this fee is effective on a given date.
     */
    public function isEffectiveOn(string $date): bool
    {
        $from = $this->effective_from?->format('Y-m-d');
        $to   = $this->effective_to?->format('Y-m-d');

        if ($from && $date < $from) {
            return false;
        }

        if ($to && $date > $to) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the service fee for a given subtotal.
     *
     * @param float $amount The subtotal (hall_price + services_price)
     * @return float The calculated service fee
     */
    public function calculateFee(float $amount): float
    {
        if ($this->fee_type === CommissionType::PERCENTAGE) {
            return round(($amount * (float) $this->fee_value) / 100, 2);
        }

        return round((float) $this->fee_value, 2);
    }

    /**
     * Resolve the applicable service fee for a given hall.
     *
     * Priority: Hall-specific → Owner-specific → Global
     *
     * @param Hall $hall The hall to resolve the fee for
     * @return static|null The applicable fee setting, or null if none
     */
    public static function resolveForHall(Hall $hall): ?static
    {
        return static::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now()->toDateString());
            })
            ->where(function ($q) use ($hall) {
                $q->where('hall_id', $hall->id)
                    ->orWhere(function ($q2) use ($hall) {
                        $q2->whereNull('hall_id')
                            ->where('owner_id', $hall->owner_id);
                    })
                    ->orWhere(function ($q2) {
                        $q2->whereNull('hall_id')
                            ->whereNull('owner_id');
                    });
            })
            ->orderByRaw('CASE
                WHEN hall_id IS NOT NULL THEN 1
                WHEN owner_id IS NOT NULL THEN 2
                ELSE 3
            END')
            ->first();
    }

    // ─────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────

    /**
     * Human-readable formatted value (e.g., "5%" or "2.000 OMR").
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->fee_type === CommissionType::PERCENTAGE) {
            return $this->fee_value . '%';
        }

        return number_format((float) $this->fee_value, 3) . ' OMR';
    }

    /**
     * Human-readable scope name (e.g., "Global", "Hall: Grand Palace").
     */
    public function getScopeNameAttribute(): string
    {
        if ($this->isHallSpecific()) {
            return __('service-fee.hall_specific', ['name' => $this->hall->name ?? '']);
        }

        if ($this->isOwnerSpecific()) {
            return __('service-fee.owner_specific', ['name' => $this->owner->name ?? '']);
        }

        return __('service-fee.global');
    }
}
