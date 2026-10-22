<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Regions\Api\Handlers;

use App\Filament\Admin\Resources\RegionResource;
use App\Filament\Admin\Resources\Regions\Api\Transformers\RegionTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Region records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = RegionResource::class;

    protected static string $permission = 'ViewAny:Region';

    protected static string $transformer = RegionTransformer::class;

    protected static array $allowedIncludes = ['cities', 'halls'];

    protected static array $allowedFilters = ['is_active'];

    protected static array $allowedSorts = ['id', 'order'];

    protected static array $searchable = ['name->en', 'name->ar', 'code'];
}
