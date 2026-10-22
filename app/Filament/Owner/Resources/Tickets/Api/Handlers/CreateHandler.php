<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Tickets\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\TicketResource;
use App\Filament\Owner\Resources\Tickets\Api\Requests\CreateTicketRequest;
use App\Filament\Owner\Resources\Tickets\Api\Transformers\TicketTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Ticket record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = TicketResource::class;

    protected static string $permission = 'Create:Ticket';

    protected static bool $checkPermission = false;

    protected static string $transformer = TicketTransformer::class;

    /**
     * Create Ticket
     */
    public function handler(CreateTicketRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return static::stampStatus($data);
    }

    /**
     * Stamp resolved_at / closed_at when the status moves there.
     */
    public static function stampStatus(array $data, ?Model $record = null): array
    {
        $status = $data['status'] ?? null;

        if ($status === 'resolved' && ! $record?->resolved_at) {
            $data['resolved_at'] = now();
        }

        if ($status === 'closed' && ! $record?->closed_at) {
            $data['closed_at'] = now();
        }

        return $data;
    }
}
