<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Pricings\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\PricingResource;
use App\Filament\Owner\Resources\Pricings\Api\Requests\CreatePricingRequest;
use App\Filament\Owner\Resources\Pricings\Api\Transformers\PricingTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Create a Pricing record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = PricingResource::class;

    protected static string $permission = 'Create:SeasonalPricing';

    protected static bool $checkPermission = false;

    protected static string $transformer = PricingTransformer::class;

    /**
     * Create Pricing
     */
    public function handler(CreatePricingRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::normalizePricing($data);
    }

    public static function normalizePricing(array $data, ?Model $record = null): array
    {
        if (array_key_exists('apply_to_slots', $data) && empty($data['apply_to_slots'])) {
            $data['apply_to_slots'] = null;
        }

        if (! empty($data['days_of_week'])) {
            $data['days_of_week'] = array_map('intval', $data['days_of_week']);
        }

        $start = $data['start_date'] ?? $record?->start_date;
        $end = $data['end_date'] ?? $record?->end_date;

        if ($start && $end && Carbon::parse($start)->gt(Carbon::parse($end))) {
            static::fail('end_date', __('The end date must be after the start date.'));
        }

        return $data;
    }
}
