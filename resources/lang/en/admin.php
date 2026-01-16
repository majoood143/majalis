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
        'singular' => 'Payout',
        'plural' => 'Payouts',
        'heading' => 'Owner Payouts',
        'subheading' => 'Manage payouts to hall owners',
        'navigation_group' => 'Financials',
        'navigation_label' => 'Payouts',

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
        'navigation_label' => 'Reports',
        'navigation_group' => 'Analytics',
        'overview' => 'Reports Overview',
        'singular' => 'Report',
        'plural' => 'Reports',
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

    // Resource Labels
    'hall' => 'Hall',
    'halls' => 'Halls',

    // Tab Labels
    'basic_info' => 'Basic Info',
    'location' => 'Location',
    'capacity_pricing' => 'Capacity & Pricing',
    'contact' => 'Contact',
    'features_media' => 'Features & Media',
    'settings' => 'Settings',

    // Basic Info Tab
    'city' => 'City',
    'owner' => 'Owner',
    'name_english' => 'Name (English)',
    'name_arabic' => 'Name (Arabic)',
    'url_slug' => 'URL Slug',
    'area' => 'Area',
    'description_english' => 'Description (English)',
    'description_arabic' => 'Description (Arabic)',

    // Advance Payment Tab
    'advance_payment' => 'Advance Payment',
    'advance_payment_settings' => 'Advance Payment Settings',
    'advance_payment_explanation' => 'Configure advance payment requirements for this hall. Customers will pay this amount upfront when booking.',
    'allows_advance_payment' => 'Allow Advance Payment',
    'allows_advance_payment_help' => 'Enable to require an advance payment for bookings',
    'advance_payment_type' => 'Advance Payment Type',
    'advance_payment_type_help' => 'Choose how the advance payment is calculated',
    'advance_type_fixed' => 'Fixed Amount',
    'advance_type_percentage' => 'Percentage of Total',
    'advance_payment_amount' => 'Advance Payment Amount',
    'advance_payment_amount_help' => 'Fixed amount to pay in advance (OMR)',
    'advance_payment_amount_placeholder' => 'Enter fixed amount',
    'advance_payment_percentage' => 'Advance Payment Percentage',
    'advance_payment_percentage_help' => 'Percentage of total booking price to pay in advance',
    'advance_payment_percentage_placeholder' => 'Enter percentage',
    'minimum_advance_payment' => 'Minimum Advance Payment',
    'minimum_advance_payment_help' => 'Ensure advance payment is at least this amount (OMR)',
    'minimum_advance_payment_placeholder' => 'Enter minimum amount',
    'advance_payment_preview' => 'Advance Payment Preview',
    'advance_payment_preview_help' => 'Preview how the advance payment works with sample pricing',
    'preview_for_price' => 'Preview for a booking of :price OMR',
    'customer_pays_advance' => 'Customer Pays (Advance)',
    'balance_due_before_event' => 'Balance Due (Before Event)',
    'advance_includes_services' => 'Advance payment calculation includes services. Balance includes additional charges.',

    // Location Tab
    'full_address' => 'Full Address',
    'address_english' => 'Address (English)',
    'address_arabic' => 'Address (Arabic)',
    'pick_location_on_map' => 'Pick Location on Map',
    'hall_location' => 'Hall Location',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'google_maps_url' => 'Google Maps URL',

    // Map Helper Texts
    'map_helper_click' => 'Click on the map to set the hall location, or drag the marker to adjust.',
    'coordinate_helper' => 'Auto-filled from map. Can also enter manually.',

    // Capacity & Pricing Tab
    'minimum_capacity' => 'Minimum Capacity',
    'maximum_capacity' => 'Maximum Capacity',
    'base_price_per_slot' => 'Base Price per Slot',
    'slot_specific_pricing' => 'Slot-Specific Pricing',
    'time_slot' => 'Time Slot',
    'price_omr' => 'Price (OMR)',

    // Contact Tab
    'phone_number' => 'Phone Number',
    'whatsapp' => 'WhatsApp',
    'email_address' => 'Email Address',

    // Features & Media Tab
    'hall_features' => 'Hall Features',
    'featured_image' => 'Featured Image',
    'gallery_images' => 'Gallery Images',
    'video_url' => 'Video URL',

    // Settings Tab
    'active' => 'Active',
    'featured' => 'Featured',
    'requires_approval' => 'Requires Approval',
    'cancellation_window' => 'Cancellation Window',
    'cancellation_fee' => 'Cancellation Fee',

    // Table Columns
    'image' => 'Image',
    'name' => 'Name',
    'capacity' => 'Capacity',
    'price' => 'Price',
    'bookings' => 'Bookings',
    'rating' => 'Rating',

    // Units
    'sqm' => 'sqm',
    'guests' => 'guests',
    'hours' => 'hours',

    // Helper Texts
    'auto_generate_slug' => 'Leave empty to auto-generate from English name',
    'recommended_image_size' => 'Recommended: 1920x1080 pixels',
    'max_images' => 'Maximum 10 images',
    'inactive_halls_hidden' => 'Inactive halls are hidden from customers',
    'featured_halls_highlighted' => 'Featured halls appear in highlighted sections',
    'allow_cancellation_help' => 'Minimum hours before booking to allow cancellation',
    'cancellation_fee_help' => 'Fee percentage charged on cancellation',

    // Placeholders
    'enter_hall_name_english' => 'Enter hall name in English',
    'enter_hall_name_arabic' => 'أدخل اسم القاعة بالعربية',
    'enter_full_address' => 'Enter the complete street address',
    'enter_address_english' => 'Address in English',
    'enter_address_arabic' => 'العنوان بالعربية',
    'enter_capacity_example' => 'e.g., 50',
    'enter_price_example' => 'e.g., 150.000',
    'phone_placeholder' => '+968 XXXX XXXX',
    'whatsapp_placeholder' => '+968 XXXX XXXX',
    'email_placeholder' => 'contact@hallname.com',
    'video_placeholder' => 'https://youtube.com/watch?v=...',

    // Table Filters
    'city_filter' => 'City',
    'owner_filter' => 'Owner',
    'featured_filter' => 'Featured',
    'active_filter' => 'Active',
    'min_capacity_filter' => 'Min Capacity',
    'max_capacity_filter' => 'Max Capacity',
    'featured_only' => 'Featured only',
    'not_featured' => 'Not featured',
    'active_only' => 'Active only',
    'inactive_only' => 'Inactive only',

    // Table Empty States
    'no_halls_found' => 'No halls found',
    'create_first_hall' => 'Create your first hall to get started.',

    // Messages
    'hall_created' => 'Hall created successfully',
    'hall_updated' => 'Hall updated successfully',
    'hall_deleted' => 'Hall deleted successfully',

    // Additional
    'add_price_override' => 'Add Price Override',
    'override_prices_help' => 'Override prices for: morning, afternoon, evening, full_day',
    'select_features_help' => 'Select all features available in this hall',
    'disabled' => 'Disabled',

    'optional_google_maps' => 'Optional: Paste a Google Maps link for this location',
    'youtube_vimeo_link' => 'YouTube or Vimeo link',
    'require_admin_approval' => 'Require admin approval for each booking',

    // Actions
    'actions' => [
        'export' => 'Export Halls',
        'export_modal_heading' => 'Export Halls Data',
        'export_modal_description' => 'Export all halls data to CSV.',
        'bulk_price_update' => 'Bulk Price Update',
        'bulk_price_modal_heading' => 'Update Prices in Bulk',
        'bulk_price_modal_description' => 'Update prices for multiple halls at once.',
        'generate_slugs' => 'Generate Missing Slugs',
        'generate_slugs_modal_heading' => 'Generate Missing Slugs',
        'generate_slugs_modal_description' => 'Generate URL slugs for halls without one.',
        'bulk_feature' => 'Bulk Feature Management',
        'sync_availability' => 'Sync Availability',
        'sync_availability_modal_heading' => 'Generate Availability Records',
        'sync_availability_modal_description' => 'Generate availability slots for all halls for the next 3 months.',
        'bulk_activation' => 'Bulk Activation',
        'download' => 'Download File',
        'create_hall' => 'Create New Hall',
        'create_hall_description' => 'Create a new hall in the system',
    ],

    // Fields
    'fields' => [
        'city' => 'City',
        'city_filter' => 'Filter by City (Optional)',
        'update_type' => 'Update Type',
        'percentage' => 'Percentage (%)',
        'amount' => 'Amount (OMR)',
        'action' => 'Action',
        'status' => 'Status',
    ],

    // Options
    'options' => [
        'percentage_increase' => 'Percentage Increase',
        'percentage_decrease' => 'Percentage Decrease',
        'fixed_increase' => 'Fixed Amount Increase',
        'fixed_decrease' => 'Fixed Amount Decrease',
        'mark_featured' => 'Mark as Featured',
        'unmark_featured' => 'Remove Featured Status',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
    ],

    // Tabs
    'tabs' => [
        'all' => 'All Halls',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'featured' => 'Featured',
        'pending_approval' => 'Pending Approval',
        'high_capacity' => 'High Capacity (500+)',
        'premium_price' => 'Premium (1000+ OMR)',
        'highly_rated' => 'Highly Rated (4.5+)',
        'with_video' => 'With Video',
        'incomplete' => 'Incomplete Profile',
        'no_bookings' => 'No Bookings',
    ],

    // Export Headers
    'export' => [
        'name_en' => 'Name (EN)',
        'name_ar' => 'Name (AR)',
        'slug' => 'Slug',
        'city' => 'City',
        'owner' => 'Owner',
        'address' => 'Address',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'capacity_min' => 'Min Capacity',
        'capacity_max' => 'Max Capacity',
        'base_price' => 'Base Price',
        'phone' => 'Phone',
        'email' => 'Email',
        'total_bookings' => 'Total Bookings',
        'average_rating' => 'Average Rating',
        'featured' => 'Featured',
        'active' => 'Active',
        'created_at' => 'Created At',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'Export Successful',
        'export_success_body' => 'Halls data exported successfully.',
        'export_error' => 'Export Failed',
        'prices_updated' => 'Prices Updated',
        'prices_updated_body' => ':count hall(s) updated successfully.',
        'slugs_generated' => 'Slugs Generated',
        'slugs_generated_body' => ':count slug(s) have been generated.',
        'feature_updated' => 'Featured Status Updated',
        'feature_updated_body' => ':count hall(s) updated successfully.',
        'activation_updated' => 'Status Updated',
        'activation_updated_body' => ':count hall(s) updated successfully.',
        'availability_synced' => 'Availability Synced',
        'availability_synced_body' => ':count availability slot(s) created.',
        'update_error' => 'Operation Failed',
    ],

    // Common
    'yes' => 'Yes',
    'no' => 'No',

    // Stats
    'stats' => [
        'total_halls' => 'Total Halls',
        'total_halls_desc' => 'All halls in system',
        'active_halls' => 'Active Halls',
        'active_halls_desc' => 'Currently active',
        'featured_halls' => 'Featured Halls',
        'featured_halls_desc' => 'Marked as featured',
        'pending_halls' => 'Pending Halls',
        'pending_halls_desc' => 'Awaiting approval',
        'average_price' => 'Average Price',
        'average_price_desc' => 'Per slot',
    ],

    'hall_navigation_group' => 'Majalis Management',

];
