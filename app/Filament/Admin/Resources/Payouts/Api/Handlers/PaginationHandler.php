<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payouts\Api\Handlers;

use App\Filament\Admin\Resources\PayoutResource;
use App\Filament\Admin\Resources\Payouts\Api\Transformers\PayoutTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Payout records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'ViewAny:OwnerPayout';

    protected static string $transformer = PayoutTransformer::class;

    protected static array $allowedIncludes = ['owner', 'processor', 'hallOwner'];

    protected static array $allowedFilters = ['owner_id', 'status'];

    protected static array $allowedSorts = ['id', 'created_at', 'net_payout', 'period_start'];

    protected static array $searchable = ['payout_number', 'transaction_reference'];

    protected static array $dateFilters = ['period_start', 'period_end', 'created_at'];
}
