<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function index(Request $request)
    {
        // Set locale from request or default to Arabic
        $locale = $request->get('lang', session('locale', 'ar'));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        $regions = Region::with('cities')->where('is_active', true)->get();

        $query = Hall::with(['city.region', 'owner'])
            ->where('is_active', true);

        // ... rest of your filters

        $halls = $query->paginate(12)->appends($request->query());

        $mapHalls = Hall::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'slug', 'latitude', 'longitude', 'featured_image', 'price_per_slot', 'city_id')
            ->with('city:id,name')
            ->get();

        $cities = collect();
        if ($request->filled('region_id')) {
            $cities = City::where('region_id', $request->region_id)
                ->where('is_active', true)
                ->get();
        }

        return view('customer.halls.index2', compact('halls', 'regions', 'cities', 'mapHalls'));
    }

    public function getCitiesByRegion($regionId)
    {
        $cities = City::where('region_id', $regionId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    public function show($slug)
    {
        $hall = Hall::where('slug', $slug)
            ->where('is_active', true)
            ->with(['city.region', 'owner', 'activeExtraServices', 'reviews' => function ($query) {
                $query->where('is_approved', true)->latest()->limit(5);
            }])
            ->firstOrFail();

        // Get hall features
        $features = $hall->feature_details; // Using the accessor from Hall model

        // Or if getFeaturesList() method works better:
        // $features = $hall->getFeaturesList();

        // Get similar halls in the same city
        $similarHalls = Hall::where('is_active', true)
            ->where('id', '!=', $hall->id)
            ->where('city_id', $hall->city_id)
            ->limit(4)
            ->get();

        return view('customer.hall-details', compact('hall', 'features', 'similarHalls'));
    }
    // public function show($slug)
    // {
    //     $hall = Hall::where('slug', $slug)
    //         ->where('is_active', true)
    //         ->with(['city.region', 'owner', 'reviews' => function ($query) {
    //             $query->where('is_approved', true)->latest()->limit(5);
    //         }])
    //         ->firstOrFail();

    //     return view('customer.hall-details', compact('hall'));
    // }
}
