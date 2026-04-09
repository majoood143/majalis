<?php

return [
    // Resource Labels
    'singular' => 'Hall Availability',
    'plural' => 'Hall Availabilities',
    'navigation_label' => 'Hall Availability',

    // Form Sections
    'availability_details' => 'Availability Details',
    'block_reason' => 'Block Reason',
    'custom_pricing' => 'Custom Pricing',

    // Form Fields
    'hall' => 'Hall',
    'date' => 'Date',
    'time_slot' => 'Time Slot',
    'is_available' => 'Available',
    'reason' => 'Reason',
    'notes' => 'Notes',
    'custom_price' => 'Custom Price',

    // Time Slot Options
    'time_slots' => [
        'morning' => 'Morning (8 AM - 12 PM)',
        'afternoon' => 'Afternoon (12 PM - 5 PM)',
        'evening' => 'Evening (5 PM - 11 PM)',
        'full_day' => 'Full Day (8 AM - 11 PM)',
    ],

    // Time Slot Short Labels
    'time_slots_short' => [
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'full_day' => 'Full Day',
    ],

    // Reason Options
    'reasons' => [
        'maintenance' => 'Under Maintenance',
        'blocked' => 'Blocked by Owner',
        'holiday' => 'Holiday',
        'custom' => 'Custom Block',
    ],

    // Field Helpers
    'is_available_helper' => 'Uncheck to block this slot',
    'custom_pricing_description' => 'Override the default hall pricing for this specific date and slot',
    'custom_price_helper' => 'Leave empty to use default pricing',

    // Table Columns
    'hall_name' => 'Hall',
    'time_slot_label' => 'Time Slot',
    'reason_label' => 'Reason',
    'effective_price' => 'Effective Price',
    'created_at' => 'Created At',

    // Placeholders
    'unnamed_hall' => 'Unnamed Hall',
    'hall_deleted' => 'Hall Deleted',
    'default_price' => 'Default',
    'not_applicable' => 'N/A',

    // Filters
    'filters' => [
        'hall' => 'Hall',
        'time_slot' => 'Time Slot',
        'available' => 'Available',
        'date_range' => 'Date Range',
        'from' => 'From',
        'until' => 'Until',
        'available_only' => 'Available only',
        'blocked_only' => 'Blocked only',
    ],

    // Table Actions
    'table_actions' => [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'toggle' => 'Toggle Availability',
        'block' => 'Block',
        'unblock' => 'Unblock',
    ],

    // Bulk Actions
    'bulk_actions' => [
        'block_selected' => 'Block Selected',
        'unblock_selected' => 'Unblock Selected',
    ],

    // List Page - Header Actions
    'list_actions' => [
        'create' => 'Create Availability',
        'bulk_block' => 'Bulk Block Dates',
        'generate_availability' => 'Generate Availability',
        'export_calendar' => 'Export Calendar',
        'cleanup_past' => 'Cleanup Past Dates',
    ],

    // List Page - Tabs
    'tabs' => [
        'all' => 'All',
        'available' => 'Available',
        'blocked' => 'Blocked',
        'today' => 'Today',
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'custom_pricing' => 'Custom Pricing',
        'maintenance' => 'Maintenance',
        'past' => 'Past',
    ],

    // Bulk Block Modal
    'bulk_block_modal' => [
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'time_slots' => 'Time Slots',
        'time_slot_options' => [
            'morning' => 'Morning (8 AM - 12 PM)',
            'afternoon' => 'Afternoon (12 PM - 5 PM)',
            'evening' => 'Evening (5 PM - 11 PM)',
            'full_day' => 'Full Day (8 AM - 11 PM)',
        ],
        'block_reason' => 'Block Reason',
    ],

    // Generate Availability Modal
    'generate_availability_modal' => [
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'time_slots_to_generate' => 'Time Slots to Generate',
        'skip_existing' => 'Skip Existing Records',
        'skip_existing_helper' => 'Only create availability for dates that don\'t exist yet',
    ],

    // Export Calendar Modal
    'export_calendar_modal' => [
        'hall_optional' => 'Hall (Optional)',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
    ],

    // Cleanup Modal
    'cleanup_modal' => [
        'heading' => 'Delete Past Availability Records',
        'description' => 'This will permanently delete all availability records before today.',
    ],

    // Export CSV Headers
    'export_headers' => [
        'hall' => 'Hall',
        'date' => 'Date',
        'day' => 'Day',
        'time_slot' => 'Time Slot',
        'available' => 'Available',
        'reason' => 'Reason',
        'custom_price' => 'Custom Price',
        'effective_price' => 'Effective Price',
        'notes' => 'Notes',
    ],

    // Export Values
    'export_values' => [
        'yes' => 'Yes',
        'no' => 'No',
        'default' => 'Default',
    ],

    // Notifications
    'notifications' => [
        'bulk_block_completed' => 'Bulk Block Completed',
        'availability_generated' => 'Availability Generated',
        'calendar_exported' => 'Calendar Exported',
        'cleanup_completed' => 'Cleanup Completed',
        'download' => 'Download File',
        'created_updated' => 'Created: :created, Updated: :updated availability records.',
        'created_skipped' => 'Created/Updated: :created, Skipped: :skipped records.',
        'deleted_records' => ':count past availability record(s) deleted.',
        'created' => 'Availability Created',
        'created_body' => 'Hall availability has been set to :status.',
        'availability_updated' => 'Availability Updated',
        'availability_updated_body' => 'Slot availability has been updated.',
        'record_updated_body' => 'The availability record has been updated successfully.',
        'price_updated' => 'Price Updated',
        'price_updated_body' => 'Custom price has been updated successfully.',
        'duplication_completed' => 'Duplication Completed',
        'duplication_body' => 'Created: :created, Skipped: :skipped records.',
        'block_extended' => 'Block Period Extended',
        'block_extended_body' => ':count slot(s) blocked.',
        'deleted' => 'Availability Deleted',
        'deleted_body' => 'The availability record has been deleted.',
        'slot_now_available' => 'Slot is now available for bookings.',
        'slot_blocked' => 'Slot has been blocked.',
        'duplicated' => 'Availability Duplicated',
        'duplicated_body' => 'A new availability has been created for :date',
    ],

    // Messages
    'messages' => [
        'created' => 'Hall availability created successfully',
        'updated' => 'Hall availability updated successfully',
        'deleted' => 'Hall availability deleted successfully',
    ],

    // Status Labels
    'status' => [
        'available' => 'Available',
        'blocked' => 'Blocked',
        'days_suffix' => ' days',
        'week_prefix' => 'Week ',
        'guests_suffix' => ' guests',
    ],

    // Error / Validation Messages
    'errors' => [
        'invalid_date' => 'Invalid Date',
        'invalid_date_body' => 'Cannot create availability for past dates.',
        'invalid_date_edit_body' => 'Cannot set availability for past dates.',
        'duplicate_slot' => 'Duplicate Slot',
        'duplicate_slot_body' => 'This time slot already exists for the selected hall and date.',
        'invalid_price' => 'Invalid Price',
        'invalid_price_body' => 'Custom price cannot be negative.',
        'missing_reason' => 'Missing Reason',
        'missing_reason_body' => 'Please provide a reason for blocking this slot.',
        'existing_bookings' => 'Existing Bookings Found',
        'existing_bookings_body' => 'There are :count existing booking(s) for this slot. They may need to be cancelled.',
        'pending_bookings' => 'Pending Bookings',
        'pending_bookings_body' => 'There are :count pending booking(s) for this blocked slot.',
    ],

    // Create Page
    'create_page' => [
        'title' => 'Create Hall Availability',
        'subheading' => 'Set availability or block specific time slots for halls',
    ],

    // Edit Page
    'edit_page' => [
        'title' => 'Edit Availability',
        'toggle_block' => 'Block Slot',
        'toggle_unblock' => 'Unblock Slot',
        'block_heading' => 'Block This Slot',
        'unblock_heading' => 'Unblock This Slot',
        'block_description' => 'This will block the slot and prevent new bookings.',
        'unblock_description' => 'This will make the slot available for bookings again.',
        'block_reason_label' => 'Block Reason',
        'view_bookings' => 'View Bookings',
        'update_price' => 'Update Price',
        'default_hall_price' => 'Default Hall Price',
        'price_change_reason' => 'Reason for Price Change',
        'leave_empty_default' => 'Leave empty to use default hall pricing',
        'duplicate' => 'Duplicate to Other Dates',
        'same_time_slot' => 'Same Time Slot Only',
        'same_time_slot_helper' => 'Copy only the same time slot, or all time slots',
        'extend_block' => 'Extend Block Period',
        'extend_until' => 'Extend Until',
        'copy_settings' => 'Copy Block Settings',
        'copy_settings_helper' => 'Use the same reason and notes',
        'view_history' => 'View History',
        'close' => 'Close',
    ],

    // View Page
    'view_page' => [
        'title' => 'View Availability',
        'slot_information' => 'Slot Information',
        'status_availability' => 'Status & Availability',
        'pricing_information' => 'Pricing Information',
        'hall_details' => 'Hall Details',
        'statistics_insights' => 'Statistics & Insights',
        'system_information' => 'System Information',
        'hall_label' => 'Hall',
        'date_label' => 'Date',
        'time_slot_label' => 'Time Slot',
        'availability_status' => 'Availability Status',
        'block_reason' => 'Block Reason',
        'active_bookings' => 'Active Bookings',
        'notes_label' => 'Notes',
        'custom_price_label' => 'Custom Price',
        'default_hall_price' => 'Default Hall Price',
        'effective_price_label' => 'Effective Price',
        'city' => 'City',
        'hall_owner' => 'Hall Owner',
        'hall_capacity' => 'Hall Capacity',
        'days_until' => 'Days Until',
        'same_day_slots' => 'Same Day Slots',
        'day_of_week' => 'Day of Week',
        'week_number' => 'Week Number',
        'availability_id' => 'Availability ID',
        'created_at' => 'Created At',
        'last_updated' => 'Last Updated',
        'no_notes' => 'No additional notes',
        'using_default_price' => 'Using Default Price',
        'view_hall' => 'View Hall',
        'view_bookings' => 'View Bookings',
        'duplicate' => 'Duplicate',
        'duplicate_heading' => 'Duplicate Availability',
        'duplicate_description' => 'Create a copy of this availability for the next day.',
        'view_duplicate' => 'View Duplicate',
        'available_status' => '✓ Available',
        'blocked_status' => '✗ Blocked',
        'block_slot' => 'Block Slot',
        'unblock_slot' => 'Unblock Slot',
        'block_modal_heading' => 'Block This Slot?',
        'unblock_modal_heading' => 'Unblock This Slot?',
        'block_modal_description' => 'This will prevent new bookings for this time slot.',
        'unblock_modal_description' => 'This will make the slot available for bookings again.',
        'time_slot_morning' => 'Morning',
        'time_slot_afternoon' => 'Afternoon',
        'time_slot_evening' => 'Evening',
        'time_slot_full_day' => 'Full Day',
        'reason_maintenance' => 'Under Maintenance',
        'reason_blocked' => 'Blocked by Owner',
        'reason_custom' => 'Custom Block',
        'reason_holiday' => 'Holiday',
        'reason_na' => 'N/A',
    ],
];
