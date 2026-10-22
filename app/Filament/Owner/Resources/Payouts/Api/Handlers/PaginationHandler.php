<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Payouts\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\PayoutResource;
use App\Filament\Owner\Resources\Payouts\Api\Transformers\PayoutTransformer;
use Illuminate\Database\Eloquent\Builder;

/**
 * List Payout records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'ViewAny:OwnerPayout';

    protected static bool $checkPermission = false;

    protected static string $transformer = PayoutTransformer::class;

    protected static array $allowedIncludes = ['processor'];

    protected static array $allowedFilters = ['status'];

    protected static array $allowedSorts = ['id', 'created_at', 'net_payout', 'period_start'];

    protected static array $searchable = ['payout_number'];

    protected static array $dateFilters = ['period_start', 'created_at'];

    /**
     * Only this owner's payouts (the panel scopes this in table()).
     */
    protected static function scopeQuery(Builder $query): Builder
    {
        return $query->where('owner_id', auth()->id());
    }
}
