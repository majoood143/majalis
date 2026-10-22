<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Halls\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\HallResource;
use App\Filament\Owner\Resources\Halls\Api\Transformers\HallTransformer;

/**
 * List Hall records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'ViewAny:Hall';

    protected static bool $checkPermission = false;

    protected static string $transformer = HallTransformer::class;

    protected static array $allowedIncludes = ['city', 'region', 'images', 'extraServices', 'reviews', 'availabilities', 'hallTypes', 'eventTypes'];

    protected static array $allowedFilters = ['city_id', 'region_id', 'is_active', 'is_listed'];

    protected static array $allowedSorts = ['id', 'created_at', 'price_per_slot'];

    protected static array $searchable = ['name->en', 'name->ar', 'slug'];
}
