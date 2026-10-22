<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\TimeSlot;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LookupResource;
use App\Models\City;
use App\Models\EventType;
use App\Models\HallFeature;
use App\Models\HallType;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Public reference data for the mobile app's filters and booking form.
 *
 * Same records (and ordering) the website's hall search uses.
 */
class CatalogController extends Controller
{
    public function regions(): AnonymousResourceCollection
    {
        return LookupResource::collection(
            Region::where('is_active', true)->orderBy('order')->get()
        );
    }

    public function cities(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
        ]);

        return LookupResource::collection(
            City::where('is_active', true)
                ->when($validated['region_id'] ?? null, fn ($query, $regionId) => $query->where('region_id', $regionId))
                ->orderBy('order')
                ->get()
        );
    }

    public function hallTypes(): AnonymousResourceCollection
    {
        return LookupResource::collection(
            HallType::where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    public function hallFeatures(): AnonymousResourceCollection
    {
        return LookupResource::collection(
            HallFeature::where('is_active', true)->orderBy('order')->get()
        );
    }

    public function eventTypes(): AnonymousResourceCollection
    {
        return LookupResource::collection(
            EventType::where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    /**
     * Static booking options the app needs before it can build the booking form.
     */
    public function config(): array
    {
        return [
            'data' => [
                'currency' => config('majalis.currency'),
                'timezone' => config('app.timezone'),
                'locales' => ['ar', 'en'],
                'time_slots' => collect(TimeSlot::cases())->map(fn (TimeSlot $slot) => [
                    'key' => $slot->value,
                    'label' => app()->getLocale() === 'ar' ? $slot->labelAr() : $slot->label(),
                    'start' => $slot->startTime(),
                    'end' => $slot->endTime(),
                ]),
                'max_booking_days' => 3,
                'advance_days' => (int) config('majalis.booking.advance_days'),
                'min_advance_hours' => (int) config('majalis.booking.min_advance_hours'),
            ],
        ];
    }
}
