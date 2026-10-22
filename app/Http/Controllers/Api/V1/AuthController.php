<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

/**
 * Token authentication for the customer mobile app.
 *
 * Each device gets its own Sanctum token named after `device_name`; signing in
 * again on the same device replaces that device's token. Sign-out only revokes
 * the current device (unlike the panel API's /api/auth/logout, which revokes all).
 */
class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\s-]{7,20}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'language_preference' => app()->getLocale(),
        ]);

        // Same as the website: sends the verification email and links earlier guest bookings.
        event(new Registered($user));

        return $this->tokenResponse($user, $validated['device_name'], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::where('email', Str::lower($validated['email']))->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => __('auth.inactive')], 403);
        }

        return $this->tokenResponse($user, $validated['device_name']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('auth.logged_out')]);
    }

    /**
     * Always answers the same way so the endpoint can't be used to find registered emails.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink(['email' => Str::lower($validated['email'])]);

        return response()->json(['message' => __('auth.reset_link_sent')]);
    }

    protected function tokenResponse(User $user, string $deviceName, int $status = 200): JsonResponse
    {
        $user->tokens()->where('name', $deviceName)->delete();

        $expiration = config('sanctum.expiration');
        $token = $user->createToken(
            $deviceName,
            ['*'],
            $expiration ? now()->addMinutes((int) $expiration) : null,
        );

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'token_expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'user' => new UserResource($user->refresh()),
            ],
        ], $status);
    }
}
