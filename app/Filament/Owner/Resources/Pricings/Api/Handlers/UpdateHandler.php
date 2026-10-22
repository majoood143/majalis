<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\PricingResource;
use App\Filament\Owner\Resources\Pricings\Api\Requests\UpdatePricingRequest;
use App\Filament\Owner\Resources\Pricings\Api\Transformers\PricingTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Pricing record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = PricingResource::class;

    protected static string $permission = 'Update:SeasonalPricing';

    protected static bool $checkPermission = false;

    protected static string $transformer = PricingTransformer::class;

    /**
     * Update Pricing
     */
    public function handler(UpdatePricingRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::normalizePricing($data, $record);
    }
}
