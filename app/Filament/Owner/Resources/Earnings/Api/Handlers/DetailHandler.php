<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Earnings\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\Earnings\Api\Transformers\EarningsTransformer;
use App\Filament\Owner\Resources\EarningsResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Show one Earnings record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = EarningsResource::class;

    protected static string $permission = 'View:Booking';

    protected static bool $checkPermission = false;

    protected static string $transformer = EarningsTransformer::class;

    protected static array $allowedIncludes = ['hall', 'extraServices'];

    /**
     * Paid, confirmed/completed bookings of the owner's halls (the panel scopes this in table()).
     */
    protected static function scopeQuery(Builder $query): Builder
    {
        return $query
            ->whereHas('hall', fn (Builder $hall) => $hall->where('owner_id', auth()->id()))
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('payment_status', 'paid');
    }
}
