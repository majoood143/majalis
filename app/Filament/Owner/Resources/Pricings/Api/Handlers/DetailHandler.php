<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\PricingResource;
use App\Filament\Owner\Resources\Pricings\Api\Transformers\PricingTransformer;

/**
 * Show one Pricing record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PricingResource::class;

    protected static string $permission = 'View:SeasonalPricing';

    protected static bool $checkPermission = false;

    protected static string $transformer = PricingTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
