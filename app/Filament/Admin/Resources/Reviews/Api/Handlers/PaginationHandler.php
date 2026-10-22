<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Handlers;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Admin\Resources\Reviews\Api\Transformers\ReviewTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Review records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = ReviewResource::class;

    protected static string $permission = 'ViewAny:Review';

    protected static string $transformer = ReviewTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking', 'user'];

    protected static array $allowedFilters = ['hall_id', 'user_id', 'booking_id', 'rating', 'is_approved', 'is_featured'];

    protected static array $allowedSorts = ['id', 'rating', 'created_at'];

    protected static array $searchable = ['comment'];
}
