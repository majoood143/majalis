<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Bookings\Api\Handlers;

use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Booking record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = BookingResource::class;

    protected static string $permission = 'Delete:Booking';
}
