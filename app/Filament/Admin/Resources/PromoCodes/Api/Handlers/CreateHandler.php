<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api\Handlers;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Admin\Resources\PromoCodes\Api\Requests\CreatePromoCodeRequest;
use App\Filament\Admin\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Create a Promo Code record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'Create:PromoCode';

    protected static string $transformer = PromoCodeTransformer::class;

    /**
     * Create Promo Code
     */
    public function handler(CreatePromoCodeRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        $data['created_by_type'] = 'admin';
        $data['created_by_id'] = auth()->id();

        return static::validatePromo($data);
    }

    public static function validatePromo(array $data, ?Model $record = null): array
    {
        $type = $data['discount_type'] ?? $record?->discount_type;
        $value = $data['discount_value'] ?? $record?->discount_value;

        if ($type === 'percentage' && $value > 100) {
            static::fail('discount_value', __('Percentage cannot exceed 100%.'));
        }

        $from = $data['valid_from'] ?? $record?->valid_from;
        $until = $data['valid_until'] ?? $record?->valid_until;

        if ($from && $until && Carbon::parse($from)->gt(Carbon::parse($until))) {
            static::fail('valid_until', __('The end date must be after the start date.'));
        }

        return $data;
    }
}
