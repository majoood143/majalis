<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Handlers;

use App\Filament\Admin\Resources\HallAvailabilities\Api\Requests\UpdateHallAvailabilityRequest;
use App\Filament\Admin\Resources\HallAvailabilities\Api\Transformers\HallAvailabilityTransformer;
use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Hall Availability record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = HallAvailabilityResource::class;

    protected static string $permission = 'Update:HallAvailability';

    protected static string $transformer = HallAvailabilityTransformer::class;

    /**
     * Update Hall Availability
     */
    public function handler(UpdateHallAvailabilityRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        return CreateHandler::validateSlot($data, $record);
    }

    protected function afterUpdate(Model $record, array $data): void
    {
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Updated via API');
    }
}
