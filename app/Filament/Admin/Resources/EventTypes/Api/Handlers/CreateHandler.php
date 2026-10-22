<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Handlers;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Admin\Resources\EventTypes\Api\Requests\CreateEventTypeRequest;
use App\Filament\Admin\Resources\EventTypes\Api\Transformers\EventTypeTransformer;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;

/**
 * Create a Event Type record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = EventTypeResource::class;

    protected static string $permission = 'Create:EventType';

    protected static string $transformer = EventTypeTransformer::class;

    /**
     * Create Event Type
     */
    public function handler(CreateEventTypeRequest $request)
    {
        return $this->store($request);
    }
}
