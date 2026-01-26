<?php

declare(strict_types=1);

/**
 * ExpenseCategory Model
 * 
 * Represents expense categories for organizing and classifying expenses.
 * Supports bilingual content (Arabic/English) using Spatie Translatable.
 * 
 * @package App\Models
 * @author  Majalis Development Team
 * @version 1.0.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * ExpenseCategory Model
 * 
 * @property int $id
 * @property int|null $owner_id
 * @property array $name
 * @property array|null $description
 * @property string $color
 * @property string $icon
 * @property string $type
 * @property bool $is_active
 * @property bool $is_system
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * @property-read User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|Expense[] $expenses
 * @property-read int|null $expenses_count
 */
class ExpenseCategory extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'expense_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'color',
        'icon',
        'type',
        'is_active',
        'is_system',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'name',
        'description',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'color' => '#6366f1',
        'icon' => 'heroicon-o-banknotes',
        'type' => 'operational',
        'is_active' => true,
        'is_system' => false,
        'order' => 0,
    ];

    /**
     * Category type options
     */
    public const TYPES = [
        'operational' => 'Operational',
        'event' => 'Event',
        'maintenance' => 'Maintenance',
        'staff' => 'Staff',
        'utility' => 'Utility',
        'marketing' => 'Marketing',
        'other' => 'Other',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the owner (user) that created this category
     *
     * @return BelongsTo<User, ExpenseCategory>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all expenses in this category
     *
     * @return HasMany<Expense>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to only active categories
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to system categories only
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to categories for a specific owner (includes system categories)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOwner($query, int $ownerId)
    {
        return $query->where(function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId)
              ->orWhere('is_system', true);
        });
    }

    /**
     * Scope to filter by type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to order by order column
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the localized name
     *
     * @return string
     */
    public function getLocalizedNameAttribute(): string
    {
        return $this->getTranslation('name', app()->getLocale()) 
            ?? $this->getTranslation('name', 'en') 
            ?? '';
    }

    /**
     * Get the badge HTML for display
     *
     * @return string
     */
    public function getBadgeHtmlAttribute(): string
    {
        $name = e($this->localized_name);
        $color = e($this->color);
        
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: %s20; color: %s;">%s</span>',
            $color,
            $color,
            $name
        );
    }

    /**
     * Get the type label
     *
     * @return string
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'operational' => app()->getLocale() === 'ar' ? 'تشغيلي' : 'Operational',
            'event' => app()->getLocale() === 'ar' ? 'فعاليات' : 'Event',
            'maintenance' => app()->getLocale() === 'ar' ? 'صيانة' : 'Maintenance',
            'staff' => app()->getLocale() === 'ar' ? 'موظفين' : 'Staff',
            'utility' => app()->getLocale() === 'ar' ? 'خدمات' : 'Utility',
            'marketing' => app()->getLocale() === 'ar' ? 'تسويق' : 'Marketing',
            default => app()->getLocale() === 'ar' ? 'أخرى' : 'Other',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this category can be deleted
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        // System categories cannot be deleted
        if ($this->is_system) {
            return false;
        }

        // Categories with expenses cannot be deleted
        if ($this->expenses()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get total expenses amount for this category
     *
     * @param int|null $ownerId Filter by owner
     * @param string|null $startDate Start date filter
     * @param string|null $endDate End date filter
     * @return float
     */
    public function getTotalExpenses(?int $ownerId = null, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->expenses()
            ->where('status', 'approved');

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        if ($startDate) {
            $query->where('expense_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('expense_date', '<=', $endDate);
        }

        return (float) $query->sum('total_amount');
    }
}
