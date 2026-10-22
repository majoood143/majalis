<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ExtraServices\Api\Handlers;

use App\Filament\Admin\Resources\ExtraServiceResource;
use App\Filament\Admin\Resources\ExtraServices\Api\Transformers\ExtraServiceTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Extra Service records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = ExtraServiceResource::class;

    protected static string $permission = 'ViewAny:ExtraService';

    protected static string $transformer = ExtraServiceTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'is_active', 'is_required', 'unit'];

    protected static array $allowedSorts = ['id', 'order', 'price'];

    protected static array $searchable = ['name->en', 'name->ar'];
}
