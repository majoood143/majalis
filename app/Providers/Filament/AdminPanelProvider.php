<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\SpatieLaravelTranslatablePlugin;
use App\Http\Middleware\SetUserLanguage;
use App\Livewire\LanguageSwitcher;
//use Filament\Panels\Enums\PanelsRenderHook;
use Filament\View\PanelsRenderHook;
use App\Filament\Pages\EditProfile;
use Illuminate\Support\Facades\Auth;
use Rmsramos\Activitylog\ActivitylogPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use Vormkracht10\FilamentMails\Facades\FilamentMails;
use Vormkracht10\FilamentMails\FilamentMailsPlugin;
use \Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('Cairo') // Arabic-friendly font
            ->brandName('Majalis Admin')
            ->brandLogo(asset('images/logo.webp'))
            ->darkModeBrandLogo(asset('images/logo.webp'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.ico'))
            ->passwordReset()
            ->profile()
            // ADD LOCALE CONFIGURATION

            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
                \App\Filament\Pages\Maintenance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                \App\Filament\Admin\Widgets\PayoutStatsWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
                ActivitylogPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make(),
                FilamentSpatieLaravelHealthPlugin::make(),
                FilamentMailsPlugin::make(),
                FilamentJobsMonitorPlugin::make()
                    ->enableNavigation(),

            ])
            ->authMiddleware([
                Authenticate::class,
                AuthenticateSession::class,
                StartSession::class,
                SetUserLanguage::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => view('filament.hooks.language-switcher')->render()
            )
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->routes(fn() => FilamentMails::routes())

            //->locale(config('app.locale'))
            //->locales(['en' => 'English', 'ar' => 'العربية'])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn(): string => Blade::render(
                    '<script src="{{ asset("js/filament-session-handler.js") }}" defer></script>'
                )
            )
            // ADD PLUGIN CONFIGURATION
            ->plugin(
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar'])
            );
    }
}
