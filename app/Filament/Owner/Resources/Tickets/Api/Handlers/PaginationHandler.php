<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Handlers;

use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;
use App\Filament\Owner\Resources\TicketResource;
use App\Filament\Owner\Resources\Tickets\Api\Transformers\TicketTransformer;

/**
 * List Ticket records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'ViewAny:Ticket';

    protected static bool $checkPermission = false;

    protected static string $transformer = TicketTransformer::class;

    protected static array $allowedIncludes = ['booking', 'messages'];

    protected static array $allowedFilters = ['type', 'priority', 'status', 'booking_id'];

    protected static array $allowedSorts = ['id', 'created_at', 'priority'];

    protected static array $searchable = ['ticket_number', 'subject'];

    protected static array $dateFilters = ['created_at'];
}
