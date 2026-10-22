<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Handlers;

use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Admin\Resources\Bookings\Api\Transformers\BookingTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Booking record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'View:Booking';

    protected static string $transformer = BookingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'user', 'payments', 'extraServices', 'bookingDays', 'promoCode', 'eventTypes', 'review', 'guestSession'];
}
