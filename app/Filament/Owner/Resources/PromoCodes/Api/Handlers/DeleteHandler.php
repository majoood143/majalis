<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodes\Api\Handlers;

use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;
use App\Filament\Owner\Resources\PromoCodeResource;

/**
 * Delete a Promo Code record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'Delete:PromoCode';

    protected static bool $checkPermission = false;
}
