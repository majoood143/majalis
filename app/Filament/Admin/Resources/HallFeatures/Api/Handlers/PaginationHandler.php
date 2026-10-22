<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallFeatures\Api\Handlers;

use App\Filament\Admin\Resources\HallFeatureResource;
use App\Filament\Admin\Resources\HallFeatures\Api\Transformers\HallFeatureTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Feature records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallFeatureResource::class;

    protected static string $permission = 'ViewAny:HallFeature';

    protected static string $transformer = HallFeatureTransformer::class;

    protected static array $allowedFilters = ['is_active'];

    protected static array $allowedSorts = ['id', 'order'];

    protected static array $searchable = ['name->en', 'name->ar', 'slug'];
}
