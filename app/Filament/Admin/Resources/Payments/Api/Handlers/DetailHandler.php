<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Payments\Api\Handlers;

use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Admin\Resources\Payments\Api\Transformers\PaymentTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Payment record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = PaymentResource::class;

    protected static string $permission = 'View:Payment';

    protected static string $transformer = PaymentTransformer::class;

    protected static array $allowedIncludes = ['booking'];
}
