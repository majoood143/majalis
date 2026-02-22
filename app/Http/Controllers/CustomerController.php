<?php

declare(strict_types=1);

/**
 * CustomerController - Handles customer-facing hall browsing and search.
 *
 * WHAT CHANGED (Smart Search Feature v2.0):
 * ------------------------------------------
 * - Added date + time_slot as primary search parameters
 * - Integrated AvailabilityService for cross-referencing availability
 * - Results now include available_slots and slot_prices per hall
 * - Added suggestNearbyDates fallback when no results found
 * - Unavailable halls shown at bottom (grayed out) instead of hidden
 *
 * This file REPLACES the existing CustomerController.
 * Backup your original: cp CustomerController.php CustomerController.php.bak
 *
 * Installation path: app/Http/Controllers/CustomerController.php
 *
 * @package App\Http\Controllers
 */

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\City;
use App\Models\Region;
use App\Models\HallFeature;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Booking;

class CustomerController extends Controller
{
    /**
     * Inject the AvailabilityService via constructor.
     *
     * @param AvailabilityService $availabilityService
     */
    public function __construct(
        private readonly AvailabilityService $availabilityService,
    ) {}

    /**
     * Display the hall browsing page with smart date-aware search.
     *
     * Query Parameters:
     *   - date       (required for search) : Y-m-d format
     *   - time_slot  (required for search) : morning|afternoon|evening|full_day
     *   - city       (optional)            : city ID
     *   - min_guests (optional)            : minimum guest count
     *   - max_price  (optional)            : maximum price in OMR
     *   - features[] (optional)            : array of feature IDs
     *   - sort       (optional)            : rating|price_low|price_high|capacity
     *
     * @param  Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // ── Validate search parameters ──
        $validated = $request->validate([
            'date'       => ['nullable', 'date', 'after_or_equal:today'],
            'time_slot'  => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
            'city'       => ['nullable', 'integer', 'exists:cities,id'],
            'min_guests' => ['nullable', 'integer', 'min:1'],
            'max_price'  => ['nullable', 'numeric', 'min:0'],
            'features'   => ['nullable', 'array'],
            'features.*' => ['integer'],
            'sort'       => ['nullable', 'string', 'in:rating,price_low,price_high,capacity'],
        ]);

        // ── Determine if this is a date-aware search or just browsing ──
        $isDateSearch  = $request->filled('date') && $request->filled('time_slot');
        $halls         = collect();
        $suggestions   = [];
        $availableCount = 0;

        if ($isDateSearch) {
            // ── DATE-AWARE SEARCH — use AvailabilityService ──
            $halls = $this->availabilityService->searchAvailableHalls(
                date: $validated['date'],
                timeSlot: $validated['time_slot'] ?? null,
                cityId: isset($validated['city']) ? (int) $validated['city'] : null,
                minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
                maxPrice: isset($validated['max_price']) ? (float) $validated['max_price'] : null,
                featureIds: $validated['features'] ?? null,
            );

            // Count available vs total
            $availableCount = $halls->where('is_available', true)->count();

            // ── Phase 4: If no available halls, suggest nearby dates ──
            if ($availableCount === 0) {
                $suggestions = $this->availabilityService->suggestNearbyDates(
                    date: $validated['date'],
                    timeSlot: $validated['time_slot'] ?? null,
                    cityId: isset($validated['city']) ? (int) $validated['city'] : null,
                    minGuests: isset($validated['min_guests']) ? (int) $validated['min_guests'] : null,
                );
            }

            // ── Apply secondary sort if requested ──
            if ($request->filled('sort')) {
                $halls = $this->applySortOrder($halls, $validated['sort'], $validated['time_slot'] ?? null);
            }
        } else {
            // ── BROWSE MODE — show all active halls (no availability data) ──
            $query = Hall::query()
                ->with(['city.region', 'owner'])
                ->where('is_active', true)
                ->whereNull('deleted_at');

            // Apply basic filters even in browse mode
            if ($request->filled('city')) {
                $query->where('city_id', (int) $validated['city']);
            }

            if ($request->filled('min_guests')) {
                $query->where('capacity_max', '>=', (int) $validated['min_guests']);
            }

            if ($request->filled('features')) {
                foreach ($validated['features'] as $featureId) {
                    $query->whereJsonContains('features', (int) $featureId);
                }
            }

            $halls = $query
                ->orderByDesc('is_featured')
                ->orderByDesc('average_rating')
                ->get();

            // In browse mode, every hall is "available" (no date context)
            $halls->each(fn(Hall $h) => $h->setAttribute('is_available', true));
            $availableCount = $halls->count();
        }

        // ── Fetch filter options ──
        $cities   = City::where('is_active', true)->orderBy('order')->get();
        $regions  = Region::where('is_active', true)->orderBy('order')->get();
        $features = HallFeature::where('is_active', true)->orderBy('order')->get();

        // ── Time slot labels (for the dropdown) ──
        $timeSlots = AvailabilityService::TIME_SLOTS;

        return view('customer.index', compact(
            'halls',
            'cities',
            'regions',
            'features',
            'timeSlots',
            'isDateSearch',
            'suggestions',
            'availableCount',
        ));
    }

    /**
     * API endpoint: Check availability for a specific date.
     *
     * Used for AJAX requests from the search form to show
     * a quick preview of available hall count before full search.
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
            'city'      => ['nullable', 'integer'],
        ]);

        $halls = $this->availabilityService->searchAvailableHalls(
            date: $validated['date'],
            timeSlot: $validated['time_slot'],
            cityId: isset($validated['city']) ? (int) $validated['city'] : null,
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
     * API endpoint: Suggest nearby dates with availability.
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
            'city'      => ['nullable', 'integer'],
        ]);

        $suggestions = $this->availabilityService->suggestNearbyDates(
            date: $validated['date'],
            timeSlot: $validated['time_slot'] ?? null,
            cityId: isset($validated['city']) ? (int) $validated['city'] : null,
        );

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * Apply secondary sort order to the results collection.
     *
     * This runs AFTER the primary availability sort, so available
     * halls still appear first, but within that group they're reordered.
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
     * Get the display price for sorting purposes.
     *
     * If a time slot is selected, returns the price for that slot.
     * Otherwise returns the lowest available slot price.
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

        // Return lowest price among available slots
        $availableSlots = $hall->available_slots ?? [];
        $availablePrices = array_intersect_key($prices, array_flip($availableSlots));

        return $availablePrices ? min($availablePrices) : (float) $hall->price_per_slot;
    }

    /**
     * Customer dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        $upcomingBookings = Booking::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now())
            ->with(['hall.city'])
            ->latest('booking_date')
            ->limit(5)
            ->get();

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'upcoming' => Booking::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('booking_date', '>=', now())
                ->count(),
            'completed' => Booking::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Booking::where('user_id', $user->id)
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view('customer.dashboard', compact('upcomingBookings', 'stats'));
    }

    /**
     * Display user bookings
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();

        $query = Booking::query()
            ->where('user_id', $user->id)
            ->with(['hall.city']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('booking_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('booking_date', '<=', $request->to_date);
        }

        $bookings = $query->latest('booking_date')->paginate(10)->withQueryString();

        return view('customer.bookings', compact('bookings'));
    }

    /**
     * Display booking details
     */
    public function bookingDetails(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $booking->load([
            'hall.city',
            'hall.owner',
            'extraServices'
        ]);

        return view('customer.booking-details', compact('booking'));
    }
}
