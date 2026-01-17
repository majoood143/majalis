<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Booking;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Link Guest Bookings On Registration Listener
 *
 * This listener automatically links all guest bookings made with
 * the same email address to a newly registered user account.
 *
 * Triggered when: User registers a new account
 * Action: Finds all guest bookings with matching email and links them
 *
 * This ensures that if a user:
 * 1. Makes guest bookings
 * 2. Later creates an account
 * The bookings are automatically associated with their account.
 *
 * @package App\Listeners
 * @version 1.0.0
 */
class LinkGuestBookingsOnRegistration implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Handle the event.
     *
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Check if user has an email
        if (!$user->email) {
            return;
        }

        // Find and link all guest bookings with matching email
        $linkedCount = Booking::guestByEmail($user->email)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'account_created_at' => now(),
            ]);

        // Log if any bookings were linked
        if ($linkedCount > 0) {
            Log::info('Linked guest bookings to new user account', [
                'user_id' => $user->id,
                'email' => $user->email,
                'bookings_linked' => $linkedCount,
            ]);
        }
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param Registered $event
     * @return bool
     */
    public function shouldQueue(Registered $event): bool
    {
        // Only queue if user has an email
        return !empty($event->user->email);
    }
}
