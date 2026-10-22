<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Handlers;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Admin\Resources\HallImages\Api\Transformers\HallImageTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Image record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallImageResource::class;

    protected static string $permission = 'View:HallImage';

    protected static string $transformer = HallImageTransformer::class;

    protected static array $allowedIncludes = ['hall'];
}
