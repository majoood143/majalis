<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

/**
 * The signed-in customer's own account. Mirrors Customer\ProfileController (website).
 */
class ProfileController extends Controller
{
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Partial update: only the fields sent are changed.
     */
    public function update(Request $request): UserResource
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^[0-9+\s-]{7,20}$/'],
            'language_preference' => ['sometimes', 'required', Rule::in(['ar', 'en'])],
        ]);

        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        $user->fill($validated);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return new UserResource($user);
    }

    /**
     * Changing the password signs out every other device.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->update(['password' => Hash::make($validated['password'])]);
        $user->tokens()->whereKeyNot($user->currentAccessToken()->getKey())->delete();

        return response()->json(['message' => __('auth.password_updated')]);
    }

    /**
     * In-app account deletion (required by the App Store). Same rules as the website:
     * password confirmation, blocked while upcoming bookings exist, soft delete.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password:sanctum'],
        ]);

        $user = $request->user();

        $hasUpcomingBookings = $user->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now()->toDateString())
            ->exists();

        if ($hasUpcomingBookings) {
            return response()->json(['message' => __('auth.delete_blocked_active_bookings')], 409);
        }

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->deviceTokens()->delete();
            $user->delete();
        });

        Log::warning('Customer account deleted from mobile app', ['user_id' => $user->id]);

        return response()->json(['message' => __('auth.account_deleted')]);
    }
}
