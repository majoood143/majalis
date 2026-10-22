<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Handlers;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Admin\Resources\CommissionSettings\Api\Transformers\CommissionSettingTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Commission Setting record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = CommissionSettingResource::class;

    protected static string $permission = 'View:CommissionSetting';

    protected static string $transformer = CommissionSettingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'owner'];
}
