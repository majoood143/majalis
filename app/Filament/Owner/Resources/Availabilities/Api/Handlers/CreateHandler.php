<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources\Availabilities\Api\Handlers;

use App\Filament\Api\Handlers\CreateHandler as BaseCreateHandler;
use App\Filament\Owner\Resources\Availabilities\Api\Requests\CreateAvailabilityRequest;
use App\Filament\Owner\Resources\Availabilities\Api\Transformers\AvailabilityTransformer;
use App\Filament\Owner\Resources\AvailabilityResource;
use App\Models\HallAvailability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Create a Availability record.
 */
class CreateHandler extends BaseCreateHandler
{
    public static ?string $resource = AvailabilityResource::class;

    protected static string $permission = 'Create:HallAvailability';

    protected static bool $checkPermission = false;

    protected static string $transformer = AvailabilityTransformer::class;

    /**
     * Create availability for a date or date range and one or more time slots.
     *
     * Existing records for the same hall/date/slot are updated (same as the panel).
     * Returns every created or updated record.
     */
    public function handler(CreateAvailabilityRequest $request)
    {
        $data = $request->validated();

        $start = Carbon::parse($data['date'] ?? $data['start_date']);
        $end = Carbon::parse($data['date'] ?? $data['end_date']);
        $slots = $data['time_slots'] ?? ['morning', 'afternoon', 'evening', 'full_day'];
        $available = $data['is_available'] ?? true;

        $records = DB::transaction(function () use ($data, $start, $end, $slots, $available) {
            $records = collect();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                foreach ($slots as $slot) {
                    $records->push(HallAvailability::updateOrCreate(
                        ['hall_id' => $data['hall_id'], 'date' => $date->toDateString(), 'time_slot' => $slot],
                        [
                            'is_available' => $available,
                            'reason' => $available ? null : ($data['reason'] ?? 'blocked'),
                            'custom_price' => $data['custom_price'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ],
                    ));
                }
            }

            return $records;
        });

        return AvailabilityTransformer::collection($records)
            ->additional(['message' => __('owner.availability_resource.notifications.bulk_created_body', ['count' => $records->count()])])
            ->response()
            ->setStatusCode(201);
    }
}
