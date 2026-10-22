<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Payouts\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\PayoutResource;
use App\Filament\Owner\Resources\Payouts\Api\Transformers\PayoutTransformer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Show one Payout record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PayoutResource::class;

    protected static string $permission = 'View:OwnerPayout';

    protected static bool $checkPermission = false;

    protected static string $transformer = PayoutTransformer::class;

    protected static array $allowedIncludes = ['processor'];

    /**
     * Only this owner's payouts (the panel scopes this in table()).
     */
    protected static function scopeQuery(Builder $query): Builder
    {
        return $query->where('owner_id', auth()->id());
    }
}
