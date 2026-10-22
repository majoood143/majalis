<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api\Handlers;

use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Admin\Resources\Payments\Api\Transformers\PaymentTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Payment records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = PaymentResource::class;

    protected static string $permission = 'ViewAny:Payment';

    protected static string $transformer = PaymentTransformer::class;

    protected static array $allowedIncludes = ['booking'];

    protected static array $allowedFilters = ['status', 'booking_id', 'payment_method', 'currency'];

    protected static array $allowedSorts = ['id', 'amount', 'paid_at', 'created_at'];

    protected static array $searchable = ['payment_reference', 'transaction_id'];

    protected static array $dateFilters = ['created_at', 'paid_at'];
}
