<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Ticket;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Link Guest Tickets On Registration
 *
 * When a new user registers, find all guest-submitted tickets whose
 * metadata.guest_email matches the new account's email and link them
 * by setting user_id — identical pattern to LinkGuestBookingsOnRegistration.
 */
class LinkGuestTicketsOnRegistration implements ShouldQueue
{
    public int $tries = 3;

    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (! $user->email) {
            return;
        }

        // Match on the JSON metadata field guest_email
        $linked = Ticket::whereNull('user_id')
            ->whereJsonContains('metadata->guest_email', strtolower($user->email))
            ->update(['user_id' => $user->id]);

        if ($linked > 0) {
            Log::info('Linked guest tickets to new user account', [
                'user_id'        => $user->id,
                'email'          => $user->email,
                'tickets_linked' => $linked,
            ]);
        }
    }

    public function shouldQueue(Registered $event): bool
    {
        return ! empty($event->user->email);
    }
}
