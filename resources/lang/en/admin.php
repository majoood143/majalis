<?php

declare(strict_types=1);

/**
 * English Admin Payout Translations
 *
 * Contains all translation keys for the admin payout management interface.
 *
 * @package Lang\En
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Payout Management
    |--------------------------------------------------------------------------
    */
    'payout' => [
        // Page titles
        'title' => 'Payout Management',
        'create_title' => 'Create Payout',
        'edit_title' => 'Edit Payout',
        'view_title' => 'Payout Details',

        // Sections
        'sections' => [
            'main' => 'Payout Information',
            'main_desc' => 'Basic payout details and owner selection',
            'financial' => 'Financial Details',
            'financial_desc' => 'Revenue, commission, and payout amounts',
            'payment' => 'Payment Details',
            'payment_desc' => 'Payment method and transaction information',
            'notes' => 'Notes & Comments',
            'owner_info' => 'Owner Information',
            'timestamps' => 'Timeline',
        ],

        // Field labels
        'fields' => [
            'payout_number' => 'Payout Number',
            'owner' => 'Hall Owner',
            'owner_name' => 'Owner Name',
            'owner_email' => 'Owner Email',
            'business_name' => 'Business Name',
            'bank_name' => 'Bank Name',
            'period' => 'Period',
            'period_start' => 'Period Start',
            'period_end' => 'Period End',
            'status' => 'Status',
            'gross_revenue' => 'Gross Revenue',
            'gross' => 'Gross',
            'commission' => 'Commission',
            'commission_rate' => 'Commission Rate',
            'adjustments' => 'Adjustments',
            'net_payout' => 'Net Payout',
            'net' => 'Net',
            'bookings_count' => 'Bookings',
            'bookings' => 'Bookings',
            'payment_method' => 'Payment Method',
            'transaction_reference' => 'Transaction Reference',
            'bank_details' => 'Bank Details',
            'notes' => 'Notes',
            'failure_reason' => 'Failure Reason',
            'hold_reason' => 'Hold Reason',
            'cancel_reason' => 'Cancellation Reason',
            'processed_at' => 'Processed At',
            'completed_at' => 'Completed At',
            'failed_at' => 'Failed At',
            'processed_by' => 'Processed By',
            'created_at' => 'Created',
        ],

        // Payment methods
        'methods' => [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'other' => 'Other',
        ],

        // Bank details
        'bank' => [
            'field' => 'Field',
            'value' => 'Value',
            'add' => 'Add Bank Detail',
        ],

        // Actions
        'actions' => [
            'create' => 'New Payout',
            'edit' => 'Edit',
            'view' => 'View',
            'delete' => 'Delete',
            'process' => 'Start Processing',
            'complete' => 'Mark Complete',
            'fail' => 'Mark Failed',
            'hold' => 'Put on Hold',
            'cancel' => 'Cancel Payout',
            'generate' => 'Generate Payouts',
            'calculate' => 'Calculate from Bookings',
            'export' => 'Export',
            'print' => 'Print Receipt',
        ],

        // Bulk actions
        'bulk' => [
            'process' => 'Process Selected',
            'cancel' => 'Cancel Selected',
        ],

        // Filters
        'filters' => [
            'status' => 'Status',
            'owner' => 'Owner',
            'period' => 'Period',
            'from' => 'From Date',
            'to' => 'To Date',
            'pending_only' => 'Pending Only',
        ],

        // Tabs
        'tabs' => [
            'all' => 'All',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'on_hold' => 'On Hold',
            'failed' => 'Failed',
        ],

        // Modal titles & descriptions
        'modal' => [
            'process_title' => 'Start Processing Payout',
            'process_desc' => 'This will mark the payout as processing. Continue?',
            'process_desc_amount' => 'Process payout of :amount OMR to :owner?',
            'process_confirm' => 'Start Processing',
            'complete_title' => 'Complete Payout',
            'complete_desc' => 'Confirm that :amount OMR has been paid to the owner.',
            'fail_title' => 'Mark Payout as Failed',
            'hold_title' => 'Put Payout on Hold',
            'cancel_title' => 'Cancel Payout',
            'cancel_desc' => 'This action cannot be undone. Are you sure?',
            'generate_title' => 'Generate Payouts',
            'generate_desc' => 'Generate payout records for owners based on completed bookings.',
            'generate_confirm' => 'Generate Payouts',
            'regenerate_receipt_title' => 'Regenerate Receipt?',
            'regenerate_receipt_desc' => 'This will delete the existing receipt and generate a new one. Continue?',
        ],

        // Notifications
        'notifications' => [
            'created' => 'Payout Created',
            'created_body' => 'Payout :number created for :owner (:amount OMR)',
            'updated' => 'Payout Updated',
            'processing' => 'Payout Processing Started',
            'processing_body' => 'Payout :number is now being processed.',
            'process_failed' => 'Failed to Start Processing',
            'completed' => 'Payout Completed',
            'completed_body' => ':amount OMR paid to :owner',
            'failed' => 'Payout Marked as Failed',
            'failed_body' => 'The payout has been marked as failed.',
            'on_hold' => 'Payout On Hold',
            'on_hold_body' => 'The payout has been put on hold.',
            'cancelled' => 'Payout Cancelled',
            'cancelled_body' => 'The payout has been cancelled.',
            'generated' => ':count Payout(s) Generated',
            'no_payouts' => 'No Payouts Generated',
            'no_payouts_body' => 'No eligible bookings found for the selected period.',
            'bulk_processed' => ':count Payout(s) Moved to Processing',
            'bulk_cancelled' => ':count Payout(s) Cancelled',
            'missing_data' => 'Missing Information',
            'missing_data_body' => 'Please select an owner and period first.',
            'calculated' => 'Values Calculated',
            'calculated_body' => 'Found :count eligible booking(s).',
            'no_bookings' => 'No Bookings Found',
            'no_bookings_body' => 'No paid bookings found for this owner in the selected period.',
            'export_started' => 'Export Started',
            'receipt_generated' => 'Receipt Generated',
            'receipt_generated_body' => 'The payout receipt PDF has been created and saved.',
            'receipt_failed' => 'Receipt Generation Warning',
            'receipt_failed_body' => 'Payout completed but receipt generation failed. You can regenerate it later.',
            'receipt_regenerated' => 'Receipt Regenerated',
            'receipt_regenerated_body' => 'The payout receipt has been regenerated successfully.',
            'receipt_not_found' => 'Receipt Not Found',
            'receipt_not_found_body' => 'The receipt file could not be found. Try regenerating it.',
        ],

        // Stats widget
        'stats' => [
            'heading' => 'Payout Overview',
            'description' => 'Summary of owner payouts',
            'pending' => 'Pending Payouts',
            'pending_count' => ':count awaiting processing',
            'processing' => 'In Processing',
            'processing_count' => ':count being processed',
            'completed_month' => 'Completed This Month',
            'total_paid' => 'Total Paid Out',
            'all_time' => 'All time',
            'on_hold' => 'On Hold',
            'requires_attention' => 'Requires attention',
            'failed' => 'Failed',
            'needs_review' => 'Needs review',
            'increase' => ':percent% increase',
            'decrease' => ':percent% decrease',
        ],

        // Export
        'export' => [
            'format' => 'Export Format',
        ],

        // Placeholders & helpers
        'auto_generated' => 'Auto-generated',
        'auto_generated_help' => 'Payout number will be generated automatically',
        'transaction_placeholder' => 'e.g., TXN-2025-00001',
        'adjustments_help' => 'Positive for additions, negative for deductions',
        'failure_placeholder' => 'Describe the reason for failure...',
        'hold_placeholder' => 'Reason for putting on hold (optional)',
        'no_notes' => 'No notes added',
        'all_owners' => 'All Owners',
        'all_statuses' => 'All Statuses',
        'generate_owner_help' => 'Leave empty to generate for all owners',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payout Status Enum Labels
    |--------------------------------------------------------------------------
    */
    'enums' => [
        'payout_status' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
        ],
    ],
    'reports' => [
        // Page titles
        'title' => 'Reports & Analytics',
        'heading' => 'Platform Reports',
        'subheading' => 'Comprehensive analytics and insights for platform performance',

        // Tabs
        'tabs' => [
            'overview' => 'Overview',
            'revenue' => 'Revenue',
            'bookings' => 'Bookings',
            'performance' => 'Performance',
            'commission' => 'Commission',
        ],

        // Filters
        'filters' => [
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
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
            'download_receipt' => 'Download Receipt',
            'regenerate_receipt' => 'Regenerate Receipt',
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
            'total_revenue' => 'Total Revenue',
            'platform_commission' => 'Platform Commission',
            'total_bookings' => 'Total Bookings',
            'pending_payouts' => 'Pending Payouts',
            'payouts' => 'payouts',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            'active_halls' => 'Active Halls',
            'verified_owners' => 'Verified Owners',
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
            'revenue_trend' => 'Revenue Trend',
            'booking_status' => 'Booking Status Distribution',
            'time_slots' => 'Time Slot Distribution',
            'revenue' => 'Revenue',
            'commission' => 'Commission',
            'bookings' => 'Bookings',
        ],

        // Sections
        'sections' => [
            'revenue_summary' => 'Revenue Summary',
            'booking_summary' => 'Booking Summary',
            'top_halls' => 'Top Performing Halls',
            'top_owners' => 'Top Performing Owners',
            'commission_summary' => 'Commission Summary',
            'by_type' => 'By Commission Type',
        ],

        // Fields
        'fields' => [
            'gross_revenue' => 'Gross Revenue',
            'platform_commission' => 'Platform Commission',
            'owner_payouts' => 'Owner Payouts',
            'refunds' => 'Refunds',
            'total_commission' => 'Total Commission',
            'total_revenue' => 'Total Revenue',
            'avg_rate' => 'Average Rate',
        ],

        // Table headers
        'table' => [
            'hall' => 'Hall',
            'owner' => 'Owner',
            'bookings' => 'Bookings',
            'revenue' => 'Revenue',
            'halls' => 'Halls',
        ],

        // Notifications
        'notifications' => [
            'no_data' => 'No data available for export',
        ],

        // No data
        'no_data' => 'No data available for the selected period',

        // PDF
        'pdf' => [
            'title' => 'Platform Report',
            'platform_report' => 'Platform Analytics Report',
            'period' => 'Period',
            'generated' => 'Generated',
            'by' => 'By',
            'overview' => 'Overview',
            'total_revenue' => 'Total Revenue',
            'platform_commission' => 'Platform Commission',
            'owner_payouts' => 'Owner Payouts',
            'pending_payouts' => 'Pending Payouts',
            'booking_stats' => 'Booking Statistics',
            'total_bookings' => 'Total Bookings',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'commission_summary' => 'Commission Summary',
            'gross_revenue' => 'Gross Revenue',
            'total_commission' => 'Total Commission',
            'avg_commission_rate' => 'Average Commission Rate',
            'bookings_processed' => 'Bookings Processed',
            'top_halls' => 'Top Performing Halls',
            'hall_name' => 'Hall Name',
            'bookings' => 'Bookings',
            'revenue' => 'Revenue',
            'avg_booking' => 'Avg. Booking',
            'top_owners' => 'Top Performing Owners',
            'owner_name' => 'Owner Name',
            'business' => 'Business',
            'halls' => 'Halls',
            'commission' => 'Commission',
            'platform_stats' => 'Platform Statistics',
            'active_halls' => 'Active Halls',
            'verified_owners' => 'Verified Owners',
            'total_customers' => 'Total Customers',
            'pending_payout_count' => 'Pending Payouts',
            'footer' => 'This report was generated by :app',
            'confidential' => 'Confidential - For Internal Use Only',
        ],
    ],
];
