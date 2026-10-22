<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Api\Handlers;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\Users\Api\Transformers\UserTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one User record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = UserResource::class;

    protected static string $permission = 'View:User';

    protected static string $transformer = UserTransformer::class;

    protected static array $allowedIncludes = ['hallOwner', 'roles', 'halls'];
}
