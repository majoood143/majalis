<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\EventTypes\Api\Handlers;

use App\Filament\Admin\Resources\EventTypeResource;
use App\Filament\Admin\Resources\EventTypes\Api\Requests\UpdateEventTypeRequest;
use App\Filament\Admin\Resources\EventTypes\Api\Transformers\EventTypeTransformer;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;

/**
 * Update a Event Type record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = EventTypeResource::class;

    protected static string $permission = 'Update:EventType';

    protected static string $transformer = EventTypeTransformer::class;

    /**
     * Update Event Type
     */
    public function handler(UpdateEventTypeRequest $request)
    {
        return $this->modify($request);
    }
}
