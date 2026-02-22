<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

/**
 * GuestBookingServiceProvider
 *
 * Registers services, observers, and configurations related to
 * the guest booking feature.
 *
 * Features registered:
 * - User observer for auto-linking guest bookings
 * - Configuration bindings
 *
 * Registration in config/app.php:
 * ```php
 * 'providers' => [
 *     // ...
 *     App\Providers\GuestBookingServiceProvider::class,
 * ],
 * ```
 *
 * @package App\Providers
 * @version 1.0.0
 */
class GuestBookingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge guest booking configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/guest-booking.php',
            'guest-booking'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register observers
        User::observe(UserObserver::class);

        // Publish configuration (optional)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/guest-booking.php' => config_path('guest-booking.php'),
            ], 'guest-booking-config');
        }
    }
}
