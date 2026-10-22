<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Handlers;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Admin\Resources\HallOwners\Api\Transformers\HallOwnerTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Hall Owner records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = HallOwnerResource::class;

    protected static string $permission = 'ViewAny:HallOwner';

    protected static string $transformer = HallOwnerTransformer::class;

    protected static array $allowedIncludes = ['user', 'halls', 'verifiedBy'];

    protected static array $allowedFilters = ['is_verified', 'is_active', 'user_id'];

    protected static array $allowedSorts = ['id', 'created_at', 'business_name'];

    protected static array $searchable = ['business_name', 'business_name_ar', 'commercial_registration', 'business_email', 'business_phone'];
}
