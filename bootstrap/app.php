<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom Authenticate middleware
        $middleware->redirectGuestsTo(function (Request $request) {
            // Get current locale
            $locale = app()->getLocale();

            // Check if it's an admin panel request
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('filament.admin.auth.login');
            }

            // Check if it's a hall owner panel request
            if ($request->is('hall-owner') || $request->is('hall-owner/*')) {
                return route('filament.hall-owner.auth.login');
            }

            // Default to customer login
            return route('login', ['lang' => $locale]);
        });

        // Handle authenticated users trying to access guest routes
        $middleware->redirectUsersTo(function (Request $request) {
            // Check user role and redirect accordingly
            $user = auth()->user();

            if ($user) {
                // If user is admin or hall owner, redirect to their panel
                if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                    return route('filament.admin.pages.dashboard');
                }

                if ($user->hasRole('hall_owner')) {
                    return route('filament.hall-owner.pages.dashboard');
                }
            }

            // Default to customer dashboard
            return route('customer.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /**
         * Handle Authentication Exceptions (401 Unauthorized)
         *
         * Redirects unauthenticated users to the appropriate login page
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // For API requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.session_expired'),
                ], 401);
            }

            $locale = app()->getLocale();

            // Determine redirect based on URL
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()
                    ->guest(route('filament.admin.auth.login'))
                    ->with('warning', __('auth.session_expired_login_again'));
            }

            if ($request->is('hall-owner') || $request->is('hall-owner/*')) {
                return redirect()
                    ->guest(route('filament.hall-owner.auth.login'))
                    ->with('warning', __('auth.session_expired_login_again'));
            }

            // Customer login with locale
            return redirect()
                ->guest(route('login', ['lang' => $locale]))
                ->with('warning', __('auth.session_expired_login_again'));
        });

        /**
         * Handle Token Mismatch (CSRF) Exceptions (419 Session Expired)
         *
         * Occurs when session expires and user submits a form
         */
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            // For API requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.session_expired'),
                ], 419);
            }

            $locale = app()->getLocale();

            // Determine redirect based on URL
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()
                    ->guest(route('filament.admin.auth.login'))
                    ->with('error', __('auth.session_expired_login_again'));
            }

            if ($request->is('hall-owner') || $request->is('hall-owner/*')) {
                return redirect()
                    ->guest(route('filament.hall-owner.auth.login'))
                    ->with('error', __('auth.session_expired_login_again'));
            }

            // Customer login with locale
            return redirect()
                ->guest(route('login', ['lang' => $locale]))
                ->with('error', __('auth.session_expired_login_again'));
        });

        /**
         * Handle 403 Forbidden Exceptions
         *
         * Usually occurs when session expires or user lacks permissions
         */
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403) {
                // If user is not authenticated, treat as session expiration
                if (!auth()->check()) {
                    $locale = app()->getLocale();

                    // Determine redirect based on URL
                    if ($request->is('admin') || $request->is('admin/*')) {
                        return redirect()
                            ->guest(route('filament.admin.auth.login'))
                            ->with('warning', __('auth.session_expired_login_again'));
                    }

                    if ($request->is('hall-owner') || $request->is('hall-owner/*')) {
                        return redirect()
                            ->guest(route('filament.hall-owner.auth.login'))
                            ->with('warning', __('auth.session_expired_login_again'));
                    }

                    return redirect()
                        ->guest(route('login', ['lang' => $locale]))
                        ->with('warning', __('auth.session_expired_login_again'));
                }

                // If authenticated but forbidden (permission issue)
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('auth.forbidden'),
                    ], 403);
                }

                return back()->with('error', __('auth.forbidden'));
            }

            // Let other HTTP exceptions pass through
            return null;
        });
    })
    ->create();
