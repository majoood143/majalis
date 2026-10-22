<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rejects tokens of deactivated users on the customer API (/api/v1).
 *
 * The token itself is revoked so the app falls back to the signed-out state.
 */
class EnsureApiUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            $user->currentAccessToken()?->delete();

            return response()->json(['message' => __('auth.inactive')], 403);
        }

        return $next($request);
    }
}
