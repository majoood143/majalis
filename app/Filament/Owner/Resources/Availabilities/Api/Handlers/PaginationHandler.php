<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\Availabilities\Api\Transformers\AvailabilityTransformer;
use App\Filament\Owner\Resources\AvailabilityResource;

/**
 * List Availability records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = AvailabilityResource::class;

    protected static string $permission = 'ViewAny:HallAvailability';

    protected static bool $checkPermission = false;

    protected static string $transformer = AvailabilityTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'time_slot', 'is_available'];

    protected static array $allowedSorts = ['id', 'date', 'created_at'];

    protected static array $dateFilters = ['date'];
}
