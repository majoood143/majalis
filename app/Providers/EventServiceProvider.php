<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Booking\BookingApproved;
use App\Events\Booking\BookingRejected;
use App\Listeners\Booking\SendBookingApprovedNotification;
use App\Listeners\Booking\SendBookingRejectedNotification;
use App\Listeners\LinkGuestBookingsOnRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use Backstage\FilamentMails\Listeners\UpdateMailStatus;

/**
 * EventServiceProvider
 *
 * Registers event-listener mappings for the application.
 *
 * NOTE: If you already have an EventServiceProvider, merge the $listen array
 * with your existing events.
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [

        Registered::class => [
            LinkGuestBookingsOnRegistration::class,
        ],
        \Illuminate\Mail\Events\MessageSent::class => [
            UpdateMailStatus::class,
        ],
        // =========================================================
        // BOOKING EVENTS
        // =========================================================

        /**
         * Booking Approved Event
         * Triggered when a hall owner approves a pending booking.
         */
        BookingApproved::class => [
            SendBookingApprovedNotification::class,
        ],

        /**
         * Booking Rejected Event
         * Triggered when a hall owner rejects a pending booking.
         */
        BookingRejected::class => [
            SendBookingRejectedNotification::class,
        ],

        // =========================================================
        // FUTURE EVENTS (uncomment when implemented)
        // =========================================================

        // BookingConfirmed::class => [
        //     SendBookingConfirmedNotification::class,
        // ],

        // BookingCancelled::class => [
        //     SendBookingCancelledNotification::class,
        // ],

        // PaymentReceived::class => [
        //     SendPaymentReceivedNotification::class,
        // ],

        // BalanceReceived::class => [
        //     SendBalanceReceivedNotification::class,
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
