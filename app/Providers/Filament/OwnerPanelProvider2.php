<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Owner\Pages\Auth\Login;
use App\Http\Middleware\EnsureUserIsOwner;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class OwnerPanelProvider extends PanelProvider
{
    /**
     * Bootstrap the Owner panel configuration
     *
     * @param Panel $panel
     * @return Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            // Basic Configuration
            ->id('owner')
            ->path('owner')
            ->login(Login::class)

            // Branding & Appearance
            ->brandName(fn() => __('owner.brand.name', ['app' => config('app.name')]))
            ->brandLogo(fn() => asset('images/owner-logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))

            // Color Scheme - Professional theme for hall owners
            ->colors([
                'primary' => Color::Indigo,
                'secondary' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Sky,
                'gray' => Color::Gray,
            ])

            // Typography & Layout
            ->font('Cairo') // Excellent Arabic support
            ->maxContentWidth(MaxWidth::ScreenTwoExtraLarge)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->collapsedSidebarWidth('4rem')

            // Navigation Configuration
            ->navigation(true)
            ->topNavigation(false) // Sidebar navigation for better organization
            ->breadcrumbs(true)

            // Navigation Groups - Organized by business function
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.overview'))
                    ->icon('heroicon-o-home')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.hall_management'))
                    ->icon('heroicon-o-building-office')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.bookings'))
                    ->icon('heroicon-o-calendar-days')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.finance'))
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(false),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.customers'))
                    ->icon('heroicon-o-users')
                    ->collapsed(true),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.reports'))
                    ->icon('heroicon-o-chart-bar-square')
                    ->collapsed(true),

                NavigationGroup::make()
                    ->label(fn() => __('owner.nav_groups.settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])

            // Auto-discovery Paths
            ->discoverResources(
                in: app_path('Filament/Owner/Resources'),
                for: 'App\\Filament\\Owner\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Owner/Pages'),
                for: 'App\\Filament\\Owner\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Owner/Widgets'),
                for: 'App\\Filament\\Owner\\Widgets'
            )

            // Default Widgets
            ->widgets([
                // We'll add custom widgets in Part 2
            ])

            // Middleware Stack
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            // Authentication Middleware
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsOwner::class,
            ])

            // Features & Plugins
            ->plugin(
                \Filament\SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['ar', 'en'])
            )

            // Notifications
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')

            // User Menu Items
            ->userMenuItems([
                'profile' => Pages\MenuItem::make()
                    ->label(fn() => __('owner.user_menu.profile'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn() => route('filament.owner.pages.profile')),

                'hall-settings' => Pages\MenuItem::make()
                    ->label(fn() => __('owner.user_menu.hall_settings'))
                    ->icon('heroicon-o-cog')
                    ->url(fn() => route('filament.owner.pages.settings')),

                'help' => Pages\MenuItem::make()
                    ->label(fn() => __('owner.user_menu.help'))
                    ->icon('heroicon-o-question-mark-circle')
                    ->url(fn() => route('filament.owner.pages.help'))
                    ->openUrlInNewTab(),
            ])

            // Global Search
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn() => __('owner.search.suffix'))

            // Additional Features
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->persistFiltersInSession()
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => view('filament.owner.footer')
            );
    }

    /**
     * Register method for service provider
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Boot method for service provider
     */
    public function boot(): void
    {
        parent::boot();
    }
}
