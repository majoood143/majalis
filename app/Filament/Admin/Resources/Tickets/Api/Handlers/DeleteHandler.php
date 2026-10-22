<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Handlers;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Api\Handlers\DeleteHandler as BaseDeleteHandler;

/**
 * Delete a Ticket record.
 */
class DeleteHandler extends BaseDeleteHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'Delete:Ticket';
}
