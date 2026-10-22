<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the same access rules to a panel API as to the panel itself.
 *
 * Must run after `auth:sanctum`. The token user has to pass
 * User::canAccessPanel() for the panel (active account, admin role for
 * /api/admin, verified hall owner for /api/owner).
 *
 * It also makes the panel "current" and binds the token user to the panel's
 * session guard, so resource code that calls Filament::auth() or
 * Auth::guard('web') sees the same user as Auth::user().
 */
class EnsureApiPanelAccess
{
    public function handle(Request $request, Closure $next, string $panelId): Response
    {
        $panel = Filament::getPanel($panelId);
        $user = $request->user();

        if (! $user instanceof FilamentUser || ! $user->canAccessPanel($panel)) {
            return response()->json([
                'message' => __('auth.forbidden'),
            ], 403);
        }

        Filament::setCurrentPanel($panel);
        Auth::guard($panel->getAuthGuard())->setUser($user);

        return $next($request);
    }
}
