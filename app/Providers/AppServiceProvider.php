<?php

namespace App\Providers;

use App\View\Composers\PageNavigationComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use App\Services\BookingService;
use App\Services\CommissionService;
use App\Services\NotificationService;
use App\Services\PaymentService;
use App\Services\PDFService;
use Livewire\Livewire;
use App\Livewire\LanguageSwitcher;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });

        $this->app->singleton(PDFService::class, function ($app) {
            return new PDFService();
        });

        // ✅ FIX: CommissionService has no dependencies — register it
        $this->app->singleton(CommissionService::class, function ($app) {
            return new CommissionService();
        });

        // ✅ FIX: BookingService now requires CommissionService (not PaymentService)
        // OLD: new BookingService($app->make(PaymentService::class), $app->make(NotificationService::class))
        // NEW: new BookingService($app->make(CommissionService::class))
        $this->app->singleton(BookingService::class, function ($app) {
            return new BookingService(
                $app->make(CommissionService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureApiRateLimits();

        Livewire::component('language-switcher', LanguageSwitcher::class);
        Livewire::component('muhammad-nawlo.filament-sitemap-generator.widgets.sitemap-stats-widget', \MuhammadNawlo\FilamentSitemapGenerator\Widgets\SitemapStatsWidget::class);
        Livewire::component('muhammad-nawlo.filament-sitemap-generator.widgets.sitemap-overview-alerts-widget', \MuhammadNawlo\FilamentSitemapGenerator\Widgets\SitemapOverviewAlertsWidget::class);
        Livewire::component('muhammad-nawlo.filament-sitemap-generator.widgets.sitemap-preview-widget', \MuhammadNawlo\FilamentSitemapGenerator\Widgets\SitemapPreviewWidget::class);
        Livewire::component('muhammad-nawlo.filament-sitemap-generator.widgets.sitemap-recent-runs-widget', \MuhammadNawlo\FilamentSitemapGenerator\Widgets\SitemapRecentRunsWidget::class);
        Livewire::component('muhammad-nawlo.filament-sitemap-generator.widgets.sitemap-runs-table-widget', \MuhammadNawlo\FilamentSitemapGenerator\Widgets\SitemapRunsTableWidget::class);
        Health::checks([
            OptimizedAppCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            UsedDiskSpaceCheck::new(),
            PingCheck::new()->url('https://www.google.com'),
            QueueCheck::new(),
            DatabaseCheck::new(),
        ]);
        // Register view composers for navigation
        View::composer(
            ['layouts.header', 'layouts.footer'],
            PageNavigationComposer::class
        );
    }

    /**
     * Named rate limiters for the customer mobile API (/api/v1).
     *
     * Named limiters keep separate counters. Anonymous `throttle:N,1` limits are keyed
     * by the user alone, so nested ones share a counter and the strict limit would
     * count every API call the user makes.
     */
    protected function configureApiRateLimits(): void
    {
        $byUserOrIp = fn (Request $request) => $request->user()?->getAuthIdentifier() ?? $request->ip();

        RateLimiter::for('api-v1', fn (Request $request) => Limit::perMinute(120)->by($byUserOrIp($request)));

        RateLimiter::for('api-v1-availability', fn (Request $request) => Limit::perMinute(30)->by($byUserOrIp($request)));

        // Sign-in and sign-up: per email + IP, plus an overall cap per IP.
        RateLimiter::for('api-v1-auth', fn (Request $request) => [
            Limit::perMinute(6)->by(Str::lower((string) $request->input('email')) . '|' . $request->ip()),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('api-v1-password-reset', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));

        // Creating bookings and starting payments.
        RateLimiter::for('api-v1-booking', fn (Request $request) => Limit::perMinute(10)->by($byUserOrIp($request)));

        // Password change and account deletion.
        RateLimiter::for('api-v1-sensitive', fn (Request $request) => Limit::perMinute(6)->by($byUserOrIp($request)));
    }
}
