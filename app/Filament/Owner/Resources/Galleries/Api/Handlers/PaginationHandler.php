<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Galleries\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\Galleries\Api\Transformers\GalleryTransformer;
use App\Filament\Owner\Resources\GalleryResource;

/**
 * List Gallery records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = GalleryResource::class;

    protected static string $permission = 'ViewAny:HallImage';

    protected static bool $checkPermission = false;

    protected static string $transformer = GalleryTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'type', 'is_active', 'is_featured'];

    protected static array $allowedSorts = ['id', 'order', 'created_at'];
}
