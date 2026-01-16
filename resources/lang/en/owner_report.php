<?php

declare(strict_types=1);

/**
 * English Owner Panel Translations
 *
 * Contains all translation keys used in the Owner Panel for:
 * - Earnings Resource
 * - Payouts Resource
 * - Financial Reports Page
 *
 * @package Lang\En
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'subheading' => 'Your performance overview for :date',
        'refresh' => 'Refresh',
        'export' => 'Export',
        'view_reports' => 'View Reports',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'select_hall' => 'Select Hall',
        'all_halls' => 'All Halls',
        'export_format' => 'Export Format',
        'report_type' => 'Report Type',
        'export_confirm' => 'Export Dashboard Data',
        'export_description' => 'Select date range and format to export your dashboard data.',
        'export_error' => 'Export failed',
        'good_morning' => 'Good morning',
        'good_afternoon' => 'Good afternoon',
        'good_evening' => 'Good evening',
    ],

    // Earnings Resource
    'earnings' => [
        'label' => 'Earning',
        'plural' => 'Earnings',
        'navigation_group' => 'Financial',

        // Table Columns
        'booking_number' => 'Booking #',
        'hall' => 'Hall',
        'date' => 'Date',
        'slot' => 'Slot',
        'customer' => 'Customer',
        'hall_price' => 'Hall Price',
        'services_price' => 'Services',
        'total_amount' => 'Total',
        'commission_amount' => 'Commission',
        'owner_payout' => 'Your Earnings',
        'status' => 'Status',

        // Filters
        'filter_date_range' => 'Date Range',
        'filter_from' => 'From',
        'filter_until' => 'Until',
        'filter_hall' => 'Hall',
        'filter_status' => 'Status',
        'filter_slot' => 'Time Slot',
        'filter_this_month' => 'This Month',
        'filter_last_month' => 'Last Month',

        // Tabs
        'tab_all' => 'All Earnings',
        'tab_this_month' => 'This Month',
        'tab_last_month' => 'Last Month',
        'tab_this_year' => 'This Year',

        // Page Titles
        'title' => 'My Earnings',
        'heading' => 'Earnings Overview',
        'list_title' => 'My Earnings',
        'view_title' => 'Earning Details',
        'subheading' => 'Total: :total OMR | This Month: :month OMR',

        // Actions
        'view_details' => 'View Details',
        'generate_report' => 'Generate Report',
        'export_excel' => 'Export to Excel',
        'download_invoice' => 'Download Invoice',
        'generate_statement' => 'Generate Statement',
        'back_to_list' => 'Back to List',

        // Report Modal
        'report_period' => 'Report Period',
        'report_title' => 'Generate Earnings Report',
        'report_start_date' => 'Start Date',
        'report_end_date' => 'End Date',
        'report_hall' => 'Filter by Hall',
        'report_all_halls' => 'All Halls',
        'report_include_details' => 'Include Booking Details',
        'report_include_breakdown' => 'Include Financial Breakdown',
        'report_generating' => 'Generating report...',
        'report_generated' => 'Report Generated Successfully',
        'report_generated_desc' => 'Report includes :bookings bookings with :earnings OMR in net earnings.',
        'report_failed' => 'Report Generation Failed',
        'include_in_report' => 'Include in Report',
        'summary' => 'Summary',
        'booking_details' => 'Booking Details',
        'hall_breakdown' => 'Hall Breakdown',
        'chart' => 'Chart',

        // Export Settings
        'export_settings' => 'Export Settings',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'select_hall' => 'Select Hall',
        'all_halls' => 'All Halls',
        'include_columns' => 'Include Columns',

        // Export Column Labels
        'columns' => [
            'booking_number' => 'Booking Number',
            'hall' => 'Hall Name',
            'customer' => 'Customer Name',
            'date' => 'Booking Date',
            'slot' => 'Time Slot',
            'hall_price' => 'Hall Price',
            'services_price' => 'Services Price',
            'gross_amount' => 'Gross Amount',
            'commission' => 'Commission',
            'net_earnings' => 'Net Earnings',
        ],

        // Export Messages
        'export_success' => 'Export Successful',
        'export_success_desc' => 'Exported :count bookings with :total OMR in net earnings.',
        'export_failed' => 'Export Failed',
        'no_data' => 'No Data to Export',
        'no_data_desc' => 'No earnings found for the selected period.',
        'totals' => 'Totals',
        'bookings' => 'bookings',

        // Infolist Sections
        'section_booking_info' => 'Booking Information',
        'section_financial' => 'Financial Breakdown',
        'section_services' => 'Extra Services',
        'section_payment' => 'Payment Details',

        // Widget
        'widget_title' => 'Earnings Summary',
        'stat_total_earnings' => 'Total Earnings',
        'stat_this_month' => 'This Month',
        'stat_this_week' => 'This Week',
        'stat_avg_booking' => 'Avg/Booking',
        'stat_gross_revenue' => 'Gross Revenue',
        'stat_total_commission' => 'Total Commission',
        'stat_mom_change' => ':change% from last month',

        // Messages
        'no_earnings' => 'No earnings found.',
        'empty_state_heading' => 'No Earnings Yet',
        'empty_state_description' => 'Your earnings from completed bookings will appear here.',
    ],

    // Payouts Resource
    'payouts' => [
        'label' => 'Payout',
        'plural' => 'Payouts',
        'navigation_group' => 'Financial',

        // Table Columns
        'payout_number' => 'Payout #',
        'period' => 'Period',
        'bookings_count' => 'Bookings',
        'gross_revenue' => 'Gross Revenue',
        'commission_amount' => 'Commission',
        'commission_rate' => 'Rate',
        'net_payout' => 'Net Payout',
        'status' => 'Status',
        'payment_method' => 'Method',
        'completed_at' => 'Completed',
        'transaction_reference' => 'Reference',

        // Filters
        'filter_status' => 'Status',
        'filter_period' => 'Period',
        'filter_from' => 'From',
        'filter_until' => 'Until',
        'filter_completed' => 'Completed Only',
        'filter_this_year' => 'This Year',

        // Tabs
        'tab_all' => 'All Payouts',
        'tab_pending' => 'Pending',
        'tab_completed' => 'Completed',
        'tab_this_year' => 'This Year',

        // Page Titles
        'list_title' => 'My Payouts',
        'view_title' => 'Payout Details',
        'subheading' => 'Total Received: :received OMR | Pending: :pending OMR',

        // Actions
        'download_receipt' => 'Download Receipt',
        'contact_support' => 'Contact Support',
        'report_issue' => 'Report Issue',
        'back_to_list' => 'Back to List',

        // Infolist Sections
        'section_summary' => 'Payout Summary',
        'section_financial' => 'Financial Breakdown',
        'section_payment' => 'Payment Details',
        'section_failure' => 'Failure Details',
        'section_notes' => 'Notes',
        'section_timestamps' => 'Timestamps',

        // Infolist Fields
        'period_start' => 'Period Start',
        'period_end' => 'Period End',
        'adjustments' => 'Adjustments',
        'bank_details' => 'Bank Details',
        'processed_by' => 'Processed By',
        'processed_at' => 'Processed At',
        'failed_at' => 'Failed At',
        'failure_reason' => 'Failure Reason',
        'notes' => 'Notes',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',

        // Widget
        'widget_title' => 'Payout Summary',
        'stat_total_received' => 'Total Received',
        'stat_pending' => 'Pending',
        'stat_processing' => 'Processing',
        'stat_avg_payout' => 'Avg Payout',
        'stat_this_year' => 'This Year',
        'stat_last_payout' => 'Last Payout',

        // Messages
        'no_payouts' => 'No payouts found.',
        'empty_state_heading' => 'No Payouts Yet',
        'empty_state_description' => 'Your payouts will appear here once processed by admin.',
        'support_ticket_created' => 'Support ticket created successfully.',
    ],

    // Financial Reports Page
    'reports' => [
        'title' => 'Financial Reports',
        'navigation_group' => 'Financial',

        // Report Types
        'type_monthly' => 'Monthly Report',
        'type_yearly' => 'Yearly Report',
        'type_hall' => 'Hall Performance',
        'type_comparison' => 'Month Comparison',

        // Form Fields
        'report_type' => 'Report Type',
        'select_year' => 'Select Year',
        'select_month' => 'Select Month',
        'select_hall' => 'Select Hall',
        'all_halls' => 'All Halls',

        // Actions
        'export_pdf' => 'Export PDF',
        'refresh' => 'Refresh',

        // Section Titles
        'summary' => 'Summary',
        'daily_breakdown' => 'Daily Breakdown',
        'monthly_breakdown' => 'Monthly Breakdown',
        'hall_breakdown' => 'Hall Breakdown',
        'slot_breakdown' => 'Time Slot Analysis',
        'comparison' => 'Comparison',

        // Stats
        'total_bookings' => 'Total Bookings',
        'gross_revenue' => 'Gross Revenue',
        'hall_revenue' => 'Hall Revenue',
        'services_revenue' => 'Services Revenue',
        'total_commission' => 'Total Commission',
        'net_earnings' => 'Net Earnings',
        'avg_per_booking' => 'Average Per Booking',
        'best_month' => 'Best Month',
        'avg_monthly' => 'Monthly Average',
        'year_total' => 'Year Total',

        // Comparison
        'current_month' => 'Current Month',
        'previous_month' => 'Previous Month',
        'change' => 'Change',
        'increase' => 'Increase',
        'decrease' => 'Decrease',
        'no_change' => 'No Change',

        // Messages
        'report_generated' => 'Report generated successfully!',
        'export_success' => 'PDF exported successfully.',
        'no_data' => 'No data available for the selected period.',
    ],

    // Common
    'months' => [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ],

    'slots' => [
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
    ],

    'payment' => [
        'paid' => 'Paid',
        'pending' => 'Pending',
        'partial' => 'Partial',
        'refunded' => 'Refunded',
    ],

    'actions' => [
        'view' => 'View',
        'export' => 'Export',
        'download' => 'Download',
        'generate' => 'Generate',
        'refresh' => 'Refresh',
        'back' => 'Back',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics
    |--------------------------------------------------------------------------
    */
    'reports' => [
        // Page titles
        'title' => 'Reports & Analytics',
        'heading' => 'My Reports',
        'subheading' => 'Track your earnings, bookings, and hall performance',
        'nav_label' => 'Reports',

        // Tabs
        'tabs' => [
            'overview' => 'Overview',
            'earnings' => 'Earnings',
            'bookings' => 'Bookings',
            'halls' => 'Halls',
        ],

        // Filters
        'filters' => [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'hall' => 'Hall',
            'all_halls' => 'All Halls',
            'preset' => 'Quick Select',
            'custom' => 'Custom Range',
        ],

        // Presets
        'presets' => [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
        ],

        // Actions
        'actions' => [
            'refresh' => 'Refresh',
            'export_csv' => 'Export CSV',
            'export_pdf' => 'Export PDF',
            'print' => 'Print',
        ],

        // Export
        'export' => [
            'type' => 'Report Type',
            'summary' => 'Summary Report',
            'bookings' => 'Bookings Report',
            'revenue' => 'Revenue Report',
            'halls' => 'Halls Performance',
        ],

        // Stats
        'stats' => [
            'total_earnings' => 'Net Earnings',
            'total_revenue' => 'Total Revenue',
            'total_bookings' => 'Total Bookings',
            'pending_payout' => 'Pending Payout',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            'total_guests' => 'Total Guests',
            'avg_booking' => 'Avg. Booking',
            'total_halls' => 'Total Halls',
            'active_halls' => 'Active Halls',
            'avg_guests_per_booking' => 'Avg. Guests/Booking',
            'avg_booking_value' => 'Avg. Booking Value',
            'avg_bookings_per_hall' => 'Avg. Bookings/Hall',
        ],

        // Status
        'status' => [
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
        ],

        // Charts
        'charts' => [
            'earnings_trend' => 'Earnings Trend',
            'booking_status' => 'Booking Status',
            'time_slots' => 'Time Slot Distribution',
            'revenue' => 'Revenue',
            'earnings' => 'Net Earnings',
            'bookings' => 'Bookings',
        ],

        // Sections
        'sections' => [
            'earnings_summary' => 'Earnings Summary',
            'booking_summary' => 'Booking Summary',
            'hall_performance' => 'Hall Performance',
            'monthly_comparison' => 'Monthly Comparison',
            'guest_stats' => 'Guest Statistics',
            'guest_summary' => 'Guest Summary',
        ],

        // Fields
        'fields' => [
            'total_revenue' => 'Total Revenue',
            'platform_fee' => 'Platform Fee',
            'net_earnings' => 'Net Earnings',
            'paid_out' => 'Paid Out',
            'pending_payout' => 'Pending Payout',
        ],

        // Table headers
        'table' => [
            'hall' => 'Hall',
            'bookings' => 'Bookings',
            'revenue' => 'Revenue',
            'avg_booking' => 'Avg. Booking',
            'total' => 'Total',
            'earnings' => 'Earnings',
        ],

        // Comparison
        'comparison' => [
            'earnings_change' => 'Earnings Change',
            'bookings_change' => 'Bookings Change',
            'vs_last_month' => 'vs. last month',
        ],

        // Notifications
        'notifications' => [
            'no_data' => 'No data available for export',
        ],

        // No data
        'no_data' => 'No data available for the selected period',

        // PDF
        'pdf' => [
            'title' => 'Earnings Report',
            'earnings_report' => 'Earnings Report',
            'subtitle' => 'Detailed earnings and performance report',
            'period' => 'Period',
            'generated' => 'Generated',
            'owner_details' => 'Owner Details',
            'owner_name' => 'Name',
            'email' => 'Email',
            'business' => 'Business',
            'phone' => 'Phone',
            'net_earnings' => 'Net Earnings',
            'financial_overview' => 'Financial Overview',
            'gross_revenue' => 'Gross Revenue',
            'platform_fee' => 'Platform Fee',
            'pending_payout' => 'Pending Payout',
            'monthly_comparison' => 'Monthly Comparison',
            'earnings_change' => 'Earnings Change',
            'bookings_change' => 'Bookings Change',
            'booking_stats' => 'Booking Statistics',
            'total_bookings' => 'Total Bookings',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'additional_stats' => 'Additional Statistics',
            'total_guests' => 'Total Guests',
            'avg_booking_value' => 'Avg. Booking Value',
            'total_paid_out' => 'Total Paid Out',
            'hall_performance' => 'Hall Performance',
            'hall_name' => 'Hall Name',
            'bookings' => 'Bookings',
            'revenue' => 'Revenue',
            'avg_booking' => 'Avg. Booking',
            'total' => 'Total',
            'hall_summary' => 'Hall Summary',
            'total_halls' => 'Total Halls',
            'active_halls' => 'Active Halls',
            'avg_bookings_per_hall' => 'Avg. Bookings/Hall',
            'avg_earnings_per_hall' => 'Avg. Earnings/Hall',
            'footer' => 'This report was generated by :app',
            'thank_you' => 'Thank you for being a valued partner!',
        ],
    ],
];
