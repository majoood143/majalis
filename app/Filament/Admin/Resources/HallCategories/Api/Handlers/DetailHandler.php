<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallCategories\Api\Handlers;

use App\Filament\Admin\Resources\HallCategories\Api\Transformers\HallCategoryTransformer;
use App\Filament\Admin\Resources\HallCategoryResource;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Category record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallCategoryResource::class;

    protected static string $permission = 'View:HallCategory';

    protected static string $transformer = HallCategoryTransformer::class;

    protected static array $allowedIncludes = ['hallTypes'];
}
