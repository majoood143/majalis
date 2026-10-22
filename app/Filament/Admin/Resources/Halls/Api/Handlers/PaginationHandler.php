<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Halls\Api\Handlers;

use App\Filament\Admin\Resources\HallResource;
use App\Filament\Admin\Resources\Halls\Api\Transformers\HallTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallResource::class;

    protected static string $permission = 'ViewAny:Hall';

    protected static string $transformer = HallTransformer::class;

    protected static array $allowedIncludes = ['city', 'region', 'owner', 'images', 'extraServices', 'reviews', 'hallTypes', 'eventTypes', 'availabilities'];

    protected static array $allowedFilters = ['city_id', 'region_id', 'owner_id', 'is_active', 'is_featured', 'is_listed'];

    protected static array $allowedSorts = ['id', 'created_at', 'price_per_slot', 'capacity_max', 'average_rating', 'total_bookings'];

    protected static array $searchable = ['name->en', 'name->ar', 'slug', 'address'];
}
