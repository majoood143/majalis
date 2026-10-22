<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Tickets\Api\Handlers;

use App\Filament\Admin\Resources\TicketResource;
use App\Filament\Admin\Resources\Tickets\Api\Requests\UpdateTicketRequest;
use App\Filament\Admin\Resources\Tickets\Api\Transformers\TicketTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Ticket record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'Update:Ticket';

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
