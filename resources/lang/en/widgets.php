<?php

return [
    'hall-stats' => [
        'total_halls' => 'Total Halls',
        'total_halls_desc' => 'All halls in the system',
        'active_halls' => 'Active Halls',
        'active_halls_desc' => 'Halls currently active',
        'featured_halls' => 'Featured Halls',
        'featured_halls_desc' => 'Halls marked as featured',
        'pending_halls' => 'Pending Approval',
        'pending_halls_desc' => 'Halls awaiting approval',
        'average_price' => 'Average Price',
        'average_price_desc' => 'Average price per slot',
    ],

    'hall-stats-overview' => [
        'total_bookings' => 'Total Bookings',
        'percent_increase_month' => ':percent% increase this month',
        'percent_decrease_month' => ':percent% decrease this month',
        'total_revenue' => 'Total Revenue',
        'percent_increase_vs_last_month' => ':percent% increase vs last month',
        'percent_decrease_vs_last_month' => ':percent% decrease vs last month',
        'average_rating' => 'Average Rating',
        'based_on_reviews' => 'Based on :count reviews',
        'no_reviews_yet' => 'No reviews yet',
        'occupancy_rate' => 'Occupancy Rate',
        'slots_this_month' => ':booked of :total slots this month',
        'pending_bookings' => 'Pending Bookings',
        'upcoming_count' => ':count upcoming',
        'completed_bookings' => 'Completed Bookings',
        'this_month_count' => ':count this month',
    ],

    'hall-revenue-chart' => [
        'heading' => 'Revenue Analysis',
        'description' => 'Revenue breakdown by month',
        'filters' => [
            '3' => 'Last 3 Months',
            '6' => 'Last 6 Months',
            '12' => 'Last 12 Months',
        ],
        'datasets' => [
            'gross_revenue' => 'Gross Revenue (OMR)',
            'platform_commission' => 'Platform Commission (OMR)',
            'owner_payout' => 'Owner Payout (OMR)',
        ],
        'axes' => [
            'y_title' => 'Amount (OMR)',
            'x_title' => 'Month',
        ],
    ],

    'hall-recent-bookings' => [
        'heading' => 'Recent Bookings',
        'description' => 'Latest booking activity for this hall',
        'columns' => [
            'booking_number' => 'Booking #',
            'customer' => 'Customer',
            'time_slot' => 'Time Slot',
            'status' => 'Status',
            'amount' => 'Amount',
            'booked' => 'Booked',
        ],
        'filters' => [
            'status' => 'Status',
            'payment' => 'Payment',
            'upcoming' => 'Upcoming Only',
        ],
        'messages' => [
            'copy_success' => 'Booking number copied',
        ],
        'empty_state' => [
            'heading' => 'No Bookings Yet',
            'description' => 'This hall has no booking records.',
        ],
    ],

    'hall-booking-trend' => [
        'heading' => 'Booking Trends',
        'description' => 'Booking activity over time',
        'filters' => [
            '30' => 'Last 30 Days',
            '60' => 'Last 60 Days',
            '90' => 'Last 90 Days',
            '180' => 'Last 6 Months',
        ],
        'datasets' => [
            'confirmed_completed' => 'Confirmed/Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
        ],
        'axes' => [
            'y_title' => 'Number of Bookings',
            'x_title' => 'Date',
        ],
    ],

    'hall-booking-status' => [
        'heading' => 'Booking Distribution',
        'description' => 'Bookings by status',
        'filters' => [
            'all' => 'All Time',
            'month' => 'This Month',
            'quarter' => 'This Quarter',
            'year' => 'This Year',
        ],
    ],

    'owner-stats-overview' => [
        'my_halls' => 'My Halls',
        'active_halls_desc' => ':count active halls',
        'total_earnings' => 'Total Earnings',
        'all_time_earnings' => 'All-time earnings',
        'monthly_earnings' => 'Monthly Earnings',
        'this_month' => 'This month',
        'total_bookings' => 'Total Bookings',
        'upcoming_desc' => ':count upcoming',
        'pending_bookings' => 'Pending Bookings',
        'requires_action' => 'Requires action',
    ],
];
