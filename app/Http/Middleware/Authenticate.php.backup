<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Authenticate Middleware
 *
 * Handles authentication redirection for different areas:
 * - Customer area → customer login
 * - Admin panel → admin login (Filament)
 * - Hall owner panel → hall owner login (Filament)
 *
 * @package App\Http\Middleware
 */
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        // Don't redirect if expecting JSON (API requests)
        if ($request->expectsJson()) {
            return null;
        }

        // Get current locale for proper redirection
        $locale = app()->getLocale();

        // Check if it's an admin panel request (Filament)
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('filament.admin.auth.login');
        }

        // Check if it's a hall owner panel request (Filament)
        if ($request->is('hall-owner') || $request->is('hall-owner/*')) {
            return route('filament.hall-owner.auth.login');
        }

        // Default to customer login with locale
        return route('login', ['lang' => $locale]);
    }
}
