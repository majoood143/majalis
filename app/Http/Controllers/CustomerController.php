<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\City;
use App\Models\Booking;
use App\Models\HallFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display the homepage with hall listings
     */
    public function index(Request $request)
    {
        $query = Hall::query()
            ->with(['city', 'owner'])
            ->active()
            ->latest();

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }

        // Filter by capacity
        if ($request->filled('min_guests')) {
            $query->where('capacity_max', '>=', $request->min_guests);
        }

        // Filter by price range
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Filter by features
        if ($request->filled('features')) {
            $features = is_array($request->features) ? $request->features : [$request->features];
            foreach ($features as $featureId) {
                $query->whereJsonContains('features', (int) $featureId);
            }
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_day', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_day', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'popular':
                $query->orderBy('total_bookings', 'desc');
                break;
            default:
                $query->latest();
        }

        $halls = $query->paginate(12)->withQueryString();
        
        $cities = City::active()->orderBy('name->en')->get();
        $features = HallFeature::active()->ordered()->get();

        return view('customer.index', compact('halls', 'cities', 'features'));
    }

    /**
     * Display hall details
     */
    public function show(Hall $hall)
    {
        // Check if hall is active
        if (!$hall->is_active) {
            abort(404, 'Hall not found');
        }

        $hall->load([
            'city',
            'owner',
            'activeExtraServices',
            'approvedReviews.user',
            'availability'
        ]);

        $features = $hall->getFeaturesList();
        
        // Get similar halls
        $similarHalls = Hall::query()
            ->where('id', '!=', $hall->id)
            ->where('city_id', $hall->city_id)
            ->active()
            ->limit(4)
            ->get();

        return view('customer.hall-details', compact('hall', 'features', 'similarHalls'));
    }

    /**
     * Show booking form
     */
    public function book(Hall $hall)
    {
        if (!$hall->is_active) {
            abort(404, 'Hall not available');
        }

        $hall->load('activeExtraServices');

        return view('customer.book', compact('hall'));
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

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }
}
