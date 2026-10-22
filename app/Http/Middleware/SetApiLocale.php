<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale for API requests.
 *
 * Mobile clients pick the language with `?lang=ar|en` or the `Accept-Language`
 * header. Translatable attributes are returned in this locale.
 */
class SetApiLocale
{
    protected const SUPPORTED = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang')
            ?? ($request->headers->has('Accept-Language') ? $request->getPreferredLanguage(self::SUPPORTED) : null);

        if (in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
