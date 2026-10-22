<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Handlers;

use App\Filament\Api\Handlers\UpdateHandler as BaseUpdateHandler;
use App\Filament\Owner\Resources\Availabilities\Api\Requests\UpdateAvailabilityRequest;
use App\Filament\Owner\Resources\Availabilities\Api\Transformers\AvailabilityTransformer;
use App\Filament\Owner\Resources\AvailabilityResource;
use App\Models\HallAvailability;
use Illuminate\Database\Eloquent\Model;

/**
 * Update a Availability record.
 */
class UpdateHandler extends BaseUpdateHandler
{
    public static ?string $resource = AvailabilityResource::class;

    protected static string $permission = 'Update:HallAvailability';

    protected static bool $checkPermission = false;

    protected static string $transformer = AvailabilityTransformer::class;

    /**
     * Update Availability
     */
    public function handler(UpdateAvailabilityRequest $request)
    {
        return $this->modify($request);
    }

    protected function beforeUpdate(Model $record, array $data): array
    {
        $taken = HallAvailability::where('hall_id', $data['hall_id'] ?? $record->hall_id)
            ->whereDate('date', $data['date'] ?? $record->date)
            ->where('time_slot', $data['time_slot'] ?? $record->time_slot)
            ->whereKeyNot($record->getKey())
            ->exists();

        if ($taken) {
            static::fail('time_slot', __('hall-availability.errors.duplicate_slot_body'));
        }

        if ($data['is_available'] ?? false) {
            $data['reason'] = null;
        }

        return $data;
    }
}
