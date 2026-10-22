<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Handlers;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Commission Setting record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = CommissionSettingResource::class;

    protected static string $permission = 'Delete:CommissionSetting';
}
