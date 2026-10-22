<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Handlers;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Admin\Resources\EventTypes\Api\Transformers\EventTypeTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Event Type records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = EventTypeResource::class;

    protected static string $permission = 'ViewAny:EventType';

    protected static string $transformer = EventTypeTransformer::class;

    protected static array $allowedIncludes = ['halls'];

    protected static array $allowedFilters = ['is_active'];

    protected static array $allowedSorts = ['id', 'sort_order'];

    protected static array $searchable = ['name->en', 'name->ar'];
}
