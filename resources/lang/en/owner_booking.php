<?php

return [
    'booking' => 'Booking',
    'bookings' => 'Bookings',

    'navigation' => [
        'group' => 'Bookings',
        'badge_tooltip' => 'Pending bookings requiring attention',
    ],

    'general' => [
        'na' => 'N/A',
    ],

    'time_slots' => [
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'full_day' => 'Full Day',
    ],

    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',

        'pending_info' => 'This booking is awaiting confirmation. Please review and approve or reject.',
        'confirmed_info' => 'This booking is confirmed. The customer has been notified.',
        'completed_info' => 'This event has been completed successfully.',
        'cancelled_info' => 'This booking was cancelled.',
        'cancelled_reason' => 'Reason:',
    ],

    'payment' => [
        'pending' => 'Pending',
        'partial' => 'Partial',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ],

    'payment_methods' => [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'card' => 'Card (POS)',
    ],

    'form' => [
        'sections' => [
            'booking_information' => 'Booking Information',
            'booking_information_description' => 'Basic booking details',
            'customer_information' => 'Customer Information',
            'customer_information_description' => 'Contact details for the customer',
            'payment_information' => 'Payment Information',
            'payment_information_description' => 'Financial details for this booking',
            'booking_status' => 'Booking Status',
        ],

        'fields' => [
            'booking_number' => 'Booking Number',
            'hall' => 'Hall',
            'event_date' => 'Event Date',
            'time_slot' => 'Time Slot',
            'event_type' => 'Event Type',
            'number_of_guests' => 'Number of Guests',
            'customer_name' => 'Customer Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'customer_notes' => 'Customer Notes',
            'hall_price' => 'Hall Price',
            'services_price' => 'Services Price',
            'total_amount' => 'Total Amount',
            'your_earnings' => 'Your Earnings',
            'your_earnings_help' => 'Amount after platform commission',
            'advance_paid' => 'Advance Paid',
            'balance_due' => 'Balance Due',
            'current_status' => 'Current Status',
        ],
    ],

    'table' => [
        'columns' => [
            'booking_number' => 'Booking #',
            'hall' => 'Hall',
            'customer' => 'Customer',
            'event_date' => 'Event Date',
            'time' => 'Time',
            'status' => 'Status',
            'your_earnings' => 'Your Earnings',
            'payment' => 'Payment',
            'balance' => 'Balance',
            'guests' => 'Guests',
            'booked_on' => 'Booked On',
        ],

        'copy_messages' => [
            'booking_number' => 'Booking number copied',
        ],

        'empty_state' => [
            'heading' => 'No bookings yet',
            'description' => 'When customers book your halls, they will appear here.',
        ],
    ],

    'filters' => [
        'hall' => 'Hall',
        'booking_status' => 'Booking Status',
        'payment_status' => 'Payment Status',
        'from_date' => 'From Date',
        'until_date' => 'Until Date',
        'from' => 'From',
        'until' => 'Until',
        'upcoming_events' => 'Upcoming Events',
        'all_bookings' => 'All bookings',
        'upcoming_only' => 'Upcoming only',
        'past_only' => 'Past only',
        'needs_action' => 'Needs Action',
    ],

    'actions' => [

    'view' => [
            'label' => 'View',
        ],

        'approve' => [
            'label' => 'Approve',
            'modal_heading' => 'Approve Booking',
            'modal_description' => 'Are you sure you want to approve this booking? The customer will be notified.',
            'modal_submit_label' => 'Yes, Approve',
        ],

        'reject' => [
            'label' => 'Reject',
            'modal_heading' => 'Reject Booking',
            'modal_description' => 'Are you sure you want to reject this booking? This action cannot be undone.',
            'reason_label' => 'Reason for Rejection',
            'reason_placeholder' => 'Please provide a reason for rejecting this booking...',
            'cancellation_reason_prefix' => 'Rejected by hall owner: ',
        ],

        'mark_balance' => [
            'label' => 'Record Balance',
            'modal_heading' => 'Record Balance Payment',
            'modal_description' => 'Record that the remaining balance has been received from the customer.',
            'balance_info' => 'Balance Due',
            'payment_method_label' => 'Payment Method',
            'reference_label' => 'Reference/Receipt Number',
            'reference_placeholder' => 'Optional reference number',
            'notes_label' => 'Notes',
            'notes_placeholder' => 'Any additional notes...',
            'admin_note' => 'Balance of OMR :amount received via :method on :date',
            'notes_prefix' => 'Notes: ',
        ],

        'contact' => [
            'label' => 'Contact',
            'call' => 'Call Customer',
            'email' => 'Email Customer',
            'whatsapp' => 'WhatsApp',
        ],

        'bulk' => [
            'export' => 'Export Selected',
        ],
    ],

    'notifications' => [
        'approve' => [
            'title' => 'Booking Approved',
            'body' => 'Booking :number has been approved successfully.',
        ],

        'reject' => [
            'title' => 'Booking Rejected',
            'body' => 'Booking :number has been rejected.',
        ],

        'mark_balance' => [
            'title' => 'Balance Payment Recorded',
            'body' => 'Balance payment for booking :number has been recorded.',
        ],

        'export' => [
            'title' => 'Export Started',
            'body' => 'Your export is being prepared...',
        ],
    ],

    'infolist' => [
        'copy_messages' => [
            'copied' => 'Copied!',
        ],

        'placeholders' => [
            'not_specified' => 'Not specified',
            'no_notes' => 'No notes provided',
            'not_yet_received' => 'Not yet received',
            'not_confirmed' => 'Not confirmed',
            'not_completed' => 'Not completed',
            'not_cancelled' => 'Not cancelled',
        ],

        'sections' => [
            'header' => [
                'booking_number' => 'Booking Number',
                'status' => 'Status',
                'payment' => 'Payment',
                'booked_on' => 'Booked On',
            ],

            'event_details' => [
                'event_details' => 'Event Details',
                'hall' => 'Hall',
                'event_date' => 'Event Date',
                'time_slot' => 'Time Slot',
                'event_type' => 'Event Type',
                'expected_guests' => 'Expected Guests',
                'guests_suffix' => 'guests',
            ],

            'customer_information' => [
                'customer_information' => 'Customer Information',
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'customer_notes' => 'Customer Notes',
            ],

            'financial_summary' => [
                'financial_summary' => 'Financial Summary',
                'hall_price' => 'Hall Price',
                'services' => 'Services',
                'total_amount' => 'Total Amount',
                'your_earnings' => 'Your Earnings',
                'advance_payment_details' => 'Advance Payment Details',
                'advance_paid' => 'Advance Paid',
                'balance_due' => 'Balance Due',
                'balance_received' => 'Balance Received',
                'payment_method' => 'Payment Method',
                'platform_fee' => 'Platform Fee',
                'commission_amount' => 'Commission Amount',
            ],

            'extra_services' => [
                'extra_services' => 'Extra Services',
                'service' => 'Service',
                'qty' => 'Qty',
                'unit_price' => 'Unit Price',
                'total' => 'Total',
            ],

            'booking_timeline' => [
                'booking_timeline' => 'Booking Timeline',
                'booked' => 'Booked',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'cancellation_reason' => 'Cancellation Reason',
            ],
        ],
    ],

    'stats' => [
        'pending_approval' => 'Pending Approval',
        'pending_bookings_description' => ':count bookings need your review',
        'all_reviewed' => 'All bookings reviewed',

        'upcoming_events' => 'Upcoming Events',
        'today_events_description' => ':count event(s) today',
        'upcoming_bookings_description' => 'Confirmed upcoming bookings',

        'this_month_earnings' => 'This Month Earnings',
        'revenue_increase' => ':percent% increase from last month',
        'revenue_decrease' => ':percent% decrease from last month',

        'balance_to_collect' => 'Balance to Collect',
        'balance_from_advance' => 'From advance payment bookings',
        'no_pending_balances' => 'No pending balances',


    ],

    'pages' => [
        'list' => [
            'export_label' => 'Export',
            'export_notification' => 'Export functionality coming soon',

            'tabs' => [
                'all' => 'All Bookings',
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'upcoming' => 'Upcoming',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ],
    ],
];
