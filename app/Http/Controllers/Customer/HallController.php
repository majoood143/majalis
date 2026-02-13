<?php

declare(strict_types=1);

/**
 * HallController — Customer-facing hall browsing with Smart Search.
 *
 * ┌───────────────────────────────────────────────────────────────┐
 * │  REPLACES: app/Http/Controllers/Customer/HallController.php  │
 * │  BACKUP FIRST: cp HallController.php HallController.php.bak  │
 * └───────────────────────────────────────────────────────────────┘
 *
 * WHAT CHANGED:
 * - Added AvailabilityService dependency injection
 * - Added date + time_slot search parameters to index()
 * - Added $timeSlots, $features, $isDateSearch, $suggestions, $availableCount to view
 * - Added checkDateAvailability() and suggestDates() API endpoints
 * - All EXISTING functionality (region filter, city filter, map, stats) is PRESERVED
 *
 * @package App\Http\Controllers\Customer
 */

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\City;
use App\Models\Region;
use App\Models\HallFeature;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class HallController extends Controller
{
    /**
     * Inject the AvailabilityService for date-aware search.
     *
     * @param AvailabilityService $availabilityService
     */
    public function __construct(
        private readonly AvailabilityService $availabilityService,
    ) {}

    /**
     * Display the hall browsing page with smart date-aware search.
     *
     * Supports TWO modes:
     *   1. BROWSE MODE  — No date selected, shows all active halls (original behavior)
     *   2. SEARCH MODE  — Date + time_slot selected, shows availability per hall
     *
     * @param  Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // ── Set locale (preserve existing behavior) ──
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // ── Validate all search parameters ──
        $validated = $request->validate([
            'date'       => ['nullable', 'date', 'after_or_equal:today'],
            'time_slot'  => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
            'region_id'  => ['nullable', 'integer', 'exists:regions,id'],
            'city_id'    => ['nullable', 'integer', 'exists:cities,id'],
            'min_guests' => ['nullable', 'integer', 'min:1'],
            'max_price'  => ['nullable', 'numeric', 'min:0'],
            'features'   => ['nullable', 'array'],
            'features.*' => ['integer'],
            'sort'       => ['nullable', 'string', 'in:rating,price_low,price_high,capacity,latest'],
        ]);

        // ── Determine search mode ──
        $isDateSearch   = $request->filled('date') && $request->filled('time_slot');
        $suggestions    = [];
        $availableCount = 0;

        // ── Get filter options (always needed) ──
        $regions  = Region::where('is_active', true)->orderBy('order')->get();
        $features = HallFeature::where('is_active', true)->orderBy('order')->get();
        $timeSlots = AvailabilityService::TIME_SLOTS;

        // ── Get cities (filtered by region if selected) ──
        $cities = collect();
        if ($request->filled('region_id')) {
            $cities = City::where('region_id', (int) $validated['region_id'])
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        } else {
            $cities = City::where('is_active', true)->orderBy('order')->get();
        }

        // ══════════════════════════════════════════════════════════
        //  MODE 1: DATE-AWARE SEARCH (new Smart Search)
        // ══════════════════════════════════════════════════════════
        if ($isDateSearch) {
            // Determine city filter — use city_id if provided, otherwise null
            $cityId = $request->filled('city_id') ? (int) $validated['city_id'] : null;

            $halls = $this->availabilityService->searchAvailableHalls(
                date: $validated['date'],
                timeSlot: $validated['time_slot'] ?? null,
                cityId: $cityId,
                minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
                maxPrice: isset($validated['max_price']) ? (float) $validated['max_price'] : null,
                featureIds: $validated['features'] ?? null,
            );

            // Filter by region if city not specified but region is
            if (!$cityId && $request->filled('region_id')) {
                $regionCityIds = $cities->pluck('id')->toArray();
                $halls = $halls->filter(fn(Hall $h) => in_array($h->city_id, $regionCityIds));
            }

            // Count available vs total
            $availableCount = $halls->where('is_available', true)->count();

            // Phase 4: Suggest nearby dates when no results
            if ($availableCount === 0) {
                $suggestions = $this->availabilityService->suggestNearbyDates(
                    date: $validated['date'],
                    timeSlot: $validated['time_slot'] ?? null,
                    cityId: $cityId,
                    minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
                );
            }

            // Apply secondary sort
            if ($request->filled('sort')) {
                $halls = $this->applySortOrder($halls, $validated['sort'], $validated['time_slot'] ?? null);
            }

        // ══════════════════════════════════════════════════════════
        //  MODE 2: BROWSE MODE (original behavior preserved)
        // ══════════════════════════════════════════════════════════
        } else {
            $query = Hall::query()
                ->with(['city.region', 'owner'])
                ->where('is_active', true)
                ->whereNull('deleted_at');

            // Region filter
            if ($request->filled('region_id')) {
                $regionCityIds = $cities->pluck('id')->toArray();
                $query->whereIn('city_id', $regionCityIds);
            }

            // City filter
            if ($request->filled('city_id')) {
                $query->where('city_id', (int) $validated['city_id']);
            }

            // Guest count filter
            if ($request->filled('min_guests')) {
                $query->where('capacity_max', '>=', (int) $validated['min_guests']);
            }

            // Max price filter
            if ($request->filled('max_price')) {
                $query->where('price_per_slot', '<=', (float) $validated['max_price']);
            }

            // Features filter
            if ($request->filled('features')) {
                foreach ($validated['features'] as $featureId) {
                    $query->whereJsonContains('features', (int) $featureId);
                }
            }

            // Sort
            $sortBy = $request->get('sort', 'latest');
            match ($sortBy) {
                'rating'     => $query->orderByDesc('average_rating'),
                'price_low'  => $query->orderBy('price_per_slot'),
                'price_high' => $query->orderByDesc('price_per_slot'),
                'capacity'   => $query->orderByDesc('capacity_max'),
                default      => $query->orderByDesc('is_featured')->latest(),
            };

            $halls = $query->get();

            // In browse mode, all halls are "available" (no date context)
            $halls->each(fn(Hall $h) => $h->setAttribute('is_available', true));
            $availableCount = $halls->count();
        }

        // ── Build map data (preserve existing functionality) ──
        $mapHalls = $halls->filter(fn(Hall $h) => $h->latitude && $h->longitude)
            ->map(fn(Hall $h) => [
                'id'        => $h->id,
                'name'      => $h->name,
                'slug'      => $h->slug,
                'lat'       => (float) $h->latitude,
                'lng'       => (float) $h->longitude,
                'price'     => (float) $h->price_per_slot,
                'image'     => $h->featured_image ? asset('storage/' . $h->featured_image) : null,
                'available' => $h->is_available ?? true,
            ])->values();

        // ── Stats (preserve existing functionality) ──
        $stats = [
            'min_price' => Hall::where('is_active', true)->min('price_per_slot'),
            'max_price' => Hall::where('is_active', true)->max('price_per_slot'),
            'avg_price' => Hall::where('is_active', true)->avg('price_per_slot'),
        ];

        // ── Halls grouped by region (preserve existing functionality) ──
        $hallsByRegion = $halls->groupBy(fn(Hall $h) => $h->city?->region_id);

        return view('customer.halls.index', compact(
            'halls',
            'regions',
            'cities',
            'features',        // ← NEW: hall features for filter chips
            'timeSlots',       // ← NEW: time slot dropdown options
            'isDateSearch',    // ← NEW: whether date search was performed
            'suggestions',     // ← NEW: nearby date suggestions (Phase 4)
            'availableCount',  // ← NEW: count of available halls
            'mapHalls',        // EXISTING: map pin data
            'stats',           // EXISTING: price statistics
            'hallsByRegion',   // EXISTING: grouped halls
        ));
    }

    /**
     * Get cities by region (AJAX) — PRESERVED from original.
     *
     * @param  int $regionId
     * @return JsonResponse
     */
    public function getCitiesByRegion($regionId): JsonResponse
    {
        $cities = City::where('region_id', $regionId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    /**
     * Display hall details — PRESERVED from original.
     *
     * @param  string $slug
     * @return View
     */
    public function show($slug): View
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        $hall = Hall::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'city.region',
                'owner',
                'activeExtraServices',
                'reviews' => function ($query) {
                    $query->where('is_approved', true)->latest()->limit(5);
                },
            ])
            ->firstOrFail();

        // Get hall features
        $features = $hall->feature_details;

        // Get similar halls
        $similarHalls = Hall::where('is_active', true)
            ->where('id', '!=', $hall->id)
            ->where('city_id', $hall->city_id)
            ->limit(4)
            ->get();

        return view('customer.hall-details', compact('hall', 'features', 'similarHalls'));
    }

    /**
     * API: Check availability for a date + slot (AJAX badge on search form).
     *
     * GET /api/halls/check-availability?date=2026-02-15&time_slot=evening
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function checkDateAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'      => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', 'in:morning,afternoon,evening,full_day'],
            'city_id'   => ['nullable', 'integer'],
        ]);

        $halls = $this->availabilityService->searchAvailableHalls(
            date: $validated['date'],
            timeSlot: $validated['time_slot'],
            cityId: isset($validated['city_id']) ? (int) $validated['city_id'] : null,
        );

        $availableCount = $halls->where('is_available', true)->count();

        return response()->json([
            'available_count' => $availableCount,
            'total_count'     => $halls->count(),
            'message'         => $availableCount > 0
                ? __(':count halls available', ['count' => $availableCount])
                : __('No halls available on this date'),
        ]);
    }

    /**
     * API: Suggest nearby dates with availability.
     *
     * GET /api/halls/suggest-dates?date=2026-02-15&time_slot=evening
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function suggestDates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'      => ['required', 'date'],
            'time_slot' => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
            'city_id'   => ['nullable', 'integer'],
        ]);

        $suggestions = $this->availabilityService->suggestNearbyDates(
            date: $validated['date'],
            timeSlot: $validated['time_slot'] ?? null,
            cityId: isset($validated['city_id']) ? (int) $validated['city_id'] : null,
        );

        return response()->json(['suggestions' => $suggestions]);
    }

    // ──────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * Apply secondary sort order to results.
     * Available halls always remain on top.
     *
     * @param  \Illuminate\Support\Collection $halls
     * @param  string                         $sort
     * @param  string|null                    $timeSlot
     * @return \Illuminate\Support\Collection
     */
    private function applySortOrder($halls, string $sort, ?string $timeSlot = null)
    {
        return match ($sort) {
            'rating' => $halls->sortBy([
                fn($a, $b) => $b->is_available <=> $a->is_available,
                fn($a, $b) => (float) $b->average_rating <=> (float) $a->average_rating,
            ])->values(),

            'price_low' => $halls->sortBy([
                fn($a, $b) => $b->is_available <=> $a->is_available,
                fn($a, $b) => $this->getDisplayPrice($a, $timeSlot) <=> $this->getDisplayPrice($b, $timeSlot),
            ])->values(),

            'price_high' => $halls->sortBy([
                fn($a, $b) => $b->is_available <=> $a->is_available,
                fn($a, $b) => $this->getDisplayPrice($b, $timeSlot) <=> $this->getDisplayPrice($a, $timeSlot),
            ])->values(),

            'capacity' => $halls->sortBy([
                fn($a, $b) => $b->is_available <=> $a->is_available,
                fn($a, $b) => $b->capacity_max <=> $a->capacity_max,
            ])->values(),

            default => $halls,
        };
    }

    /**
     * Get display price for a hall for sorting.
     *
     * @param  Hall        $hall
     * @param  string|null $timeSlot
     * @return float
     */
    private function getDisplayPrice(Hall $hall, ?string $timeSlot = null): float
    {
        $prices = $hall->slot_prices ?? [];

        if ($timeSlot && isset($prices[$timeSlot])) {
            return (float) $prices[$timeSlot];
        }

        $availableSlots  = $hall->available_slots ?? [];
        $availablePrices = array_intersect_key($prices, array_flip($availableSlots));

        return $availablePrices ? min($availablePrices) : (float) $hall->price_per_slot;
    }
}
