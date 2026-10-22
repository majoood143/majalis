<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\PricingResource;

/**
 * Delete a Pricing record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PricingResource::class;

    protected static string $permission = 'Delete:SeasonalPricing';

    protected static bool $checkPermission = false;
}
