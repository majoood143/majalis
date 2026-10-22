<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Handlers;

use App\Filament\Admin\Resources\HallCategories\Api\Transformers\HallCategoryTransformer;
use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Category records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallCategoryResource::class;

    protected static string $permission = 'ViewAny:HallCategory';

    protected static string $transformer = HallCategoryTransformer::class;

    protected static array $allowedIncludes = ['hallTypes'];

    protected static array $allowedSorts = ['id', 'sort_order'];

    protected static array $searchable = ['name->en', 'name->ar', 'slug'];
}
