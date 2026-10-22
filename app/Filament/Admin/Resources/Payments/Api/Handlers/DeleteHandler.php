<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api\Handlers;

use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Payment record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = PaymentResource::class;

    protected static string $permission = 'Delete:Payment';
}
