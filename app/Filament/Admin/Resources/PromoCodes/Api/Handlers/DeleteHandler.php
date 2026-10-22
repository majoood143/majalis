<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api\Handlers;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Promo Code record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'Delete:PromoCode';
}
