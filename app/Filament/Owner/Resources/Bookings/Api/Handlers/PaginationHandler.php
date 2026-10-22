<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Bookings\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\BookingResource;
use App\Filament\Owner\Resources\Bookings\Api\Transformers\BookingTransformer;

/**
 * List Booking records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'ViewAny:Booking';

    protected static bool $checkPermission = false;

    protected static string $transformer = BookingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'extraServices', 'bookingDays', 'payments', 'eventTypes', 'review'];

    protected static array $allowedFilters = ['status', 'payment_status', 'hall_id', 'time_slot', 'payment_type'];

    protected static array $allowedSorts = ['id', 'booking_date', 'created_at', 'total_amount'];

    protected static array $searchable = ['booking_number', 'customer_name', 'customer_email', 'customer_phone'];

    protected static array $dateFilters = ['booking_date', 'created_at'];
}
