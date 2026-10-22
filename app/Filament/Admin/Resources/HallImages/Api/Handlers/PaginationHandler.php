<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Handlers;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Admin\Resources\HallImages\Api\Transformers\HallImageTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Image records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallImageResource::class;

    protected static string $permission = 'ViewAny:HallImage';

    protected static string $transformer = HallImageTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'type', 'is_active', 'is_featured'];

    protected static array $allowedSorts = ['id', 'order', 'created_at'];
}
