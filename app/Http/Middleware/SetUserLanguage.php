<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set application locale based on user preference.
 *
 * Handles locale detection and setting with proper null-safety
 * for Filament admin panel compatibility.
 *
 * Priority order:
 *   1. URL parameter (?locale= or ?lang=) — highest priority
 *   2. Session stored locale (persists across requests)
 *   3. Authenticated user's language_preference
 *   4. Default app locale (config fallback)
 *
 * FIX APPLIED (2026-02-22):
 *   ✅ Checks BOTH ?locale= AND ?lang= query parameters
 *   ✅ Session locale takes priority over user DB preference
 *      (so clicking the switcher actually sticks)
 *   ✅ Redirects to clean URL after setting locale in session
 *      (prevents bookmark/share issues with ?lang= in URL)
 *   ✅ Fixed redirect()->response() crash in expired session handler
 *
 * @package App\Http\Middleware
 */
class SetUserLanguage
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on user preference or session
     * with proper null-safety checks for Filament authentication flow.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ─────────────────────────────────────────────────────────
        // GUARD: Check for expired session before processing
        // ─────────────────────────────────────────────────────────
        if ($this->hasExpiredSession($request)) {
            $request->session()->flush();
            Auth::logout();

            $redirectUrl = $this->getRedirectUrlForExpiredSession($request);

            // FIX: redirect() already returns a valid RedirectResponse
            // The old ->response($request) call doesn't exist in Laravel 12
            return redirect($redirectUrl);
        }

        // Initialize locale variable
        $locale = null;

        // ─────────────────────────────────────────────────────────
        // STEP 1: Check URL parameter first (highest priority)
        //
        // FIX: Check BOTH ?locale= (used by Filament panels) AND
        //      ?lang= (used by customer blade templates).
        // ─────────────────────────────────────────────────────────
        $urlLocale = $request->query('locale') ?? $request->query('lang');

        if ($urlLocale && in_array($urlLocale, $this->getAvailableLocales(), true)) {
            // Store in session — this is the source of truth going forward
            Session::put('locale', $urlLocale);
            $locale = $urlLocale;

            // Attempt to persist to user's DB record (fire-and-forget)
            $this->updateUserLanguagePreference($urlLocale);

            // ─────────────────────────────────────────────────────
            // FIX: Redirect to the SAME URL without the lang/locale
            // query param. This ensures:
            //   a) The session holds the locale (sticky across pages)
            //   b) The URL stays clean (no ?lang=ar in bookmarks)
            //   c) Browser back/forward doesn't flip languages
            // ─────────────────────────────────────────────────────
            $cleanUrl = $this->removeLocaleQueryParams($request);

            // Only redirect if the URL actually changed (avoid infinite loop)
            if ($cleanUrl !== $request->fullUrl()) {
                // Set locale before redirect so the redirected request
                // picks it up from session immediately
                App::setLocale($locale);
                $this->setTextDirection($locale);

                return redirect($cleanUrl);
            }
        }

        // ─────────────────────────────────────────────────────────
        // STEP 2: Check session (persists the user's last choice)
        //
        // FIX: Session now takes priority OVER the user's DB
        // language_preference. This is critical because:
        //   - The user clicks EN → session = 'en'
        //   - Next request: if we check DB first and DB still
        //     says 'ar' (update failed silently), the switch
        //     appears broken.
        //   - By checking session first, the click always works.
        // ─────────────────────────────────────────────────────────
        if (! $locale && Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $this->getAvailableLocales(), true)) {
                $locale = $sessionLocale;
            }
        }

        // ─────────────────────────────────────────────────────────
        // STEP 3: Check authenticated user's DB preference
        // (only if session didn't have a locale)
        // ─────────────────────────────────────────────────────────
        if (! $locale && $this->hasAuthenticatedUser()) {
            $user = $this->getAuthenticatedUser();
            $userLocale = $user?->language_preference;

            if ($userLocale && in_array($userLocale, $this->getAvailableLocales(), true)) {
                $locale = $userLocale;
                // Sync to session so future requests use session path
                Session::put('locale', $locale);
            }
        }

        // ─────────────────────────────────────────────────────────
        // STEP 4: Use default locale as final fallback
        // ─────────────────────────────────────────────────────────
        if (! $locale) {
            $locale = config('app.locale', 'en');
        }

        // ─────────────────────────────────────────────────────────
        // STEP 5: Apply locale to the application
        // ─────────────────────────────────────────────────────────
        if (in_array($locale, $this->getAvailableLocales(), true)) {
            App::setLocale($locale);
            $this->setTextDirection($locale);
        }

        return $next($request);
    }

    /**
     * Remove locale/lang query parameters from the current URL.
     *
     * Returns a clean URL suitable for redirect after storing
     * the locale in the session.
     *
     * @param  Request  $request
     * @return string
     */
    protected function removeLocaleQueryParams(Request $request): string
    {
        // Get all current query parameters
        $query = $request->query();

        // Remove both locale and lang keys
        unset($query['locale'], $query['lang']);

        // Rebuild the URL
        $baseUrl = $request->url(); // URL without query string

        if (! empty($query)) {
            return $baseUrl . '?' . http_build_query($query);
        }

        return $baseUrl;
    }

    /**
     * Check if session has expired.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function hasExpiredSession(Request $request): bool
    {
        // Don't check expiration for login/logout/password pages
        if (
            $request->routeIs('login') || $request->routeIs('logout') ||
            $request->routeIs('password.*') || $request->is('auth/*')
        ) {
            return false;
        }

        // Check if session has a last_activity timestamp
        if ($request->session()->has('_token') && $request->session()->has('last_activity')) {
            $lastActivity = $request->session()->get('last_activity');
            $sessionLifetime = (int) config('session.lifetime', 120); // minutes

            if (now()->diffInMinutes($lastActivity) > $sessionLifetime) {
                return true;
            }
        }

        // Update last activity timestamp
        if ($request->session()->has('_token')) {
            $request->session()->put('last_activity', now());
        }

        return false;
    }

    /**
     * Get redirect URL for expired session based on context.
     *
     * @param  Request  $request
     * @return string
     */
    protected function getRedirectUrlForExpiredSession(Request $request): string
    {
        // Filament admin panel → admin login
        if (str_starts_with($request->path(), 'admin')) {
            return route('filament.admin.auth.login');
        }

        // Filament owner panel → owner login
        if (str_starts_with($request->path(), 'owner')) {
            return route('filament.owner.auth.login');
        }

        // Default → customer login
        return route('login');
    }

    /**
     * Check if user is authenticated with null-safety.
     *
     * Safely checks authentication without causing errors
     * during Filament's authentication flow.
     *
     * @return bool
     */
    protected function hasAuthenticatedUser(): bool
    {
        try {
            $guard = $this->getGuardName();

            return Auth::guard($guard)->check()
                && Auth::guard($guard)->user() !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get authenticated user safely.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function getAuthenticatedUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        try {
            $guard = $this->getGuardName();
            return Auth::guard($guard)->user();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update user language preference safely.
     *
     * Fire-and-forget: wrapped in try-catch so a DB failure
     * never breaks the language switch (session is the real
     * source of truth).
     *
     * @param  string  $locale
     * @return void
     */
    protected function updateUserLanguagePreference(string $locale): void
    {
        try {
            $user = $this->getAuthenticatedUser();

            if ($user && method_exists($user, 'update') && $user->exists) {
                if (
                    array_key_exists('language_preference', $user->getAttributes()) ||
                    $user->isFillable('language_preference')
                ) {
                    $user->update(['language_preference' => $locale]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail — session already has the correct locale
        }
    }

    /**
     * Get the guard name for current request.
     *
     * @return string|null
     */
    protected function getGuardName(): ?string
    {
        if (str_starts_with(request()->path(), 'admin')) {
            return config('filament.auth.guard', 'web');
        }

        return 'web';
    }

    /**
     * Get available locales from configuration.
     *
     * @return array<string>
     */
    protected function getAvailableLocales(): array
    {
        return config('app.available_locales', ['en', 'ar']);
    }

    /**
     * Set text direction based on locale.
     *
     * Shares the text direction with all views for proper RTL support.
     *
     * @param  string  $locale
     * @return void
     */
    protected function setTextDirection(string $locale): void
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];
        $direction = in_array($locale, $rtlLocales, true) ? 'rtl' : 'ltr';

        view()->share('textDirection', $direction);
    }
}
