<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api\Handlers;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Admin\Resources\PromoCodes\Api\Requests\UpdatePromoCodeRequest;
use App\Filament\Admin\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Promo Code record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'Update:PromoCode';

    protected static string $transformer = PromoCodeTransformer::class;

    /**
     * Update Promo Code
     */
    public function handler(UpdatePromoCodeRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::validatePromo($data, $record);
    }
}
