<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Handlers;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Admin\Resources\Payouts\Api\Transformers\PayoutTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Payout record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'View:OwnerPayout';

    protected static string $transformer = PayoutTransformer::class;

    protected static array $allowedIncludes = ['owner', 'processor', 'hallOwner'];
}
