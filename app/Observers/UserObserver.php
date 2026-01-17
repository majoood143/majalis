<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * UserObserver
 *
 * Observes User model events to perform automatic actions such as
 * linking guest bookings when a new user registers with an email
 * that has existing guest bookings.
 *
 * Registration Flow for Guest Bookings:
 * 1. User registers with email "user@example.com"
 * 2. Observer checks for guest bookings with same email
 * 3. If found, links all guest bookings to new user account
 * 4. Updates account_created_at timestamp on linked bookings
 *
 * @package App\Observers
 * @version 1.0.0
 */
class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * When a new user is created, check if there are any guest bookings
     * with the same email address and link them to the new user account.
     *
     * @param User $user The newly created user
     * @return void
     */
    public function created(User $user): void
    {
        $this->linkGuestBookings($user);
    }

    /**
     * Handle the User "updated" event.
     *
     * If user's email is changed, we might want to re-check for guest bookings.
     * This is optional and can be enabled based on business requirements.
     *
     * @param User $user The updated user
     * @return void
     */
    public function updated(User $user): void
    {
        // Optionally link guest bookings if email was changed
        if ($user->wasChanged('email')) {
            $this->linkGuestBookings($user);
        }
    }

    /**
     * Link guest bookings with matching email to the user.
     *
     * Finds all guest bookings where:
     * - is_guest_booking = true
     * - customer_email matches user's email
     * - user_id is NULL (not already linked)
     *
     * @param User $user The user to link bookings to
     * @return int Number of bookings linked
     */
    protected function linkGuestBookings(User $user): int
    {
        $email = strtolower($user->email);

        // Find unlinked guest bookings with matching email
        $linkedCount = Booking::where('is_guest_booking', true)
            ->where('customer_email', $email)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'account_created_at' => now(),
            ]);

        // Log the linking for audit purposes
        if ($linkedCount > 0) {
            Log::info('Guest bookings linked to new user account', [
                'user_id' => $user->id,
                'user_email' => $email,
                'bookings_linked' => $linkedCount,
            ]);
        }

        return $linkedCount;
    }
}
