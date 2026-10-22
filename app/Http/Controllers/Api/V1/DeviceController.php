<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Push notification tokens. The app registers after sign-in and on token refresh,
 * and unregisters before sign-out.
 */
class DeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in(DeviceToken::PLATFORMS)],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        // A device belongs to whoever signed in last on it.
        $device = DeviceToken::updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => $request->user()->id,
                'platform' => $validated['platform'],
                'device_name' => $validated['device_name'] ?? null,
                'locale' => app()->getLocale(),
                'last_used_at' => now(),
            ],
        );

        return response()->json(['data' => ['id' => $device->id]], $device->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, string $token): Response
    {
        $request->user()->deviceTokens()->where('token', $token)->delete();

        return response()->noContent();
    }
}
