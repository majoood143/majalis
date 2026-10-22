<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HallAvailabilities\Api\Handlers;

use App\Filament\Admin\Resources\HallAvailabilities\Api\Requests\CreateHallAvailabilityRequest;
use App\Filament\Admin\Resources\HallAvailabilities\Api\Transformers\HallAvailabilityTransformer;
use App\Filament\Admin\Resources\HallAvailabilityResource;
use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Models\HallAvailability;
use Illuminate\Database\Eloquent\Model;

/**
 * Create a Hall Availability record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = HallAvailabilityResource::class;

    protected static string $permission = 'Create:HallAvailability';

    protected static string $transformer = HallAvailabilityTransformer::class;

    /**
     * Create Hall Availability
     */
    public function handler(CreateHallAvailabilityRequest $request)
    {
        return $this->store($request);
    }

    protected function beforeCreate(array $data): array
    {
        return static::validateSlot($data);
    }

    /**
     * One record per hall/date/slot; reason and notes only apply to blocked slots.
     */
    public static function validateSlot(array $data, ?Model $record = null): array
    {
        $hallId = $data['hall_id'] ?? $record?->hall_id;
        $date = $data['date'] ?? $record?->date?->toDateString();
        $slot = $data['time_slot'] ?? $record?->time_slot;

        $taken = HallAvailability::where('hall_id', $hallId)
            ->whereDate('date', $date)
            ->where('time_slot', $slot)
            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->exists();

        if ($taken) {
            static::fail('time_slot', __('hall-availability.errors.duplicate_slot_body'));
        }

        if ($data['is_available'] ?? $record?->is_available ?? true) {
            $data['reason'] = null;
            $data['notes'] = null;
        }

        return $data;
    }

    protected function afterCreate(Model $record, array $data): void
    {
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->withProperties(['source' => 'api', 'changes' => $data])
            ->log(class_basename($record) . ' Created via API');
    }
}
