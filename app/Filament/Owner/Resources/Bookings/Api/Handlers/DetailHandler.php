<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Bookings\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\BookingResource;
use App\Filament\Owner\Resources\Bookings\Api\Transformers\BookingTransformer;

/**
 * Show one Booking record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'View:Booking';

    protected static bool $checkPermission = false;

    protected static string $transformer = BookingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'extraServices', 'bookingDays', 'payments', 'eventTypes', 'review'];
}
