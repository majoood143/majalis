<?php

declare(strict_types=1);

/**
 * English Enum Translations
 *
 * Contains translation keys for all application enums.
 *
 * @package Lang\En
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Payout Status
    |--------------------------------------------------------------------------
    */
    'payout_status' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'on_hold' => 'On Hold',
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Status
    |--------------------------------------------------------------------------
    */
    'booking_status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    */
    'payment_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'partial' => 'Partial',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Type
    |--------------------------------------------------------------------------
    */
    'payment_type' => [
        'full' => 'Full Payment',
        'advance' => 'Advance Payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Slots
    |--------------------------------------------------------------------------
    */
    'time_slot' => [
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'full_day' => 'Full Day',
    ],
];
