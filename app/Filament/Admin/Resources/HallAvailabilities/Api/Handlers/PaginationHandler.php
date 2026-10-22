<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Handlers;

use App\Filament\Admin\Resources\HallAvailabilities\Api\Transformers\HallAvailabilityTransformer;
use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Availability records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallAvailabilityResource::class;

    protected static string $permission = 'ViewAny:HallAvailability';

    protected static string $transformer = HallAvailabilityTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'time_slot', 'is_available'];

    protected static array $allowedSorts = ['id', 'date', 'created_at'];

    protected static array $dateFilters = ['date'];
}
