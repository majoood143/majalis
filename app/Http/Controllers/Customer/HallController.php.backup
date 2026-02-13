<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;

class HallController extends Controller
{
    /**
     * Display hall listings with filters and sorting
     */
    public function index(Request $request)
    {
        // Set locale
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Get all active regions with their cities
        $regions = Region::with('cities')->where('is_active', true)->get();

        // Base query
        $query = Hall::with(['city.region', 'owner'])
            ->where('is_active', true);

        // FILTERS

        // Filter by region
        if ($request->filled('region_id')) {
            $query->whereHas('city', function ($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by capacity
        if ($request->filled('capacity')) {
            $capacity = (int) $request->capacity;
            $query->where('capacity_min', '<=', $capacity)
                ->where('capacity_max', '>=', $capacity);
        }

        // Filter by minimum price
        if ($request->filled('min_price')) {
            $query->where('price_per_slot', '>=', (float) $request->min_price);
        }

        // Filter by maximum price
        if ($request->filled('max_price')) {
            $query->where('price_per_slot', '<=', (float) $request->max_price);
        }

        // Search by name, description, or address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search in JSON name field
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$search}%"])
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // SORTING
        $sortBy = $request->get('sort', 'latest');

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_slot', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_slot', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'popular':
                $query->orderBy('total_bookings', 'desc');
                break;
            case 'name':
                // Sort by name in current locale
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) ASC");
                break;
            default: // 'latest'
                $query->orderBy('created_at', 'desc');
        }

        // Get paginated results
        $halls = $query->paginate(12)->withQueryString();

        // Get halls grouped by region (for region view)
        $hallsByRegion = collect();
        if ($request->get('view_mode') === 'by_region') {
            $allHalls = $query->get();
            $hallsByRegion = $allHalls->groupBy(function ($hall) {
                return $hall->city->region->id;
            });
        }

        // Get halls with coordinates for map
        $mapHalls = Hall::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'slug', 'latitude', 'longitude', 'featured_image', 'price_per_slot', 'city_id')
            ->with('city:id,name')
            ->get();

        // Get cities for selected region
        $cities = collect();
        if ($request->filled('region_id')) {
            $cities = City::where('region_id', $request->region_id)
                ->where('is_active', true)
                ->get();
        }

        // Get filter statistics
        $stats = [
            'total_halls' => Hall::where('is_active', true)->count(),
            'min_price' => Hall::where('is_active', true)->min('price_per_slot'),
            'max_price' => Hall::where('is_active', true)->max('price_per_slot'),
            'avg_price' => Hall::where('is_active', true)->avg('price_per_slot'),
        ];

        return view('customer.halls.index', compact(
            'halls',
            'regions',
            'cities',
            'mapHalls',
            'stats',
            'hallsByRegion'
        ));
    }

    /**
     * Get cities by region (AJAX)
     */
    public function getCitiesByRegion($regionId)
    {
        $cities = City::where('region_id', $regionId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    /**
     * Display hall details
     */
    public function show($slug)
    {
        // Set locale
        $locale = request()->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        // Get hall with relationships
        // $hall = Hall::where('slug', $slug)
        //     ->where('is_active', true)
        //     ->with([
        //         'city.region',
        //         'owner',
        //         'activeExtraServices',
        //         'reviews' => function ($query) {
        //             $query->where('is_approved', true)->latest()->limit(5);
        //         }
        //     ])
        //     ->firstOrFail();

        $hall = Hall::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'owner',
                'city.region',
                'activeExtraServices',
                'city',
                //'amenities',
                'images' => function ($query) {
                    // Load only active gallery images, ordered
                    $query->where('is_active', true)
                        ->where('type', 'gallery')
                        ->orderBy('order')
                        ->orderBy('id');
                },
                'reviews' => function ($query) {
                    $query->latest()->limit(5);
                }
            ])
            ->firstOrFail();

        // Get hall features
        $features = $hall->feature_details;

        // Get similar halls in the same city
        $similarHalls = Hall::where('is_active', true)
            ->where('id', '!=', $hall->id)
            ->where('city_id', $hall->city_id)
            ->limit(4)
            ->get();

        return view('customer.hall-details', compact('hall', 'features', 'similarHalls'));
    }
}
