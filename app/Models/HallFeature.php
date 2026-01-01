<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Spatie\Permission\Traits\HasRoles;

/**
 * HallFeature Model
 *
 * Features are stored as JSON array in halls.features column,
 * NOT through a pivot table. This model provides helper methods
 * to work with the JSON-based relationship.
 *
 * @package App\Models
 */
class HallFeature extends Model
{
    use HasFactory, HasTranslations, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<string>
     */
    public array $translatable = ['name', 'description'];

    // ==================== RELATIONSHIPS ====================
    // NOTE: Features are stored as JSON in halls.features column,
    // NOT through a pivot table. Use getHalls() method instead.

    /**
     * Get halls that have this feature (JSON-based lookup).
     *
     * Since features are stored as JSON array in halls.features column,
     * we use whereJsonContains for the query.
     *
     * @return Collection<Hall>
     */
    public function getHalls(): Collection
    {
        return Hall::whereJsonContains('features', $this->id)->get();
    }

    /**
     * Get active halls that have this feature.
     *
     * @return Collection<Hall>
     */
    public function getActiveHalls(): Collection
    {
        return Hall::where('is_active', true)
            ->whereJsonContains('features', $this->id)
            ->get();
    }

    /**
     * Get halls owned by a specific user that have this feature.
     *
     * @param int $ownerId
     * @return Collection<Hall>
     */
    public function getHallsByOwner(int $ownerId): Collection
    {
        return Hall::where('owner_id', $ownerId)
            ->whereJsonContains('features', $this->id)
            ->get();
    }

    // ==================== SCOPES ====================

    /**
     * Scope to only active features.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name->en');
    }

    // ==================== BOOT ====================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug on creation
        static::creating(function ($feature) {
            if (empty($feature->slug)) {
                $name = is_array($feature->name) ? $feature->name : json_decode($feature->name, true);
                $feature->slug = Str::slug($name['en'] ?? $name['ar'] ?? 'feature');
            }
        });
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the translated name attribute.
     *
     * @return string
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();
        $nameArray = is_array($this->name) ? $this->name : json_decode($this->name, true);
        return $nameArray[$locale] ?? $nameArray['en'] ?? 'Unnamed Feature';
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get count of halls using this feature.
     *
     * @return int
     */
    public function getHallsCount(): int
    {
        return Hall::whereJsonContains('features', $this->id)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get count of halls by a specific owner using this feature.
     *
     * @param int $ownerId
     * @return int
     */
    public function getHallsCountByOwner(int $ownerId): int
    {
        return Hall::where('owner_id', $ownerId)
            ->whereJsonContains('features', $this->id)
            ->count();
    }

    /**
     * Check if a specific hall has this feature.
     *
     * @param int $hallId
     * @return bool
     */
    public function isUsedByHall(int $hallId): bool
    {
        return Hall::where('id', $hallId)
            ->whereJsonContains('features', $this->id)
            ->exists();
    }

    /**
     * Add this feature to a hall.
     *
     * @param Hall $hall
     * @return bool
     */
    public function addToHall(Hall $hall): bool
    {
        $features = $hall->features ?? [];

        if (!in_array($this->id, $features)) {
            $features[] = $this->id;
            return $hall->update(['features' => $features]);
        }

        return false; // Already added
    }

    /**
     * Remove this feature from a hall.
     *
     * @param Hall $hall
     * @return bool
     */
    public function removeFromHall(Hall $hall): bool
    {
        $features = $hall->features ?? [];
        $features = array_values(array_filter($features, fn ($f) => $f != $this->id));

        return $hall->update(['features' => $features]);
    }
}
