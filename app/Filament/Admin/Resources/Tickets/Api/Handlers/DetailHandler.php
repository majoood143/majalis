<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Handlers;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Admin\Resources\Tickets\Api\Transformers\TicketTransformer;
use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;

/**
 * Show one Ticket record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'View:Ticket';

    protected static string $transformer = TicketTransformer::class;

    protected static array $allowedIncludes = ['user', 'assignedTo', 'booking', 'messages'];
}
