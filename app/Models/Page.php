<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Page Model
 *
 * Manages static content pages with bilingual support (English/Arabic)
 * Used for About Us, Terms & Conditions, Privacy Policy, etc.
 *
 * @property int $id
 * @property string $slug
 * @property string $title_en
 * @property string $title_ar
 * @property string $content_en
 * @property string $content_ar
 * @property string|null $meta_title_en
 * @property string|null $meta_title_ar
 * @property string|null $meta_description_en
 * @property string|null $meta_description_ar
 * @property bool $is_active
 * @property int $order
 * @property bool $show_in_footer
 * @property bool $show_in_header
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'slug',
        'title_en',
        'title_ar',
        'content_en',
        'content_ar',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_active',
        'order',
        'show_in_footer',
        'show_in_header',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_header' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug from English title if not provided
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title_en);
            }
        });
    }

    /**
     * Get the page title based on current locale.
     *
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * Get the page content based on current locale.
     *
     * @return string
     */
    public function getContentAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->content_ar : $this->content_en;
    }

    /**
     * Get the meta title based on current locale.
     *
     * @return string|null
     */
    public function getMetaTitleAttribute(): ?string
    {
        $metaTitle = app()->getLocale() === 'ar' ? $this->meta_title_ar : $this->meta_title_en;
        return $metaTitle ?: $this->title;
    }

    /**
     * Get the meta description based on current locale.
     *
     * @return string|null
     */
    public function getMetaDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->meta_description_ar : $this->meta_description_en;
    }

    /**
     * Scope a query to only include active pages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order pages by their order column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope a query to only include pages shown in footer.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    /**
     * Scope a query to only include pages shown in header.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHeader($query)
    {
        return $query->where('show_in_header', true);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
