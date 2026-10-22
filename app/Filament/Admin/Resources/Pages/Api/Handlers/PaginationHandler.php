<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Pages\Api\Handlers;

use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\Pages\Api\Transformers\PageTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Page records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PageResource::class;

    protected static string $permission = 'ViewAny:Page';

    protected static string $transformer = PageTransformer::class;

    protected static array $allowedFilters = ['is_active', 'show_in_footer', 'show_in_header'];

    protected static array $allowedSorts = ['id', 'order', 'created_at'];

    protected static array $searchable = ['slug', 'title_en', 'title_ar'];
}
