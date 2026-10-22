<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodes\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\PromoCodeResource;
use App\Filament\Owner\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;

/**
 * List Promo Code records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'ViewAny:PromoCode';

    protected static bool $checkPermission = false;

    protected static string $transformer = PromoCodeTransformer::class;

    protected static array $allowedIncludes = ['hall', 'usages'];

    protected static array $allowedFilters = ['is_active', 'discount_type', 'hall_id'];

    protected static array $allowedSorts = ['id', 'created_at', 'valid_until'];

    protected static array $searchable = ['code', 'name'];
}
