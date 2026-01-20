<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetUserLanguage;
use App\Filament\Pages\EditProfile;
use Filament\View\PanelsRenderHook;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class OwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('owner')
            ->path('owner')
            ->login()  // This enables the login page
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info' => Color::Sky,
            ])
            ->font('Cairo') // Arabic-friendly font
            ->brandName(__('owner.brand.name', ['app' => config('app.name')]))
            ->brandLogo(asset('images/logo.webp'))
            ->darkModeBrandLogo(asset('images/logo.webp'))
            ->favicon(asset('images/favicon.ico'))
            ->discoverResources(in: app_path('Filament/Owner/Resources'), for: 'App\\Filament\\Owner\\Resources')
            ->discoverPages(in: app_path('Filament/Owner/Pages'), for: 'App\\Filament\\Owner\\Pages')
            ->discoverWidgets(in: app_path('Filament/Owner/Widgets'), for: 'App\\Filament\\Owner\\Widgets')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Owner/Widgets'), for: 'App\\Filament\\Owner\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                \App\Filament\Owner\Widgets\AvailabilityCalendarWidget::class,

            ])
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
            ->authMiddleware([
                Authenticate::class,
                SetUserLanguage::class,
            ])
            ->authGuard('web')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => view('filament.hooks.language-switcher')->render()
            )
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                __('owner.nav_groups.overview'),
                __('owner.nav_groups.hall_management'),
                __('owner.nav_groups.bookings'),
                __('owner.nav_groups.finance'),
                __('owner.nav_groups.settings'),
            ])
            ->plugin(
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar'])
            )
            /*
            |--------------------------------------------------------------------------
            | FullCalendar Plugin Configuration
            |--------------------------------------------------------------------------
            |
            | Register the FullCalendar plugin with default settings.
            | These can be overridden per-widget.
            |
            */
            ->plugins([
                FilamentFullCalendarPlugin::make()
                    // Allow clicking/dragging to select dates
                    ->selectable(true)
                    // Allow dragging/resizing events
                    ->editable(true)
                    // Set timezone (Oman timezone)
                    ->timezone('Asia/Muscat')
                    // Set locale based on app locale
                    ->locale(config('app.locale', 'en'))
                    // Configure available plugins
                    ->plugins([
                        'dayGrid',      // Month/day grid views
                        'timeGrid',     // Week/day time grid views
                        'interaction',  // Required for selectable/editable
                        'list',         // List views
                    ])
                    // Additional FullCalendar config
                    ->config([
                        'headerToolbar' => [
                            'left' => 'prev,next today',
                            'center' => 'title',
                            'right' => 'dayGridMonth,timeGridWeek,listWeek',
                        ],
                        'initialView' => 'dayGridMonth',
                        'firstDay' => 6, // Start week on Saturday (Omani weekend)
                        'slotMinTime' => '08:00:00',
                        'slotMaxTime' => '23:00:00',
                        'allDaySlot' => true,
                        'nowIndicator' => true,
                        'dayMaxEvents' => true, // Show "more" link when too many events
                        'eventDisplay' => 'block',
                        'displayEventTime' => true,
                        'displayEventEnd' => true,
                        'eventTimeFormat' => [
                            'hour' => '2-digit',
                            'minute' => '2-digit',
                            'meridiem' => 'short',
                        ],
                        // Responsive settings
                        'handleWindowResize' => true,
                        'expandRows' => true,
                        // Business hours (typical Omani working hours)
                        'businessHours' => [
                            'daysOfWeek' => [0, 1, 2, 3, 4], // Sun-Thu
                            'startTime' => '08:00',
                            'endTime' => '22:00',
                        ],
                    ]),
            ]);
    }
}
