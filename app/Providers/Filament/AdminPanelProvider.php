<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use App\Filament\Pages\Maintenance;
use App\Filament\Admin\Widgets\PayoutStatsWidget;
use App\Filament\Admin\Pages\EnvEditor;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SetUserLanguage;
//use Filament\Panels\Enums\PanelsRenderHook;
use Filament\View\PanelsRenderHook;
use App\Filament\Pages\EditProfile;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use Backstage\Mails\Mails;
use Backstage\Mails\MailsPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Illuminate\Support\Facades\Blade;
use JeffersonGoncalves\Filament\Gtag\GtagPlugin;
use GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin;
use WallaceMartinss\FilamentEvolution\FilamentEvolutionPlugin;




class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->default()
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->profile()
            ->colors([
                // 'primary' => Color::generateV3Palette('#B9916D'),
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
            // ADD LOCALE CONFIGURATION

            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            EditProfile::class,
                Maintenance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                PayoutStatsWidget::class,
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
                SetUserLanguage::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                // TODO: replace rmsramos/activitylog (v3-only) with a v4-compatible activity log plugin
                FilamentSpatieLaravelBackupPlugin::make(),
                FilamentSpatieLaravelHealthPlugin::make(),
                //MailsPlugin::make(),
                FilamentJobsMonitorPlugin::make()
                    ->enableNavigation(),
                GtagPlugin::make(),
                FilamentEnvEditorPlugin::make()
                    ->viewPage(EnvEditor::class),
            //FilamentEvolutionPlugin::make(),
            //\MarcoGermani87\FilamentCaptcha\FilamentCaptcha::make(),
            //KnowledgeBasePlugin::make(),


            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => view('filament.hooks.language-switcher')->render()
            )
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->routes(fn() => Mails::routes())

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
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ar']),



            );
    }
}
