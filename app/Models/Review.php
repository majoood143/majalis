<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class Review extends Model
{
    use HasFactory,HasRoles;

    protected $fillable = [
        'hall_id',
        'booking_id',
        'user_id',
        'rating',
        'comment',
        'photos',
        'cleanliness_rating',
        'service_rating',
        'value_rating',
        'location_rating',
        'is_approved',
        'is_featured',
        'admin_notes',
        'owner_response',
        'owner_response_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'cleanliness_rating' => 'integer',
        'service_rating' => 'integer',
        'value_rating' => 'integer',
        'location_rating' => 'integer',
        'photos' => 'array',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'owner_response_at' => 'datetime',
    ];

    // Relationships
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_approved', true);
    }

    public function scopeForHall($query, int $hallId)
    {
        return $query->where('hall_id', $hallId);
    }

    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeMinRating($query, int $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('owner_response');
    }

    public function scopeWithoutResponse($query)
    {
        return $query->whereNull('owner_response');
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            if ($review->is_approved) {
                $review->hall->updateAverageRating();
            }
        });

        static::updated(function ($review) {
            if ($review->isDirty('is_approved') || $review->isDirty('rating')) {
                $review->hall->updateAverageRating();
            }
        });

        static::deleted(function ($review) {
            $review->hall->updateAverageRating();
        });
    }

    // Status Methods
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function hasResponse(): bool
    {
        return !empty($this->owner_response);
    }

    public function hasPhotos(): bool
    {
        return !empty($this->photos);
    }

    // Action Methods
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'is_approved' => false,
            'admin_notes' => $reason,
        ]);
    }

    public function addOwnerResponse(string $response): void
    {
        $this->update([
            'owner_response' => $response,
            'owner_response_at' => now(),
        ]);
    }

    public function markAsFeatured(): void
    {
        $this->update(['is_featured' => true]);
    }

    public function unmarkAsFeatured(): void
    {
        $this->update(['is_featured' => false]);
    }

    // Helper Methods
    public function getStarsArray(): array
    {
        return array_fill(0, $this->rating, true);
    }

    public function getAverageDetailedRating(): float
    {
        $ratings = array_filter([
            $this->cleanliness_rating,
            $this->service_rating,
            $this->value_rating,
            $this->location_rating,
        ]);

        if (empty($ratings)) {
            return $this->rating;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d M Y');
    }

    public function getReviewerNameAttribute(): string
    {
        return $this->user->name;
    }
}
