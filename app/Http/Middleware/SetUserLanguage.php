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
 * Middleware to set application locale based on user preference
 *
 * This middleware handles locale detection and setting with proper
 * null-safety for Filament admin panel compatibility
 */
class SetUserLanguage
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on user preference or session
     * with proper null-safety checks for Filament authentication flow
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Initialize locale variable
        $locale = null;

        // STEP 1: Check URL parameter first (highest priority)
        $urlLocale = $request->get('locale');

        if ($urlLocale && in_array($urlLocale, $this->getAvailableLocales(), true)) {
            // Store locale in session if provided via URL
            Session::put('locale', $urlLocale);
            $locale = $urlLocale;

            // STEP 2: Safely update user preference if authenticated
            // Only update if user is fully loaded and not in authentication flow
            $this->updateUserLanguagePreference($urlLocale);
        }
        // STEP 3: Check authenticated user with null-safety
        elseif ($this->hasAuthenticatedUser()) {
            // Use null-safe operator to access user property
            $user = $this->getAuthenticatedUser();

            // Safely get language preference with fallback
            $locale = $user?->language_preference ?? Session::get('locale') ?? config('app.locale', 'en');

            // Only store in session if we got a valid locale
            if ($locale) {
                Session::put('locale', $locale);
            }
        }
        // STEP 4: Fall back to session locale for guests
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // STEP 5: Use default locale as final fallback
        else {
            $locale = config('app.locale', 'en');
        }

        // STEP 6: Validate and set locale
        if ($locale && in_array($locale, $this->getAvailableLocales(), true)) {
            App::setLocale($locale);

            // Set direction for RTL languages
            $this->setTextDirection($locale);
        }

        return $next($request);
    }

    /**
     * Check if user is authenticated with null-safety
     *
     * This method safely checks authentication without causing
     * errors during Filament's authentication flow
     *
     * @return bool
     */
    protected function hasAuthenticatedUser(): bool
    {
        try {
            // Get the appropriate guard for the current request
            $guard = $this->getGuardName();

            // Check if user is authenticated on this guard
            return Auth::guard($guard)->check() && Auth::guard($guard)->user() !== null;
        } catch (\Exception $e) {
            // If any error occurs during auth check, safely return false
            return false;
        }
    }

    /**
     * Get authenticated user safely
     *
     * Returns the authenticated user or null without throwing exceptions
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
     * Update user language preference safely
     *
     * Only updates if user is authenticated and the update is safe
     * Wrapped in try-catch to prevent middleware failures
     *
     * @param string $locale
     * @return void
     */
    protected function updateUserLanguagePreference(string $locale): void
    {
        try {
            // Only update if user exists and has the language_preference column
            $user = $this->getAuthenticatedUser();

            if ($user && method_exists($user, 'update') && $user->exists) {
                // Check if language_preference attribute exists on model
                if (array_key_exists('language_preference', $user->getAttributes()) ||
                    $user->isFillable('language_preference')) {
                    $user->update(['language_preference' => $locale]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail - don't break the request if update fails
            // You can log this if needed: Log::warning('Failed to update user language preference', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get the guard name for current request
     *
     * Detects if request is for Filament panel and uses appropriate guard
     *
     * @return string|null
     */
    protected function getGuardName(): ?string
    {
        // Check if this is a Filament admin request
        if (str_starts_with(request()->path(), 'admin')) {
            // Use Filament's configured guard (check your filament panel config)
            return config('filament.auth.guard', 'web');
        }

        // Default to web guard
        return 'web';
    }

    /**
     * Get available locales from configuration
     *
     * @return array<string>
     */
    protected function getAvailableLocales(): array
    {
        return config('app.available_locales', ['en', 'ar']);
    }

    /**
     * Set text direction based on locale
     *
     * Shares the text direction with all views for proper RTL support
     *
     * @param string $locale
     * @return void
     */
    protected function setTextDirection(string $locale): void
    {
        // Define RTL languages
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];

        // Determine direction
        $direction = in_array($locale, $rtlLocales, true) ? 'rtl' : 'ltr';

        // Share direction with all views
        view()->share('textDirection', $direction);
    }
}
