<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Handlers;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\Users\Api\Transformers\UserTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List User records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = UserResource::class;

    protected static string $permission = 'ViewAny:User';

    protected static string $transformer = UserTransformer::class;

    protected static array $allowedIncludes = ['hallOwner', 'roles', 'halls'];

    protected static array $allowedFilters = ['role', 'is_active'];

    protected static array $allowedSorts = ['id', 'name', 'created_at'];

    protected static array $searchable = ['name', 'email', 'phone'];
}
