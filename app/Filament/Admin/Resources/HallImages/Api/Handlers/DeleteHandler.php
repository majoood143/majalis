<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallImages\Api\Handlers;

use App\Filament\Admin\Resources\HallImageResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Hall Image record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = HallImageResource::class;

    protected static string $permission = 'Delete:HallImage';
}
