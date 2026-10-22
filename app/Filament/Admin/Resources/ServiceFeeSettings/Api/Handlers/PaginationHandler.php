<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ServiceFeeSettings\Api\Handlers;

use App\Filament\Admin\Resources\ServiceFeeSettingResource;
use App\Filament\Admin\Resources\ServiceFeeSettings\Api\Transformers\ServiceFeeSettingTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Service Fee Setting records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = ServiceFeeSettingResource::class;

    protected static string $permission = 'ViewAny:ServiceFeeSetting';

    protected static string $transformer = ServiceFeeSettingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'owner'];

    protected static array $allowedFilters = ['hall_id', 'owner_id', 'fee_type', 'is_active'];

    protected static array $allowedSorts = ['id', 'created_at', 'effective_from'];
}
