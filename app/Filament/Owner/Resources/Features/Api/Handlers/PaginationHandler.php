<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Features\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\FeatureResource;
use App\Filament\Owner\Resources\Features\Api\Transformers\FeatureTransformer;

/**
 * List Feature records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = FeatureResource::class;

    protected static string $permission = 'ViewAny:HallFeature';

    protected static bool $checkPermission = false;

    protected static string $transformer = FeatureTransformer::class;

    protected static array $allowedSorts = ['id', 'order'];

    protected static array $searchable = ['name->en', 'name->ar'];

    protected static string $defaultSort = 'order';
}
