<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TimeSlot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Spatie\Permission\Traits\HasRoles;


/**
 * Hall Model
 *
 * Represents event venues/halls in the Majalis booking system.
 * Supports multilingual content, pricing configurations, and advance payment options.
 *
 * Advance Payment Feature:
 * - Hall owners can require customers to pay advance before full booking
 * - Supports fixed amounts (e.g., 500 OMR) or percentage-based (e.g., 20%)
 * - Advance calculated on total booking (hall price + services)
 * - Services are ALWAYS included in advance (reserved from suppliers)
 *
 * @property int $id
 * @property int $city_id
 * @property int $owner_id
 * @property array $name Translatable (en, ar)
 * @property string $slug
 * @property array $description Translatable (en, ar)
 * @property string $address
 * @property float $price_per_slot Base price in OMR
 * @property array|null $pricing_override Slot-specific pricing
 * @property bool $allows_advance_payment Enable advance payment
 * @property string $advance_payment_type 'fixed' or 'percentage'
 * @property float|null $advance_payment_amount Fixed amount in OMR
 * @property float|null $advance_payment_percentage Percentage value
 * @property float|null $minimum_advance_payment Minimum advance required
 */
class Hall extends Model
{
    use HasFactory, HasTranslations, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'region_id',
        'city_id',
        'owner_id',
        'name',
        'slug',
        'area',
        'description',
        'address',
        'address_localized',
        'latitude',
        'longitude',
        'google_maps_url',
        'capacity_min',
        'capacity_max',
        'price_per_slot',
        'pricing_override',
        'phone',
        'whatsapp',
        'email',
        'featured_image',
        'gallery',
        'video_url',
        'virtual_tour_url',
        'is_active',
        'is_featured',
        'requires_approval',
        'cancellation_hours',
        'cancellation_fee_percentage',
        'total_bookings',
        'average_rating',
        'total_reviews',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'features',
        // Advance Payment Fields
        'allows_advance_payment',
        'advance_payment_type',
        'advance_payment_amount',
        'advance_payment_percentage',
        'minimum_advance_payment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [

        'pricing_override' => 'array',
        'allows_advance_payment' => 'boolean',
        'advance_payment_amount' => 'decimal:3',
        'advance_payment_percentage' => 'decimal:2',
        'minimum_advance_payment' => 'decimal:3',

