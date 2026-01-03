<?php

declare(strict_types=1);

/**
 * English PDF Translations
 *
 * Contains all translation keys used in PDF templates for:
 * - Owner Earnings Report
 * - Booking Statement
 * - Financial Report
 *
 * @package Lang\En
 */
return [
    // Company Information
    'company' => [
        'name' => 'Majalis',
        'tagline' => 'Hall Booking Platform',
    ],

    // Footer
    'footer' => [
        'generated' => 'Generated',
        'system' => 'System',
        'page' => 'Page',
        'confidential' => 'Confidential - For Owner Use Only',
        'document_id' => 'Document ID',
    ],

    // Earnings Report
    'earnings_report' => [
        'title' => 'Earnings Report',
        'period' => 'Period',
        'owner_name' => 'Owner Name',
        'report_date' => 'Report Date',
        'email' => 'Email',
        'report_number' => 'Report Number',

        // Summary
        'total_earnings' => 'Net Earnings',
        'gross_revenue' => 'Gross Revenue',
        'commission' => 'Commission',
        'total_bookings' => 'Total Bookings',

        // Financial Breakdown
        'financial_breakdown' => 'Financial Breakdown',
        'hall_rental_income' => 'Hall Rental Income',
        'services_income' => 'Services Income',
        'gross_total' => 'Gross Total',
        'platform_commission' => 'Platform Commission',
        'net_earnings' => 'Net Earnings',

        // Details Table
        'earnings_details' => 'Earnings Details',
        'col_booking' => 'Booking #',
        'col_date' => 'Date',
        'col_hall' => 'Hall',
        'col_slot' => 'Slot',
        'col_hall_price' => 'Hall Price',
        'col_services' => 'Services',
        'col_commission' => 'Commission',
        'col_net' => 'Net',
        'no_bookings' => 'No bookings found in this period.',
        'totals' => 'Totals',

        // Hall Performance
        'hall_performance' => 'Hall Performance',
        'col_bookings_count' => 'Bookings',
        'col_hall_revenue' => 'Hall Revenue',
        'col_services_revenue' => 'Services Revenue',
        'col_total_revenue' => 'Total Revenue',
        'col_net_earnings' => 'Net Earnings',

        // Notes
        'notes' => 'Notes',
    ],

    // Booking Statement
    'booking_statement' => [
        'title' => 'Booking Statement',

        // Booking Info
        'booking_info' => 'Booking Information',
        'booking_number' => 'Booking Number',
        'booking_date' => 'Booking Date',
        'time_slot' => 'Time Slot',
        'booking_status' => 'Booking Status',
        'payment_status' => 'Payment Status',

        // Customer Info
        'customer_info' => 'Customer Information',
        'customer_name' => 'Customer Name',
        'customer_phone' => 'Phone Number',
        'customer_email' => 'Email',
        'guests_count' => 'Guests Count',
        'guests' => 'guests',
        'event_type' => 'Event Type',

        // Hall Details
        'hall_details' => 'Hall Details',
        'location' => 'Location',
        'capacity' => 'Capacity',
        'persons' => 'persons',
        'hall_type' => 'Hall Type',

        // Extra Services
        'extra_services' => 'Extra Services',
        'service_name' => 'Service Name',
        'quantity' => 'Qty',
        'unit_price' => 'Unit Price',
        'total' => 'Total',

        // Financial Summary
        'financial_summary' => 'Financial Summary',
        'hall_rental' => 'Hall Rental',
        'services_total' => 'Services Total',
        'gross_total' => 'Gross Total',
        'platform_commission' => 'Platform Commission',
        'your_earnings' => 'Your Earnings',

        // Payment Details
        'payment_details' => 'Payment Details',
        'payment_method' => 'Payment Method',
        'paid_amount' => 'Paid Amount',
        'payment_date' => 'Payment Date',

        // Notes
        'customer_notes' => 'Customer Notes',

        // Timestamps
        'created_at' => 'Booking Created',
        'updated_at' => 'Last Updated',
        'statement_generated' => 'Statement Generated',
    ],

    // Financial Report
    'financial_report' => [
        'title' => 'Financial Report',
        'generated' => 'Generated',
        'report_id' => 'Report ID',

        // Report Types
        'types' => [
            'monthly' => 'Monthly Report',
            'yearly' => 'Yearly Report',
            'hall' => 'Hall Performance Report',
            'comparison' => 'Comparison Report',
        ],

        // Period Labels
        'period_monthly' => 'Monthly Report for :month :year',
        'period_yearly' => 'Annual Report for :year',
        'period_comparison' => 'Comparison: :current vs :previous :year',
        'period_custom' => 'Custom Period: :start - :end',

        // Owner Info
        'owner' => 'Owner',
        'total_halls' => 'Total Halls',
        'active_halls' => 'Active Halls',
        'member_since' => 'Member Since',

        // Summary Cards
        'net_earnings' => 'Net Earnings',
        'gross_revenue' => 'Gross Revenue',
        'commission' => 'Commission',
        'total_bookings' => 'Total Bookings',
        'avg_per_booking' => 'Avg/Booking',
        'occupancy_rate' => 'Occupancy Rate',

        // Section Headers
        'daily_breakdown' => 'Daily Breakdown',
        'monthly_breakdown' => 'Monthly Breakdown',
        'financial_breakdown' => 'Financial Breakdown',
        'slot_breakdown' => 'Time Slot Analysis',
        'hall_breakdown' => 'Hall Breakdown',
        'hall_performance' => 'Hall Performance',
        'hall_comparison' => 'Hall Comparison',
        'month_comparison' => 'Month-over-Month Comparison',
        'year_stats' => 'Year Statistics',
        'payout_summary' => 'Payout Summary',

        // Table Headers
        'date' => 'Date',
        'month' => 'Month',
        'bookings' => 'Bookings',
        'gross' => 'Gross',
        'net' => 'Net',
        'hall_rev' => 'Hall Rev.',
        'services_rev' => 'Services Rev.',
        'hall' => 'Hall',
        'slot' => 'Slot',
        'count' => 'Count',
        'revenue' => 'Revenue',
        'total' => 'Total',
        'share' => 'Share',
        'contribution' => 'Contribution',
        'metric' => 'Metric',
        'change' => 'Change',

        // Breakdown Labels
        'hall_revenue' => 'Hall Revenue',
        'services_revenue' => 'Services Revenue',
        'gross_total' => 'Gross Total',
        'platform_commission' => 'Platform Commission',
        'net_total' => 'Net Total',

        // Yearly Stats
        'year_total' => 'Year Total',
        'best_month' => 'Best Performing Month',
        'best_month_earnings' => 'Best Month Earnings',
        'avg_monthly' => 'Average Monthly',
        'total_year_earnings' => 'Total Year Earnings',

        // Payout
        'total_received' => 'Total Received',
        'pending_payout' => 'Pending Payout',
        'payout_count' => 'Payout Count',

        // Analysis
        'analysis' => 'Analysis',
        'analysis_positive' => 'Your earnings have increased by :change% compared to the previous month. Keep up the great performance!',
        'analysis_negative' => 'Your earnings have decreased by :change% compared to the previous month. Consider reviewing your pricing or marketing strategy.',
        'analysis_neutral' => 'Your earnings have remained stable compared to the previous month.',
    ],
];
