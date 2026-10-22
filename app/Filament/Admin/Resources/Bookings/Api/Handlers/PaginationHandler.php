<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Handlers;

use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Admin\Resources\Bookings\Api\Transformers\BookingTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Booking records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'ViewAny:Booking';

    protected static string $transformer = BookingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'user', 'payments', 'extraServices', 'bookingDays', 'promoCode', 'eventTypes', 'review', 'guestSession'];

    protected static array $allowedFilters = ['status', 'payment_status', 'hall_id', 'user_id', 'time_slot', 'payment_type', 'is_guest_booking', 'promo_code_id'];

    protected static array $allowedSorts = ['id', 'booking_date', 'created_at', 'total_amount'];

    protected static array $searchable = ['booking_number', 'customer_name', 'customer_email', 'customer_phone'];

    protected static array $dateFilters = ['booking_date', 'created_at'];
}
