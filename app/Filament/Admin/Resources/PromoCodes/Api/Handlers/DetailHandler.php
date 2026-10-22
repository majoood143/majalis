<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PromoCodes\Api\Handlers;

use App\Filament\Admin\Resources\PromoCodeResource;
use App\Filament\Admin\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Promo Code record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'View:PromoCode';

    protected static string $transformer = PromoCodeTransformer::class;

    protected static array $allowedIncludes = ['hall', 'usages', 'bookings'];
}
