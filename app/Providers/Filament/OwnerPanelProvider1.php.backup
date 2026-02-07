<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Http\Middleware\EnsureFilamentSessionIsValid;
use App\Http\Middleware\SetUserLanguage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

/**
 * Owner Panel Provider
 *
 * Configures the Filament panel for hall owners.
 * Includes session expiration handling for proper login redirects.
 *
 * @package App\Providers\Filament
 */
class OwnerPanelProvider extends PanelProvider
{
    /**
     * Configure the owner panel.
     *
     * @param  \Filament\Panel  $panel
     * @return \Filament\Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ==================== PANEL IDENTITY ====================
            ->id('owner')
            ->path('owner')

            // ==================== AUTHENTICATION ====================
            // Enable login page and configure auth guard
            ->login()
            ->authGuard('web')  // Explicitly set the auth guard
            ->authPasswordBroker('users')  // Password reset broker

            // ==================== BRANDING ====================
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info' => Color::Sky,
            ])
            ->font('Cairo')
            ->brandName(__('owner.brand.name', ['app' => config('app.name')]))
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.png'))

            // ==================== RESOURCE DISCOVERY ====================
            ->discoverResources(
                in: app_path('Filament/Owner/Resources'),
                for: 'App\\Filament\\Owner\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Owner/Pages'),
                for: 'App\\Filament\\Owner\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Owner/Widgets'),
                for: 'App\\Filament\\Owner\\Widgets'
            )
            ->widgets([
                \App\Filament\Owner\Widgets\AvailabilityCalendarWidget::class,
            ])

            // ==================== MIDDLEWARE STACK ====================
            // Order matters! Session must be started before authentication
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetUserLanguage::class,
            ])
            // Authentication middleware - runs after session is established
            ->authMiddleware([
                Authenticate::class,
                // AuthenticateSession invalidates session on password change
                AuthenticateSession::class,
                // Custom session validation for proper redirect handling
                EnsureFilamentSessionIsValid::class,
            ])

            // ==================== RENDER HOOKS ====================
            // Inject session expiration handler script
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render(
                    '<script src="{{ asset("js/filament-session-handler.js") }}" defer></script>'
                )
            )

            // ==================== PLUGINS ====================
            ->plugins([
                FilamentFullCalendarPlugin::make(),
            ]);
    }
}
