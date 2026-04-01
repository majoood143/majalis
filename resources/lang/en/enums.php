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

    /*
    |--------------------------------------------------------------------------
    | Ticket Type
    |--------------------------------------------------------------------------
    */
    'ticket_type' => [
        'claim'        => 'Claim',
        'complaint'    => 'Complaint',
        'inquiry'      => 'Inquiry',
        'refund'       => 'Refund Request',
        'cancellation' => 'Cancellation Request',
        'technical'    => 'Technical Issue',
        'feedback'     => 'Feedback',
        'other'        => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Priority
    |--------------------------------------------------------------------------
    */
    'ticket_priority' => [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Status
    |--------------------------------------------------------------------------
    */
    'ticket_status' => [
        'open'        => 'Open',
        'pending'     => 'Pending Response',
        'in_progress' => 'In Progress',
        'on_hold'     => 'On Hold',
        'resolved'    => 'Resolved',
        'closed'      => 'Closed',
        'cancelled'   => 'Cancelled',
        'escalated'   => 'Escalated',
    ],
];
