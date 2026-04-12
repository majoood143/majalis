<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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

        Livewire::component('language-switcher', LanguageSwitcher::class);
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
            \App\View\Composers\PageNavigationComposer::class
        );
    }
}
