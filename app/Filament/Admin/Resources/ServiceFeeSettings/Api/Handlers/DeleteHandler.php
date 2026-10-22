<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Handlers;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Service Fee Setting record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = ServiceFeeSettingResource::class;

    protected static string $permission = 'Delete:ServiceFeeSetting';
}
