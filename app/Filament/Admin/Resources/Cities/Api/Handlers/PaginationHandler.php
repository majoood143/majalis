<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Cities\Api\Handlers;

use App\Filament\Admin\Resources\Cities\Api\Transformers\CityTransformer;
use App\Filament\Admin\Resources\CityResource;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List City records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = CityResource::class;

    protected static string $permission = 'ViewAny:City';

    protected static string $transformer = CityTransformer::class;

    protected static array $allowedIncludes = ['region', 'halls'];

    protected static array $allowedFilters = ['region_id', 'is_active'];

    protected static array $allowedSorts = ['id', 'order', 'created_at'];

    protected static array $searchable = ['name->en', 'name->ar', 'code'];
}
