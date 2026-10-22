<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Reviews\Api\Handlers;

use App\Filament\Admin\Resources\ReviewResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Review record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = ReviewResource::class;

    protected static string $permission = 'Delete:Review';
}
