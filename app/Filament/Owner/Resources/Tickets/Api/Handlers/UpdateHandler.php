<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\TicketResource;
use App\Filament\Owner\Resources\Tickets\Api\Requests\UpdateTicketRequest;
use App\Filament\Owner\Resources\Tickets\Api\Transformers\TicketTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Ticket record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'Update:Ticket';

    protected static bool $checkPermission = false;

    protected static string $transformer = TicketTransformer::class;

    /**
     * Update Ticket
     */
    public function handler(UpdateTicketRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::stampStatus($data, $record);
    }
}
