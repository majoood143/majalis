<?php

return [

    'title' => 'Payments',
    'title_singular' => 'Payment',
    'breadcrumb' => 'Payments',
    'singleton' => 'Payment',
    'plural' => 'Payments',
    'navigation_label' => 'Payments',
    // Sections
    'sections' => [
        'payment_information' => 'Payment Information',
        'refund_information' => 'Refund Information',
        'failure_information' => 'Failure Information',
        'gateway_response' => 'Gateway Response',
        'timestamps' => 'Timestamps',
        'refund_details' => 'Refund Details',
        'export_options' => 'Export Options',
        'report_period' => 'Report Period',
        'email_details' => 'Email Details',
    ],

    // Fields
    'fields' => [
        'payment_reference' => 'Payment Reference',
        'booking' => 'Booking',
        'transaction_id' => 'Transaction ID',
        'amount' => 'Amount',
        'currency' => 'Currency',
        'status' => 'Status',
        'payment_method' => 'Payment Method',
        'refund_amount' => 'Refund Amount',
        'refund_reason' => 'Refund Reason',
        'failure_reason' => 'Failure Reason',
        'gateway_response' => 'Gateway Response',
        'paid_at' => 'Paid At',
        'failed_at' => 'Failed At',
        'refunded_at' => 'Refunded At',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'refund_type' => 'Refund Type',
        'refund_amount_input' => 'Refund Amount',
        'refund_reason_select' => 'Refund Reason',
        'additional_notes' => 'Additional Notes',
        'notify_customer' => 'Notify Customer',
        'status_filter' => 'Filter by Status',
        'format' => 'Format',
        'include_booking_details' => 'Include Booking Details',
        'metrics' => 'Include Metrics',
        'reconcile_date' => 'Reconcile For Date',
        'auto_update' => 'Auto-update Mismatches',
        'age' => 'Failed Within',
        'retry_reason' => 'Retry Reason (Optional)',
        'pending_for' => 'Pending For',
        'customer_email' => 'Customer Email',
        'send_admin_copy' => 'Send copy to admin',
        'custom_message' => 'Custom Message (Optional)',
        'payment_date' => 'Payment Date',
    ],

    // Status
    'status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially Refunded',
        'refund_in_progress' => 'Refund In Progress',
        'retrying' => 'Retrying',
        'reconciliation_pending' => 'Reconciliation Pending',
        'processing' => 'Processing',
        'canceled' => 'Canceled',
    ],

    // Actions
    'actions' => [
        'create' => 'Create Payment',
        'export' => 'Export Payments',
        'financial_report' => 'Financial Report',
        'reconcile' => 'Reconcile Payments',
        'retry_failed' => 'Retry Failed',
        'send_reminders' => 'Send Reminders',
        'refund' => 'Process Refund',
        'process_refund' => 'Process Refund',
        'download' => 'Download File',
        'view' => 'View',
        'edit' => 'Edit',
        'receipt' => 'Receipt',
        'download_receipt' => 'Download Receipt',
        'print_receipt' => 'Print Receipt',
        'email_receipt' => 'Email Receipt',
        'send_email' => 'Send Email',
    ],

    // Options
    'options' => [
        'all_statuses' => 'All Statuses',
        'paid_only' => 'Paid Only',
        'pending_only' => 'Pending Only',
        'failed_only' => 'Failed Only',
        'refunded_only' => 'Refunded Only',
        'partially_refunded' => 'Partially Refunded',
        'csv_format' => 'CSV (Excel compatible)',
        'json_format' => 'JSON',
        'full_refund' => 'Full Refund (:amount OMR)',
        'partial_refund' => 'Partial Refund (Specify Amount)',
        'last_24_hours' => 'Last 24 hours',
        'last_3_days' => 'Last 3 days',
        'last_7_days' => 'Last 7 days',
        'last_30_days' => 'Last 30 days',
        'more_than_1_day' => 'More than 1 day',
        'more_than_3_days' => 'More than 3 days',
        'more_than_7_days' => 'More than 7 days',
    ],

    // Refund Reasons
    'refund_reasons' => [
        'customer_request' => 'Customer Request',
        'event_cancelled' => 'Event Cancelled',
        'hall_unavailable' => 'Hall Unavailable',
        'duplicate_payment' => 'Duplicate Payment',
        'service_not_provided' => 'Service Not Provided',
        'quality_issues' => 'Quality Issues',
        'other' => 'Other',
    ],

    // Metrics
    'metrics' => [
        'revenue' => 'Total Revenue',
        'refunds' => 'Refunds Summary',
        'failed' => 'Failed Payments Analysis',
        'payment_methods' => 'Payment Methods Breakdown',
        'daily_trends' => 'Daily Transaction Trends',
    ],

    // Tabs
    'tabs' => [
        'all' => 'All Payments',
        'paid' => 'Paid',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'today' => 'Today',
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'high_value' => 'High Value (1000+ OMR)',
    ],

    // Columns
    'columns' => [
        'payment_reference' => 'Payment Reference',
        'booking_number' => 'Booking Number',
        'transaction_id' => 'Transaction ID',
        'amount' => 'Amount',
        'status' => 'Status',
        'payment_method' => 'Payment Method',
        'paid_at' => 'Paid At',
        'created_at' => 'Created At',
    ],

    // Filters
    'filters' => [
        'status' => 'Status',
        'paid_at' => 'Paid At',
    ],

    // Export Headers
    'export' => [
        'payment_reference' => 'Payment Reference',
        'booking_number' => 'Booking Number',
        'transaction_id' => 'Transaction ID',
        'amount' => 'Amount (OMR)',
        'currency' => 'Currency',
        'status' => 'Status',
        'payment_method' => 'Payment Method',
        'refund_amount' => 'Refund Amount (OMR)',
        'paid_at' => 'Paid At',
        'failed_at' => 'Failed At',
        'refunded_at' => 'Refunded At',
        'created_at' => 'Created At',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Customer Email',
        'hall_name' => 'Hall Name',
        'booking_date' => 'Booking Date',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'Export Successful',
        'export_success_body' => ':count payment(s) exported successfully.',
        'export_failed' => 'Export Failed',
        'export_failed_body' => 'Failed to export payments: :error',
        'json_export_success' => 'JSON Export Complete',
        'reconciliation_completed' => 'Reconciliation Completed',
        'reconciliation_completed_body' => ':count payment(s) reconciled. :mismatches mismatch(es) found.',
        'reconciliation_failed' => 'Reconciliation Failed',
        'report_generated' => 'Financial Report Generated',
        'report_failed' => 'Report Generation Failed',
        'retry_completed' => 'Retry Completed',
        'retry_completed_body' => ':count payment(s) queued for retry.',
        'retry_failed' => 'Retry Failed',
        'reminders_sent' => 'Reminders Sent',
        'reminders_sent_body' => ':count reminder email(s) sent successfully.',
        'reminders_failed' => 'Send Failed',
        'refund_success' => 'Refund Processed Successfully',
        'refund_success_body' => 'Refund of :amount OMR has been processed successfully.',
        'refund_failed' => 'Refund Failed',
        'refund_failed_body' => 'Failed to process refund: :error',
        'error_prefix' => 'Error: ',
        'email_sent' => 'Receipt Sent Successfully',
        'email_sent_body' => 'Payment receipt has been sent to :email',
        'email_failed' => 'Failed to Send Receipt',
        'email_failed_body' => 'Error: :error',
        'download_failed' => 'Download Failed',
        'download_failed_body' => 'Error generating receipt: :error',
        'print_failed' => 'Print Failed',
        'print_failed_body' => 'Error preparing receipt for print: :error',
    ],

    // Modals
    'modals' => [
        'refund' => [
            'heading' => 'Process Refund',
            'description' => 'This action will process a refund through Thawani payment gateway.',
        ],
        'reconcile' => [
            'heading' => 'Reconcile Payment Records',
            'description' => 'Match payment records with gateway transactions. This may take a few moments.',
        ],
        'retry' => [
            'heading' => 'Retry Failed Payments',
            'description' => 'Attempt to reprocess all failed payments. Only recent failures will be retried.',
        ],
        'reminders' => [
            'heading' => 'Send Payment Reminders',
            'description' => 'Send email reminders to customers with pending payments.',
        ],
        'email_receipt' => [
            'heading' => 'Send Receipt to Customer',
            'description' => 'Send payment receipt with PDF attachment to the customer email address.',
        ],
    ],

    // Placeholders
    'placeholders' => [
        'original_amount' => 'Original Amount',
        'already_refunded' => 'Already Refunded',
        'refundable_amount' => 'Available to Refund',
        'additional_notes' => 'Enter any additional details about this refund...',
        'custom_message' => 'Thank you for your booking with us...',
    ],

    // Descriptions
    'descriptions' => [
        'refund_process' => 'Process a full or partial refund for this payment.',
        'email_receipt' => 'The receipt will be sent as a PDF attachment to the specified email address.',
    ],

    // Helpers
    'helpers' => [
        'max_refund' => 'Maximum: :amount OMR',
        'notify_customer' => 'Customer will receive an email about this refund',
        'include_booking_details' => 'Include related booking information in export',
        'auto_update' => 'Automatically update payment statuses based on gateway data',
        'email_receipt' => 'Enter the email address where the receipt should be sent.',
        'send_admin_copy' => 'Also send a copy to the system admin email.',
        'custom_message' => 'Add an optional personal message to the email.',
    ],

    // =========================================================================
    // RECEIPT ERROR TRANSLATIONS
    // =========================================================================
    'errors' => [
        // ... existing errors ...
        'invalid_email' => 'Please enter a valid email address.',
    ],

    // =========================================================================
    // PDF RECEIPT TEMPLATE TRANSLATIONS
    // =========================================================================
    'receipt' => [
        'title' => 'PAYMENT RECEIPT',
        'tagline' => 'Hall Booking Management System',
        'amount_paid' => 'Amount Paid',
        'refund_amount' => 'Refunded Amount',
        'payment_details' => 'Payment Details',
        'booking_details' => 'Booking Details',
        'customer_info' => 'Customer Information',
        'hall' => 'Hall',
        'event_date' => 'Event Date',
        'time_slot' => 'Time Slot',
        'customer_name' => 'Customer Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'thank_you' => 'Thank you for your payment!',
        'thank_you_sub' => 'We appreciate your business and look forward to serving you.',
        'computer_generated' => 'This is a computer-generated receipt and does not require a signature.',
        'generated_on' => 'Generated on',
        'sultanate_oman' => 'Sultanate of Oman',
    ],

    // Report Messages
    'report_period' => '📅 Period: :from to :to',
    'report_revenue' => '💰 Total Revenue: :amount OMR (:count payments)',
    'report_refunds' => '↩️ Total Refunds: :amount OMR (:count refunds)',
    'report_failed' => '❌ Failed Payments: :count (:amount OMR)',
    'report_net_revenue' => '📊 Net Revenue: :amount OMR',

    // Common
    'n_a' => 'N/A',
    'processed_by' => 'Processed by',
    // Payment page
    'method' => 'Payment Method',
    'select_option' => 'Select Payment Option',
    'full' => 'Full Payment',
    'full_description' => 'Pay the full amount now',
    'advance' => 'Advance Payment',
    'advance_description' => 'Pay :percentage% now, rest before event',
    'balance' => 'Balance',
    'total_amount' => 'Total Amount',
    'secure' => 'Secure Payment',
    'redirect_message' => 'You will be redirected to Thawani secure payment gateway to complete your payment.',
    'terms_agreement' => 'I understand that my booking will be confirmed upon successful payment.',
    'view_terms' => 'View Terms & Conditions',
    'pay_now' => 'Pay Now',
    'redirecting' => 'Redirecting to payment...',
    'terms_required' => 'Please agree to the terms and conditions',

    // Price breakdown
    'hall_rental' => 'Hall Rental',
    'services' => 'Services',
    'platform_fee' => 'Platform Fee',
    'total' => 'Total',
];
