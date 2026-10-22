<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\GuestSessions\Api\Handlers;

use App\Filament\Admin\Resources\GuestSessionResource;
use App\Filament\Admin\Resources\GuestSessions\Api\Transformers\GuestSessionTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Guest Session records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = GuestSessionResource::class;

    protected static string $permission = 'ViewAny:GuestSession';

    protected static string $transformer = GuestSessionTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking'];

    protected static array $allowedFilters = ['status', 'is_verified', 'hall_id', 'booking_id'];

    protected static array $allowedSorts = ['id', 'created_at'];

    protected static array $searchable = ['email', 'name', 'phone'];

    protected static array $dateFilters = ['created_at'];
}
