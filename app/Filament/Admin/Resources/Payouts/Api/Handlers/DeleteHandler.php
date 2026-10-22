<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Handlers;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Payout record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'Delete:OwnerPayout';
}
