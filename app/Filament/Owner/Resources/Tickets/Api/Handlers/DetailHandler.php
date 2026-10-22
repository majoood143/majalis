<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Handlers;

use App\Filament\Api\Handlers\DetailHandler as BaseDetailHandler;
use App\Filament\Owner\Resources\TicketResource;
use App\Filament\Owner\Resources\Tickets\Api\Transformers\TicketTransformer;

/**
 * Show one Ticket record.
 */
class DetailHandler extends BaseDetailHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'View:Ticket';

    protected static bool $checkPermission = false;

    protected static string $transformer = TicketTransformer::class;

    protected static array $allowedIncludes = ['booking', 'messages'];
}
