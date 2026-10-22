<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api\Handlers;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Admin\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Promo Code records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'ViewAny:PromoCode';

    protected static string $transformer = PromoCodeTransformer::class;

    protected static array $allowedIncludes = ['hall', 'usages', 'bookings'];

    protected static array $allowedFilters = ['is_active', 'discount_type', 'hall_id', 'created_by_type'];

    protected static array $allowedSorts = ['id', 'created_at', 'valid_until'];

    protected static array $searchable = ['code', 'name'];
}
