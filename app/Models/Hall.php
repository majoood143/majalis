<?php

namespace App\Models;

use App\Enums\TimeSlot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Hall extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'city_id',
        'owner_id',
        'name',
        'slug',
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
        'features',
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
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'address_localized' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacity_min' => 'integer',
        'capacity_max' => 'integer',
        'price_per_slot' => 'decimal:2',
        'pricing_override' => 'array',
        'gallery' => 'array',
        'virtual_tour_url' => 'array',
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
    ];

    public $translatable = ['name', 'description', 'address_localized', 'meta_title', 'meta_description'];

    // Relationships
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function extraServices(): HasMany
    {
        return $this->hasMany(ExtraService::class);
    }

    public function activeExtraServices(): HasMany
    {
        return $this->extraServices()->where('is_active', true)->orderBy('order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    public function availability(): HasMany
    {
        return $this->hasMany(HallAvailability::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeForCapacity($query, int $guests)
    {
        return $query->where('capacity_min', '<=', $guests)
            ->where('capacity_max', '>=', $guests);
    }

    public function scopePriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price_per_slot', [$min, $max]);
    }

    public function scopeWithFeatures($query, array $featureIds)
    {
        foreach ($featureIds as $featureId) {
            $query->whereJsonContains('features', $featureId);
        }
        return $query;
    }

    public function scopeAvailableOn($query, string $date, string $timeSlot)
    {
        return $query->where('is_active', true)
            ->whereDoesntHave('bookings', function ($q) use ($date, $timeSlot) {
                $q->where('booking_date', $date)
                    ->where('time_slot', $timeSlot)
                    ->whereIn('status', ['pending', 'confirmed']);
            })
            ->whereDoesntHave('availability', function ($q) use ($date, $timeSlot) {
                $q->where('date', $date)
                    ->where('time_slot', $timeSlot)
                    ->where('is_available', false);
            });
    }

    // Accessors
    public function getNameAttribute($value)
    {
        $decoded = json_decode($value, true);
        $locale = app()->getLocale();
        return $decoded[$locale] ?? $decoded['en'] ?? '';
    }

    public function getRegionAttribute()
    {
        return $this->city?->region;
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hall) {
            if (empty($hall->slug)) {
                $name = is_array($hall->name) ? $hall->name : json_decode($hall->name, true);
                $hall->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'hall');
            }
        });

        static::updating(function ($hall) {
            if ($hall->isDirty('name') && empty($hall->slug)) {
                $name = is_array($hall->name) ? $hall->name : json_decode($hall->name, true);
                $hall->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'hall');
            }
        });
    }

    // Pricing Methods
    public function getPriceForSlot(string $timeSlot): float
    {
        if ($this->pricing_override && isset($this->pricing_override[$timeSlot])) {
            return (float) $this->pricing_override[$timeSlot];
        }

        return (float) $this->price_per_slot;
    }

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

    // Availability Methods
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

    // Statistics Methods
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

    public function incrementBookings(): void
    {
        $this->increment('total_bookings');
    }

    // Revenue Methods
    public function getTotalRevenue(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    public function getOwnerEarnings(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('owner_payout');
    }

    public function getPlatformEarnings(): float
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('commission_amount');
    }

    // Feature Methods
    public function hasFeature(int $featureId): bool
    {
        return in_array($featureId, $this->features ?? []);
    }

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
}
