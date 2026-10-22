<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\TimeSlot;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HallListResource;
use App\Http\Resources\Api\V1\HallResource;
use App\Models\City;
use App\Models\Hall;
use App\Services\AvailabilityService;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Public hall search, details and availability for the mobile app.
 *
 * Mirrors Customer\HallController (website) so both clients show the same halls:
 * - browse mode (no date): paginated DB query
 * - date mode (?date=): AvailabilityService::searchAvailableHalls(), each hall flagged `is_available`
 */
class HallController extends Controller
{
    /** Longest range one availability request may cover. */
    protected const MAX_AVAILABILITY_DAYS = 31;

    public function __construct(protected AvailabilityService $availabilityService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time_slot' => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'hall_type_id' => ['nullable', 'integer', 'exists:hall_types,id'],
            'min_guests' => ['nullable', 'integer', 'min:1'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['integer'],
            'sort' => ['nullable', 'string', 'in:rating,price_low,price_high,capacity,latest'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);

        if (isset($validated['date'])) {
            return $this->searchByDate($request, $validated, $perPage);
        }

        $query = Hall::query()
            ->with(['city.region', 'hallTypes'])
            ->where('is_active', true)
            ->where('is_listed', true)
            ->when($validated['q'] ?? null, fn (Builder $q, string $term) => $this->whereMatches($q, $term))
            ->when($validated['region_id'] ?? null, fn (Builder $q, $regionId) => $q->whereIn(
                'city_id',
                City::where('region_id', $regionId)->select('id')
            ))
            ->when($validated['city_id'] ?? null, fn (Builder $q, $cityId) => $q->where('city_id', $cityId))
            ->when($validated['min_guests'] ?? null, fn (Builder $q, $guests) => $q->where('capacity_max', '>=', $guests))
            ->when($validated['max_price'] ?? null, fn (Builder $q, $price) => $q->where('price_per_slot', '<=', $price))
            ->when($validated['hall_type_id'] ?? null, fn (Builder $q, $typeId) => $q->whereHas(
                'hallTypes',
                fn (Builder $types) => $types->where('hall_types.id', $typeId)
            ));

        foreach ($validated['features'] ?? [] as $featureId) {
            $query->whereJsonContains('features', (int) $featureId);
        }

        match ($validated['sort'] ?? 'latest') {
            'rating' => $query->orderByDesc('average_rating'),
            'price_low' => $query->orderBy('price_per_slot'),
            'price_high' => $query->orderByDesc('price_per_slot'),
            'capacity' => $query->orderByDesc('capacity_max'),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        $halls = $query->paginate($perPage)->withQueryString();
        $this->markFavorites($request, $halls->getCollection());

        return HallListResource::collection($halls);
    }

    public function show(Request $request, string $slug): HallResource
    {
        $hall = Hall::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_listed', true)
            ->with([
                'city.region',
                'activeExtraServices',
                'activeImages',
                'hallTypes' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'approvedReviews' => fn ($q) => $q->with('user:id,name')->limit(5),
            ])
            ->firstOrFail();

        $hall->setRelation('featureList', $hall->getFeaturesList());
        $this->markFavorites($request, new Collection([$hall]));

        return new HallResource($hall);
    }

    /**
     * Day-by-day slot availability and prices, for the booking calendar.
     *
     * GET /api/v1/halls/{slug}/availability?from=2026-11-01&to=2026-11-14
     */
    public function availability(Request $request, string $slug): array
    {
        $today = Carbon::today()->toDateString();
        $lastBookable = Carbon::today()->addDays((int) config('majalis.booking.advance_days'))->toDateString();

        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);

        $hall = Hall::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_listed', true)
            ->firstOrFail();

        $from = Carbon::parse($validated['from'] ?? $today);
        $to = Carbon::parse($validated['to'] ?? $from->copy()->addDays(13));
        $to = $to->min($from->copy()->addDays(self::MAX_AVAILABILITY_DAYS - 1))->min(Carbon::parse($lastBookable));

        $earliestBookable = Carbon::now()->addHours((int) config('majalis.booking.min_advance_hours'));
        $days = [];

        foreach (CarbonPeriod::create($from, $to) as $date) {
            $dateString = $date->toDateString();
            $freeSlots = $this->availabilityService->getAvailableSlotsForHall($hall, $dateString);
            $prices = $freeSlots === [] ? [] : $this->availabilityService->getSlotPrices($hall, $dateString);

            $days[] = [
                'date' => $dateString,
                'slots' => collect(TimeSlot::cases())->map(fn (TimeSlot $slot) => [
                    'key' => $slot->value,
                    'available' => in_array($slot->value, $freeSlots, true)
                        && $date->copy()->setTimeFromTimeString($slot->startTime())->gte($earliestBookable),
                    'price' => isset($prices[$slot->value]) ? round((float) $prices[$slot->value], 3) : null,
                ]),
            ];
        }

        return [
            'data' => $days,
            'meta' => [
                'hall_id' => $hall->id,
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'currency' => config('majalis.currency'),
            ],
        ];
    }

    /**
     * Text search over the hall name (both languages) and address.
     */
    protected function whereMatches(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(fn (Builder $q) => $q
            ->where('name->en', 'like', $like)
            ->orWhere('name->ar', 'like', $like)
            ->orWhere('address', 'like', $like));
    }

    /**
     * Sets `is_favorite` on each hall when the request carries a customer token
     * (these routes are public, so the token is optional).
     *
     * @param  Collection<int, Hall>  $halls
     */
    protected function markFavorites(Request $request, Collection $halls): void
    {
        $user = $request->user('sanctum');
        if (! $user || $halls->isEmpty()) {
            return;
        }

        $favoriteIds = $user->favoriteHalls()->whereIn('halls.id', $halls->modelKeys())->pluck('halls.id')->all();
        $halls->each(fn (Hall $hall) => $hall->setAttribute('is_favorite', in_array($hall->id, $favoriteIds, true)));
    }

    protected function searchByDate(Request $request, array $validated, int $perPage): AnonymousResourceCollection
    {
        $halls = $this->availabilityService->searchAvailableHalls(
            date: $validated['date'],
            timeSlot: $validated['time_slot'] ?? null,
            cityId: isset($validated['city_id']) ? (int) $validated['city_id'] : null,
            minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
            maxPrice: isset($validated['max_price']) ? (float) $validated['max_price'] : null,
            featureIds: $validated['features'] ?? null,
        );

        if (! isset($validated['city_id']) && isset($validated['region_id'])) {
            $halls = $halls->filter(fn (Hall $hall) => $hall->city?->region_id === (int) $validated['region_id']);
        }

        if (isset($validated['hall_type_id'])) {
            $halls = $halls->filter(fn (Hall $hall) => $hall->hallTypes->contains('id', (int) $validated['hall_type_id']));
        }

        if (filled($validated['q'] ?? null)) {
            $term = Str::lower($validated['q']);
            $halls = $halls->filter(fn (Hall $hall) => collect($hall->getTranslations('name'))
                ->push((string) $hall->address)
                ->contains(fn (?string $text) => Str::contains(Str::lower((string) $text), $term)));
        }

        $this->markFavorites($request, $halls);

        $halls = match ($validated['sort'] ?? null) {
            'rating' => $halls->sortByDesc('average_rating'),
            'price_low' => $halls->sortBy('price_per_slot'),
            'price_high' => $halls->sortByDesc('price_per_slot'),
            'capacity' => $halls->sortByDesc('capacity_max'),
            default => $halls->sortByDesc('is_available'),
        };

        $availableCount = $halls->where('is_available', true)->count();
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginator = new LengthAwarePaginator(
            $halls->forPage($page, $perPage)->values(),
            $halls->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return HallListResource::collection($paginator)->additional([
            'meta' => [
                'available_count' => $availableCount,
                'suggested_dates' => $availableCount === 0
                    ? $this->availabilityService->suggestNearbyDates(
                        date: $validated['date'],
                        timeSlot: $validated['time_slot'] ?? null,
                        cityId: isset($validated['city_id']) ? (int) $validated['city_id'] : null,
                        minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
                    )
                    : [],
            ],
        ]);
    }
}
