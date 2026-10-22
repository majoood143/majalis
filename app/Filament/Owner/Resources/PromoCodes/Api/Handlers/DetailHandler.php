<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\PromoCodes\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\PromoCodeResource;
use App\Filament\Owner\Resources\PromoCodes\Api\Transformers\PromoCodeTransformer;

/**
 * Show one Promo Code record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PromoCodeResource::class;

    protected static string $permission = 'View:PromoCode';

    protected static bool $checkPermission = false;

    protected static string $transformer = PromoCodeTransformer::class;

    protected static array $allowedIncludes = ['hall', 'usages'];
}
