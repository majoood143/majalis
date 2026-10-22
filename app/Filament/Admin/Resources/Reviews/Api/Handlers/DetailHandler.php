<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Handlers;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Admin\Resources\Reviews\Api\Transformers\ReviewTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Review record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = ReviewResource::class;

    protected static string $permission = 'View:Review';

    protected static string $transformer = ReviewTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking', 'user'];
}
