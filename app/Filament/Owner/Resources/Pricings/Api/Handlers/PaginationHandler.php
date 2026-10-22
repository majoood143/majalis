<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\PricingResource;
use App\Filament\Owner\Resources\Pricings\Api\Transformers\PricingTransformer;

/**
 * List Pricing records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PricingResource::class;

    protected static string $permission = 'ViewAny:SeasonalPricing';

    protected static bool $checkPermission = false;

    protected static string $transformer = PricingTransformer::class;

    protected static array $allowedIncludes = ['hall'];

    protected static array $allowedFilters = ['hall_id', 'type', 'is_active', 'is_recurring'];

    protected static array $allowedSorts = ['id', 'priority', 'start_date', 'created_at'];

    protected static array $searchable = ['name->en', 'name->ar'];

    protected static array $dateFilters = ['start_date', 'end_date'];
}
