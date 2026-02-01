<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure Filament Session Is Valid Middleware
 *
 * This middleware handles session expiration specifically for Filament panels.
 * It detects when a session has expired (including during Livewire/AJAX requests)
 * and returns the appropriate response to trigger a redirect to the login page.
 *
 * Key features:
 * - Handles standard HTTP requests with redirect
 * - Handles Livewire/AJAX requests with 419 status (session expired)
 * - Works with both admin and owner panels
 *
 * @package App\Http\Middleware
 */
class EnsureFilamentSessionIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for login pages and public routes
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        // Process the request
        $response = $next($request);

        // Check if the response indicates an authentication failure
        // This can happen when session expires mid-request
        if ($response->getStatusCode() === 401 || $response->getStatusCode() === 419) {
            return $this->handleUnauthenticated($request);
        }

        return $response;
    }

    /**
     * Handle unauthenticated request based on request type.
     *
     * For Livewire/AJAX requests: Return 419 status with redirect URL
     * For standard requests: Redirect to appropriate login page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleUnauthenticated(Request $request): Response
    {
        // Determine the correct login URL based on the panel
        $loginUrl = $this->getLoginUrl($request);

        // Handle Livewire/AJAX requests
        // Livewire needs a special response to trigger page reload
        if ($request->hasHeader('X-Livewire') || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Session expired. Please log in again.',
                'redirect' => $loginUrl,
            ], 419, [
                'X-Filament-Session-Expired' => 'true',
            ]);
        }

        // Standard HTTP redirect
        return redirect()->guest($loginUrl);
    }

    /**
     * Determine the appropriate login URL based on the current panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        // Check for admin panel
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('filament.admin.auth.login');
        }

        // Check for owner panel
        if ($request->is('owner') || $request->is('owner/*')) {
            return route('filament.owner.auth.login');
        }

        // Default login
        return route('login');
    }

    /**
     * Check if the current route is a public route (login, etc.)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isPublicRoute(Request $request): bool
    {
        $publicPatterns = [
            'admin/login',
            'owner/login',
            'login',
            'register',
            'password/*',
            'livewire/upload-file', // Livewire file uploads
        ];

        foreach ($publicPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