        'name' => 'array',
        'description' => 'array',
        'address_localized' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacity_min' => 'integer',
        'capacity_max' => 'integer',
        'price_per_slot' => 'decimal:3',
        'pricing_override' => 'array',
        'gallery' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_approval' => 'boolean',
        'cancellation_hours' => 'integer',
        'cancellation_fee_percentage' => 'decimal:2',
        'total_bookings' => 'integer',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
        // Advance Payment Casts
        'allows_advance_payment' => 'boolean',
        'advance_payment_amount' => 'decimal:3',
        'advance_payment_percentage' => 'decimal:2',
        'minimum_advance_payment' => 'decimal:3',
    ];

    /**
     * Translatable attributes.
     *
     * @var array<string>
     */
    public $translatable = [
        'name',
        'description',
        'address_localized',
        'meta_title',
        'meta_description',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the city that owns the hall.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the region that owns the hall.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the user who owns this hall.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all bookings for this hall.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all extra services offered by this hall.
     */
    public function extraServices(): HasMany
    {
        return $this->hasMany(ExtraService::class);
    }

    /**
     * Get only active extra services.
     */
    public function activeExtraServices(): HasMany
    {
        return $this->extraServices()->where('is_active', true)->orderBy('order');
    }

    /**
     * Get all reviews for this hall.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get only approved reviews.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    /**
     * Get availability records for this hall.
     */
    public function availability(): HasMany
    {
        return $this->hasMany(HallAvailability::class);
    }

    /**
     * Alias for availability() - used by Filament RelationManagers.
     */
    public function availabilities(): HasMany
    {
        return $this->availability();
    }

    /**
     * Get all images for the hall.
     */
    public function images(): HasMany
    {
        return $this->hasMany(HallImage::class)
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * Get only active images.
     */
    public function activeImages(): HasMany
    {
        return $this->hasMany(HallImage::class)
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * Get only gallery images.
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(HallImage::class)
            ->where('type', 'gallery')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * Get the featured image from hall_images table.
     */
    public function featuredImages(): HasMany
    {
        return $this->hasMany(HallImage::class)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('order');
    }

    // ==================== BOOT & EVENTS ====================

    /**
     * Boot the model and register events.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name on creation
        static::creating(function ($hall) {
            if (empty($hall->slug)) {
                $name = is_array($hall->name) ? $hall->name : json_decode($hall->name, true);
                $hall->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'hall');
            }
        });

        // Update slug if name changes
        static::updating(function ($hall) {
            if ($hall->isDirty('name') && empty($hall->slug)) {
                $name = is_array($hall->name) ? $hall->name : json_decode($hall->name, true);
                $hall->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'hall');
            }
        });
    }

    /**
     * Get the route key for the model (use slug instead of ID).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ==================== PRICING METHODS ====================

    /**
     * Get price for a specific time slot.
     *
     * Checks pricing_override first, falls back to base price.
     *
     * @param string $timeSlot morning|afternoon|evening|full_day
     * @return float Price in OMR
     */
    public function getPriceForSlot(string $timeSlot): float
    {
        if ($this->pricing_override && isset($this->pricing_override[$timeSlot])) {
            return (float) $this->pricing_override[$timeSlot];
        }

        return (float) $this->price_per_slot;
    }

    /**
     * Get price for a specific date and time slot.
     *
     * Checks custom availability pricing first, then slot pricing.
     *
     * @param string $date Date in Y-m-d format
     * @param string $timeSlot morning|afternoon|evening|full_day
     * @return float Price in OMR
     */
    public function getPriceForDate(string $date, string $timeSlot): float
    {
        // Check for custom pricing on specific date
        $customAvailability = $this->availability()
            ->where('date', $date)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($customAvailability && $customAvailability->custom_price) {
            return (float) $customAvailability->custom_price;
        }

        return $this->getPriceForSlot($timeSlot);
    }

    // ==================== ADVANCE PAYMENT METHODS ====================

    /**
     * Check if this hall requires advance payment.
     *
     * @return bool True if advance payment is enabled
     */
    public function requiresAdvancePayment(): bool
    {
        return (bool) $this->allows_advance_payment;
    }

    /**
     * Calculate advance payment amount based on hall settings.
     *
     * Important: Advance is calculated on TOTAL booking amount (hall + services)
     * because services must be reserved from suppliers upfront.
     *
     * @param float $totalAmount Total booking amount (hall_price + services_price)
     * @return float Advance amount in OMR
     */
    public function calculateAdvanceAmount(float $totalAmount): float
    {
        // Cast to float to handle string inputs from database (strict types compatibility)
        $totalAmount = (float) $totalAmount;

        // If advance payment not enabled, return 0
        if (!$this->allows_advance_payment) {
            return 0.0;
        }

        $advanceAmount = 0.0;

        // Calculate based on type
        if ($this->advance_payment_type === 'fixed') {
            // Fixed amount (e.g., 500 OMR)
            $advanceAmount = (float) $this->advance_payment_amount;
        } elseif ($this->advance_payment_type === 'percentage') {
            // Percentage of total (e.g., 20%)
            $percentage = (float) $this->advance_payment_percentage;
            $advanceAmount = ($totalAmount * $percentage) / 100;
        }

        // Apply minimum advance if set
        if ($this->minimum_advance_payment && $advanceAmount < $this->minimum_advance_payment) {
            $advanceAmount = (float) $this->minimum_advance_payment;
        }

        // Ensure advance doesn't exceed total amount
        if ($advanceAmount > $totalAmount) {
            $advanceAmount = $totalAmount;
        }

        return round($advanceAmount, 3);
    }

    /**
     * Calculate balance due after advance payment.
     *
     * @param float $totalAmount Total booking amount
     * @param float|null $advanceAmount Advance paid (if null, calculates from settings)
     * @return float Balance remaining in OMR
     */
    public function calculateBalanceDue(float $totalAmount, ?float $advanceAmount = null): float
    {
        // Cast to float to handle string inputs from database (strict types compatibility)
        $totalAmount = (float) $totalAmount;
        $advanceAmount = $advanceAmount !== null ? (float) $advanceAmount : null;

        // If no advance payment, balance is full amount
        if (!$this->allows_advance_payment) {
            return $totalAmount;
        }

        // Calculate advance if not provided
        if ($advanceAmount === null) {
            $advanceAmount = $this->calculateAdvanceAmount($totalAmount);
        }

        $balance = $totalAmount - $advanceAmount;

        // Ensure balance is not negative
        return max(0, round($balance, 3));
    }

    /**
     * Get advance payment preview for display purposes.
     *
     * Returns array with advance and balance for a given total.
     * Useful for showing preview in admin panel.
     *
     * @param float $totalAmount Sample total amount
     * @return array{advance: float, balance: float, type: string}
     */
    public function getAdvancePaymentPreview(float $totalAmount): array
    {
        // Cast to float to handle string inputs from database (strict types compatibility)
        $totalAmount = (float) $totalAmount;

        if (!$this->allows_advance_payment) {
            return [
                'advance' => 0.0,
                'balance' => $totalAmount,
                'type' => 'none',
            ];
        }

        $advance = $this->calculateAdvanceAmount($totalAmount);
        $balance = $this->calculateBalanceDue($totalAmount, $advance);

        return [
            'advance' => $advance,
            'balance' => $balance,
            'type' => $this->advance_payment_type,
        ];
    }

    /**
     * Validate advance payment configuration.
     *
     * Ensures hall has valid advance payment settings.
     *
     * @return array{valid: bool, errors: array<string>}
     */
    public function validateAdvancePaymentSettings(): array
    {
        $errors = [];

        if (!$this->allows_advance_payment) {
            return ['valid' => true, 'errors' => []];
        }

        // Check type-specific requirements
        if ($this->advance_payment_type === 'fixed') {
            if (!$this->advance_payment_amount || $this->advance_payment_amount <= 0) {
                $errors[] = 'Fixed advance amount must be greater than 0';
            }
        } elseif ($this->advance_payment_type === 'percentage') {
            if (!$this->advance_payment_percentage || $this->advance_payment_percentage <= 0) {
                $errors[] = 'Advance percentage must be greater than 0';
            }
            if ($this->advance_payment_percentage > 100) {
                $errors[] = 'Advance percentage cannot exceed 100%';
            }
        }

        // Check minimum advance
        if ($this->minimum_advance_payment && $this->minimum_advance_payment < 0) {
            $errors[] = 'Minimum advance payment cannot be negative';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    // ==================== AVAILABILITY METHODS ====================

    /**
     * Check if hall is available on a specific date and time slot.
     *
     * @param string $date Date in Y-m-d format
     * @param string $timeSlot morning|afternoon|evening|full_day
     * @return bool True if available
     */
    public function isAvailableOn(string $date, string $timeSlot): bool
    {
        // Check if hall is active
        if (!$this->is_active) {
            return false;
        }

        // Check custom availability settings
        $customAvailability = $this->availability()
            ->where('date', $date)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($customAvailability && !$customAvailability->is_available) {
            return false;
        }

        // Check for existing bookings
        return !$this->bookings()
            ->where('booking_date', $date)
            ->where('time_slot', $timeSlot)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Get available time slots for a specific date.
     *
     * @param string $date Date in Y-m-d format
     * @return array<array{slot: string, label: string, price: float, start_time: string, end_time: string}>
     */
    public function getAvailableSlots(string $date): array
    {
        $availableSlots = [];

        foreach (TimeSlot::cases() as $slot) {
            if ($this->isAvailableOn($date, $slot->value)) {
                $availableSlots[] = [
                    'slot' => $slot->value,
                    'label' => $slot->label(),
                    'price' => $this->getPriceForDate($date, $slot->value),
                    'start_time' => $slot->startTime(),
                    'end_time' => $slot->endTime(),
                ];
            }
        }

        return $availableSlots;
    }

    // ==================== STATISTICS METHODS ====================

    /**
     * Update average rating and total reviews count.
     */
    public function updateAverageRating(): void
    {
        $this->average_rating = $this->reviews()
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $this->total_reviews = $this->reviews()
            ->where('is_approved', true)
            ->count();

        $this->saveQuietly();
    }

    /**
     * Increment total bookings count.
     */
    public function incrementBookings(): void
    {
        $this->increment('total_bookings');
    }

    // ==================== REVENUE METHODS ====================

    /**
     * Get total revenue from all confirmed/completed bookings.
     *
     * @return float Total revenue in OMR
     */
    public function getTotalRevenue(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    /**
     * Get owner's earnings (after platform commission).
     *
     * @return float Owner earnings in OMR
     */
    public function getOwnerEarnings(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    /**
     * Get platform's earnings (commission).
     *
     * @return float Platform earnings in OMR
     */
    public function getPlatformEarnings(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('commission_amount');
    }

    // ==================== FEATURE METHODS ====================

    /**
     * Get list of active features for this hall.
     *
     * @return \Illuminate\Support\Collection<HallFeature>
     */
    public function getFeaturesList()
    {
        if (empty($this->features)) {
            return collect();
        }

        return HallFeature::whereIn('id', $this->features)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get features attribute properly formatted.
     *
     * @param mixed $value
     * @return array<int>
     */
    public function getFeaturesAttribute($value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? array_values(array_map('intval', $decoded)) : [];
        }

        if (is_array($value)) {
            return array_values(array_map('intval', $value));
        }

        return [];
    }

    // ==================== ACCESSOR METHODS ====================

    /**
     * Get translated name in current locale.
     *
     * @return string Hall name
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return is_array($this->name)
            ? ($this->name[$locale] ?? $this->name['en'] ?? 'Unnamed Hall')
            : $this->name;
    }

    /**
     * Get description in English.
     *
     * @return string Description
     */
    public function getDescriptionEnAttribute(): string
    {
        return $this->getTranslation('description', 'en') ?? 'No description';
    }

    /**
     * Get description in Arabic.
     *
     * @return string Description
     */
    public function getDescriptionArAttribute(): string
    {
        return $this->getTranslation('description', 'ar') ?? 'لا يوجد وصف';
    }


    /**
     * Get all payments through bookings (Laravel's HasManyThrough)
     * This allows us to access payments directly from hall
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,    // Target model
            Booking::class,    // Intermediate model
            'hall_id',         // Foreign key on bookings table
            'booking_id',      // Foreign key on payments table
            'id',              // Local key on halls table
            'id'               // Local key on bookings table
        );
    }





    /**
     * Calculate total revenue for this hall
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->payments()
            ->where('payments.status', 'paid')
            ->sum('amount');
    }

    /**
     * Calculate this month's revenue
     */
    public function getMonthlyRevenueAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}
