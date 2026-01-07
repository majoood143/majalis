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
    ],
    
    // Messages
    'messages' => [
        'created' => 'Hall availability created successfully',
        'updated' => 'Hall availability updated successfully',
        'deleted' => 'Hall availability deleted successfully',
    ],
];