<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Transformers;

use App\Filament\Api\Transformers\Transformer;
use App\Models\Booking;

/**
 * @property Booking $resource
 */
class BookingTransformer extends Transformer
{
    protected array $except = ['guest_token'];
}
