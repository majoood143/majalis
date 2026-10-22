<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Handlers;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Transformers\ServiceFeeSettingTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Service Fee Setting record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = ServiceFeeSettingResource::class;

    protected static string $permission = 'View:ServiceFeeSetting';

    protected static string $transformer = ServiceFeeSettingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'owner'];
}
