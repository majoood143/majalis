<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetUserLanguage
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on user preference or session
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Initialize locale variable
        $locale = null;

        // Priority order: URL parameter > User preference > Session > Default
        $urlLocale = $request->get('locale');

        if ($urlLocale && in_array($urlLocale, $this->getAvailableLocales())) {
            // Store locale in session if provided via URL
            Session::put('locale', $urlLocale);
            $locale = $urlLocale;

            // Update user preference if authenticated
            if (Auth::check() && Auth::user()) {
                Auth::user()->update(['language_preference' => $urlLocale]);
            }
        } elseif (Auth::check() && Auth::user()) {
            // Use authenticated user's preference
            $locale = Auth::user()->language_preference ?? config('app.locale', 'en');
            Session::put('locale', $locale);
        } elseif (Session::has('locale')) {
            // Use session locale for guests
            $locale = Session::get('locale');
        } else {
            // Fall back to default locale
            $locale = config('app.locale', 'en');
        }

        // Validate locale before setting
        if (in_array($locale, $this->getAvailableLocales())) {
            App::setLocale($locale);

            // Set direction for RTL languages
            $this->setTextDirection($locale);
        }

        return $next($request);
    }

    /**
     * Get available locales from configuration
     *
     * @return array
     */
    protected function getAvailableLocales(): array
    {
        return config('app.available_locales', ['en', 'ar']);
    }

    /**
     * Set text direction based on locale
     *
     * @param string $locale
     * @return void
     */
    protected function setTextDirection(string $locale): void
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];

        // Share direction with all views
        view()->share('textDirection', in_array($locale, $rtlLocales) ? 'rtl' : 'ltr');
    }
}
