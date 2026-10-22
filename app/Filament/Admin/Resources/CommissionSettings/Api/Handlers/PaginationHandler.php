<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommissionSettings\Api\Handlers;

use App\Filament\Admin\Resources\CommissionSettingResource;
use App\Filament\Admin\Resources\CommissionSettings\Api\Transformers\CommissionSettingTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Commission Setting records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = CommissionSettingResource::class;

    protected static string $permission = 'ViewAny:CommissionSetting';

    protected static string $transformer = CommissionSettingTransformer::class;

    protected static array $allowedIncludes = ['hall', 'owner'];

    protected static array $allowedFilters = ['hall_id', 'owner_id', 'commission_type', 'is_active'];

    protected static array $allowedSorts = ['id', 'created_at', 'effective_from'];
}
