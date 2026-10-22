<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallOwners\Api\Handlers;

use App\Filament\Admin\Resources\HallOwnerResource;
use App\Filament\Admin\Resources\HallOwners\Api\Transformers\HallOwnerTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Hall Owner record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = HallOwnerResource::class;

    protected static string $permission = 'View:HallOwner';

    protected static string $transformer = HallOwnerTransformer::class;

    protected static array $allowedIncludes = ['user', 'halls', 'verifiedBy'];
}
