<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BookingExtraService Model
 * 
 * Represents an extra service added to a booking.
 * Stores a snapshot of the service details at the time of booking.
 * 
 * @property int $id
 * @property int $booking_id
 * @property int $extra_service_id
 * @property array|string $service_name
 * @property float $unit_price
 * @property int $quantity
 * @property float $total_price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read Booking $booking
 * @property-read ExtraService $extraService
 */
class BookingExtraService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booking_extra_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'extra_service_id',
        'service_name',
        'unit_price',
        'quantity',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'service_name' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Get the booking this service belongs to.
     *
     * @return BelongsTo<Booking, BookingExtraService>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the original extra service.
     *
     * @return BelongsTo<ExtraService, BookingExtraService>
     */
    public function extraService(): BelongsTo
    {
        return $this->belongsTo(ExtraService::class);
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    /**
     * Get the localized service name.
     *
     * @return string
     */
    public function getLocalizedNameAttribute(): string
    {
        $name = $this->service_name;
        
        // Handle JSON string
        if (is_string($name)) {
            $decoded = json_decode($name, true);
            if (is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? $name;
            }
            // Remove surrounding quotes if present
            return trim($name, '"');
        }
        
        // Handle array
        if (is_array($name)) {
            return $name[app()->getLocale()] ?? $name['en'] ?? (string) array_values($name)[0];
        }
        
        return (string) $name;
    }
}
