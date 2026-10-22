<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\GuestSessions\Api\Handlers;

use App\Filament\Admin\Resources\GuestSessionResource;
use App\Filament\Admin\Resources\GuestSessions\Api\Transformers\GuestSessionTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Guest Session record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = GuestSessionResource::class;

    protected static string $permission = 'View:GuestSession';

    protected static string $transformer = GuestSessionTransformer::class;

    protected static array $allowedIncludes = ['hall', 'booking'];
}
