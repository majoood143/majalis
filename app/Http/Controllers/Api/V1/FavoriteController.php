<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HallListResource;
use App\Models\Hall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * The "Saved" tab: halls the customer marked as favourites.
 */
class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $halls = $request->user()->favoriteHalls()
            ->with(['city.region', 'hallTypes'])
            ->where('is_active', true)
            ->where('is_listed', true)
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        $halls->each(fn (Hall $hall) => $hall->setAttribute('is_favorite', true));

        return HallListResource::collection($halls);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $hall = $this->listedHall($slug);
        $request->user()->favoriteHalls()->syncWithoutDetaching([$hall->id]);

        return response()->json(['data' => ['hall_id' => $hall->id, 'is_favorite' => true]]);
    }

    public function destroy(Request $request, string $slug): JsonResponse
    {
        $hall = $this->listedHall($slug);
        $request->user()->favoriteHalls()->detach($hall->id);

        return response()->json(['data' => ['hall_id' => $hall->id, 'is_favorite' => false]]);
    }

    protected function listedHall(string $slug): Hall
    {
        return Hall::where('slug', $slug)->where('is_active', true)->where('is_listed', true)->firstOrFail();
    }
}
