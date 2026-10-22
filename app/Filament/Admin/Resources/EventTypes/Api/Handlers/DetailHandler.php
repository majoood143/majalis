<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Handlers;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Admin\Resources\EventTypes\Api\Transformers\EventTypeTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Event Type record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = EventTypeResource::class;

    protected static string $permission = 'View:EventType';

    protected static string $transformer = EventTypeTransformer::class;

    protected static array $allowedIncludes = ['halls'];
}
