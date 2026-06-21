<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BookingDay Model
 *
 * One row per day in a (potentially multi-day) Booking. Ordinary single-day
 * bookings have exactly one row; multi-day bookings have up to 3.
 *
 * @property int    $id
 * @property int    $booking_id
 * @property int    $hall_id
 * @property Carbon $date
 * @property string $time_slot
 * @property float  $price
 *
 * @property-read Booking $booking
 * @property-read Hall    $hall
 */
class BookingDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'hall_id',
        'date',
        'time_slot',
        'price',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }
}
