<?php

return [
    // Resource labels
    'singular'          => 'Guest Session',
    'plural'            => 'Guest Sessions',
    'navigation_label'  => 'Guest Sessions',

    // Sections
    'guest_information'   => 'Guest Information',
    'session_status'      => 'Session Status',
    'booking_information' => 'Booking Information',
    'security_information'=> 'Security Information',
    'timestamps'          => 'Timestamps',

    // Fields
    'name'            => 'Name',
    'email'           => 'Email',
    'phone'           => 'Phone',
    'session_token'   => 'Session Token',
    'status'          => 'Status',
    'is_verified'     => 'Verified',
    'otp_attempts'    => 'OTP Attempts',
    'verified_at'     => 'Verified At',
    'expires_at'      => 'Expires At',
    'otp_expires_at'  => 'OTP Expires At',
    'hall'            => 'Hall',
    'booking'         => 'Booking',
    'booking_data'    => 'Booking Data',
    'ip_address'      => 'IP Address',
    'user_agent'      => 'User Agent',
    'created_at'      => 'Created At',
    'updated_at'      => 'Updated At',

    // Statuses
    'status_pending'   => 'Awaiting Verification',
    'status_verified'  => 'Verified',
    'status_booking'   => 'Creating Booking',
    'status_payment'   => 'Processing Payment',
    'status_completed' => 'Completed',
    'status_expired'   => 'Expired',
    'status_cancelled' => 'Cancelled',

    // Filters
    'verified_only'   => 'Verified only',
    'unverified_only' => 'Unverified only',
    'filter_expired'  => 'Expired sessions',
    'filter_active'   => 'Active sessions',

    // Tabs
    'tabs' => [
        'all'       => 'All',
        'active'    => 'Active',
        'pending'   => 'Pending',
        'verified'  => 'Verified',
        'completed' => 'Completed',
        'expired'   => 'Expired',
        'cancelled' => 'Cancelled',
    ],

    // Hard delete actions
    'hard_delete'              => 'Permanently Delete',
    'hard_delete_bulk'         => 'Permanently Delete Selected',
    'hard_delete_heading'      => 'Permanently Delete Session',
    'hard_delete_description'  => 'This will permanently remove the session record. This action cannot be undone.',
    'hard_delete_bulk_heading' => 'Permanently Delete Sessions',
    'hard_delete_bulk_description' => 'This will permanently remove the selected session records. This action cannot be undone.',
    'hard_delete_confirm'      => 'Yes, Delete Permanently',
];
