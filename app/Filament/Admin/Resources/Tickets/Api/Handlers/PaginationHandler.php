<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Handlers;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Admin\Resources\Tickets\Api\Transformers\TicketTransformer;
use App\Filament\Api\Handlers\PaginationHandler as BasePaginationHandler;

/**
 * List Ticket records.
 */
class PaginationHandler extends BasePaginationHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'ViewAny:Ticket';

    protected static string $transformer = TicketTransformer::class;

    protected static array $allowedIncludes = ['user', 'assignedTo', 'booking', 'messages'];

    protected static array $allowedFilters = ['type', 'priority', 'status', 'user_id', 'assigned_to', 'booking_id'];

    protected static array $allowedSorts = ['id', 'created_at', 'priority', 'due_date'];

    protected static array $searchable = ['ticket_number', 'subject'];

    protected static array $dateFilters = ['created_at', 'due_date'];
}
