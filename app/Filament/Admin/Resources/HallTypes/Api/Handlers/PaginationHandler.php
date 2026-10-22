<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallTypes\Api\Handlers;

use App\Filament\Admin\Resources\HallTypeResource;
use App\Filament\Admin\Resources\HallTypes\Api\Transformers\HallTypeTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Type records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallTypeResource::class;

    protected static string $permission = 'ViewAny:HallType';

    protected static string $transformer = HallTypeTransformer::class;

    protected static array $allowedIncludes = ['category', 'halls'];

    protected static array $allowedFilters = ['category_id', 'is_active'];

    protected static array $allowedSorts = ['id', 'sort_order'];

    protected static array $searchable = ['name->en', 'name->ar', 'slug'];
}
