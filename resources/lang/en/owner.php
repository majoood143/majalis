<?php

declare(strict_types=1);

/**
 * English Translation File for Owner Panel
 *
 * This file contains all translation strings for the Hall Owner Panel
 * in the Majalis Hall Booking Management System.
 *
 * @package    Majalis
 * @subpackage Translations
 * @version    2.0.0
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Brand & Auth
    |--------------------------------------------------------------------------
    */
    'brand' => [
        'name' => ':app - Hall Owner Portal',
    ],

    'auth' => [
        'login_title' => 'Owner Login',
        'login_heading' => 'Welcome Back!',
        'login_subheading' => 'Manage your halls and bookings',
        'email' => 'Email Address',
        'email_placeholder' => 'owner@example.com',
        'email_helper' => 'Use your registered email address',
        'password' => 'Password',
        'password_placeholder' => 'Enter your password',
        'password_helper' => 'Minimum 8 characters',
        'remember_me' => 'Remember me',
        'not_authorized' => 'You are not authorized to access the owner portal.',
        'account_suspended' => 'Your account has been suspended. Please contact support.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Groups
    |--------------------------------------------------------------------------
    */
    'nav_groups' => [
        'overview' => 'Overview',
        'hall_management' => 'Hall Management',
        'bookings' => 'Bookings',
        'finance' => 'Finance',
        'customers' => 'Customers',
        'reports' => 'Reports',
        'settings' => 'Settings',
    ],

    'user_menu' => [
        'profile' => 'My Profile',
        'hall_settings' => 'Hall Settings',
        'help' => 'Help & Support',
    ],

    'search' => [
        'suffix' => 'in owner portal',
    ],

    'errors' => [
        'unauthorized_title' => 'Access Denied',
        'unauthorized_body' => 'You do not have permission to access the owner portal.',
        'unauthorized' => 'You are not authorized to access this resource.',
        'account_suspended_title' => 'Account Suspended',
        'account_suspended_body' => 'Your owner account has been suspended. Please contact support for assistance.',
    ],

    'warnings' => [
        'complete_profile_title' => 'Complete Your Profile',
        'complete_profile_body' => 'Please complete your profile to access all features.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'title' => 'Dashboard - :name',
        'subheading' => 'Here\'s what\'s happening with your halls on :date',
        'good_morning' => 'Good Morning',
        'good_afternoon' => 'Good Afternoon',
        'good_evening' => 'Good Evening',
        'refresh' => 'Refresh',
        'export' => 'Export Report',
        'export_confirm' => 'Export Dashboard Data',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'select_hall' => 'Select Hall',
        'all_halls' => 'All Halls',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Widgets
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'revenue_trend' => 'Revenue Trend',
        'revenue_last_days' => 'Last :days days',
        'gross_revenue' => 'Gross Revenue',
        'net_revenue' => 'Net Revenue (After Commission)',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'last_60_days' => 'Last 60 Days',
        'last_90_days' => 'Last 90 Days',
        'recent_bookings' => 'Recent Bookings',
        'upcoming_bookings' => 'Upcoming Bookings',
        'bookings' => 'bookings',
        'today' => 'Today',
        'tomorrow' => 'Tomorrow',
        'no_upcoming_bookings' => 'No upcoming bookings',
        'view_all_bookings' => 'View All Bookings',
        'hall_performance' => 'Hall Performance Overview',
        'booking_statistics' => 'Booking Statistics',
        'bookings_by_status' => 'Distribution by Status',
        'bookings_by_slot' => 'Distribution by Time Slot',
        'bookings_by_hall' => 'Distribution by Hall',
        'bookings_by_source' => 'Distribution by Source',
        'booking_distribution' => 'Booking Distribution',
        'by_status' => 'By Status',
        'by_time_slot' => 'By Time Slot',
        'by_hall' => 'By Hall',
        'by_source' => 'By Source',
        'registered_customers' => 'Registered Customers',
        'guest_bookings' => 'Guest Bookings',
        'recent_activities' => 'Recent Activities',
        'no_recent_activities' => 'No recent activities',
        'view_all_activities' => 'View All Activities',
        'pending_actions' => 'Pending Actions',
        'urgent' => 'Urgent',
        'pending' => 'Pending',
        'all_clear' => 'All Clear',
        'take_action' => 'Take Action',
        'more_actions_pending' => 'And :count more actions pending',
        'bookings_to_confirm' => 'bookings to confirm',
        'payments_pending' => 'payments pending',
        'reviews_to_respond' => 'reviews to respond',
        'tickets_open' => 'tickets open',
        'no_pending_actions' => 'No Pending Actions',
        'all_caught_up' => 'You\'re all caught up!',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Stats
    |--------------------------------------------------------------------------
    */
    'stats' => [
        'total_revenue' => 'Total Revenue',
        'revenue_increase' => ':percent% increase from last period',
        'revenue_decrease' => ':percent% decrease from last period',
        'total_bookings' => 'Total Bookings',
        'bookings_breakdown' => ':confirmed confirmed, :pending pending',
        'occupancy_rate' => 'Occupancy Rate',
        'slots_booked' => ':booked of :total slots booked',
        'pending_payments' => 'Pending Payments',
        'pending_count' => ':count pending transactions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */
    'activities' => [
        'system' => 'System',
        'unknown_activity' => 'Activity performed',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'hall_created' => 'Hall ":name" was created',
        'hall_updated' => 'Hall ":name" was updated',
        'hall_deleted' => 'Hall was deleted',
        'hall_activated' => 'Hall ":name" was activated',
        'hall_deactivated' => 'Hall ":name" was deactivated',
        'booking_created' => 'New booking #:number created',
        'booking_confirmed' => 'Booking #:number confirmed',
        'booking_cancelled' => 'Booking #:number cancelled',
        'booking_completed' => 'Booking #:number completed',
        'payment_received' => 'Payment of OMR :amount received',
        'payment_completed' => 'Payment of OMR :amount completed',
        'payment_refunded' => 'Payment of OMR :amount refunded',
        'payment_failed' => 'Payment failed',
        'review_received' => 'New :rating star review received',
        'review_replied' => 'Replied to review',
        'user_logged_in' => 'Logged into owner portal',
        'user_logged_out' => 'Logged out of owner portal',
        'field_changed' => [
            'status' => 'Status changed from :from to :to',
            'total_amount' => 'Amount changed from :from to :to',
            'event_date' => 'Date changed from :from to :to',
            'time_slot' => 'Time slot changed from :from to :to',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Levels
    |--------------------------------------------------------------------------
    */
    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Halls Resource
    |--------------------------------------------------------------------------
    */
    'halls' => [
        // Navigation & Labels
        'navigation' => 'My Halls',
        'singular' => 'Hall',
        'plural' => 'Halls',
        'title' => 'My Halls',
        'heading' => 'Manage Your Halls',
        'subheading' => 'You have :count hall(s) registered',
        'guests' => 'guests',
        'name' => 'Hall Name',
        'status' => 'Status',
        'total_bookings' => 'Total Bookings',
        'this_month' => 'This Month',
        'revenue' => 'Total Revenue',
        'occupancy' => 'Occupancy',
        'rating' => 'Rating',
        'next_booking' => 'Next Booking',

        // Tabs
        'tabs' => [
            'basic' => 'Basic Information',
            'location' => 'Location',
            'pricing' => 'Capacity & Pricing',
            'features' => 'Features',
            'media' => 'Media',
            'contact' => 'Contact',
            'settings' => 'Settings',
            'all' => 'All Halls',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'featured' => 'Featured',
        ],

        // Sections
        'sections' => [
            'basic_info' => 'Basic Information',
            'basic_info_desc' => 'Enter the hall\'s basic details in both languages',
            'address' => 'Address Details',
            'capacity' => 'Capacity',
            'pricing' => 'Pricing',
            'pricing_desc' => 'Set base price and slot-specific overrides',
            'advance_payment' => 'Advance Payment',
            'advance_payment_desc' => 'Configure advance payment requirements',
            'amenities' => 'Amenities & Features',
            'amenities_desc' => 'Select the features available in your hall',
            'images' => 'Images',
            'video' => 'Video & Virtual Tour',
            'contact' => 'Contact Information',
            'status' => 'Status',
            'booking_settings' => 'Booking Settings',
            'details' => 'Hall Details',
        ],

        // Fields
        'fields' => [
            'name' => 'Hall Name',
            'name_en' => 'Hall Name (English)',
            'name_ar' => 'Hall Name (Arabic)',
            'description' => 'Description',
            'description_en' => 'Description (English)',
            'description_ar' => 'Description (Arabic)',
            'city' => 'City',
            'slug' => 'URL Slug',
            'address_en' => 'Address (English)',
            'address_ar' => 'Address (Arabic)',
            'google_maps_url' => 'Google Maps Link',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'capacity' => 'Capacity',
            'capacity_min' => 'Minimum Capacity',
            'capacity_max' => 'Maximum Capacity',
            'area_sqm' => 'Area',
            'area' => 'Area (m²)',
            'base_price' => 'Base Price per Slot',
            'price_morning' => 'Morning Price',
            'price_afternoon' => 'Afternoon Price',
            'price_evening' => 'Evening Price',
            'price_full_day' => 'Full Day Price',
            'advance_required' => 'Require Advance Payment',
            'advance_type' => 'Advance Type',
            'advance_percentage' => 'Advance Percentage',
            'advance_amount' => 'Advance Amount',
            'advance_minimum' => 'Minimum Advance',
            'featured_image' => 'Featured Image',
            'gallery' => 'Gallery Images',
            'video_url' => 'Video URL',
            'virtual_tour' => 'Virtual Tour URL',
            'phone' => 'Phone Number',
            'whatsapp' => 'WhatsApp Number',
            'email' => 'Email Address',
            'is_active' => 'Active',
            'is_featured' => 'Featured',
            'requires_approval' => 'Require Booking Approval',
            'cancellation_hours' => 'Cancellation Notice',
            'cancellation_fee' => 'Cancellation Fee',
        ],

        // Helpers
        'helpers' => [
            'slug' => 'Auto-generated from hall name. Used in URLs.',
            'base_price' => 'Default price for all time slots',
            'pricing_override_info' => 'Leave empty to use base price for that slot',
            'advance_minimum' => 'Minimum amount if percentage results in lower value',
            'featured_image' => 'Main image shown in listings (max 5MB)',
            'gallery' => 'Additional images (max 20 images, 5MB each)',
            'video_url' => 'YouTube or Vimeo video URL',
            'virtual_tour' => '360° virtual tour URL',
            'is_active' => 'Inactive halls are not visible to customers',
            'featured_admin_only' => 'Featured status is managed by administrators',
            'requires_approval' => 'Bookings will require your approval before being confirmed',
            'cancellation_hours' => 'Hours before event when cancellation is allowed',
        ],

        // Placeholders
        'placeholders' => [
            'use_base' => 'Use base price',
        ],

        // Suffixes
        'suffixes' => [
            'hours' => 'hours',
        ],

        // Advance Types
        'advance_types' => [
            'percentage' => 'Percentage of Total',
            'fixed' => 'Fixed Amount',
        ],

        // Table Columns
        'columns' => [
            'name' => 'Hall Name',
            'capacity' => 'Capacity',
            'price' => 'Base Price',
            'rating' => 'Rating',
            'bookings' => 'Bookings',
            'status' => 'Status',
            'featured' => 'Featured',
        ],

        // Filters
        'filters' => [
            'status' => 'Status',
            'active' => 'Active Only',
            'inactive' => 'Inactive Only',
        ],

        // Actions
        'actions' => [
            'create' => 'Add New Hall',
            'view' => 'View Details',
            'edit' => 'Edit Hall',
            'delete' => 'Delete Hall',
            'availability' => 'Manage Availability',
            'regenerate' => 'Regenerate Availability',
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
            'view_public' => 'View on Website',
        ],

        // Bulk Actions
        'bulk' => [
            'activate' => 'Activate Selected',
            'deactivate' => 'Deactivate Selected',
        ],

        // Modals
        'modals' => [
            'regenerate_heading' => 'Regenerate Availability',
            'regenerate_description' => 'This will create availability slots for the next 90 days. Existing booked slots will not be affected.',
            'delete_heading' => 'Delete Hall',
            'delete_description' => 'Are you sure you want to delete this hall? This action cannot be undone.',
        ],

        // Notifications
        'notifications' => [
            'created' => 'Hall Created Successfully',
            'created_body' => 'Your hall has been created and availability has been generated.',
            'availability_generated' => 'Availability slots have been generated for the next 90 days.',
            'updated' => 'Hall Updated Successfully',
            'updated_body' => 'Your changes have been saved.',
            'activated' => 'Hall Activated',
            'deactivated' => 'Hall Deactivated',
            'availability_regenerated' => 'Availability Regenerated',
        ],

        // Errors
        'errors' => [
            'cannot_delete' => 'Cannot Delete Hall',
            'has_active_bookings' => 'This hall has :count active booking(s). Please cancel or complete them first.',
        ],

        // Stats
        'stats' => [
            'bookings' => 'Bookings',
            'rating' => 'Rating',
            'reviews' => 'Reviews',
            'price' => 'Base Price',
        ],

        // Empty State
        'empty' => [
            'heading' => 'No Halls Found',
            'description' => 'Create your first hall to start accepting bookings.',
            'action' => 'Add Your First Hall',
        ],

        // Create/Edit
        'create' => [
            'title' => 'Add New Hall',
        ],
        'edit' => [
            'title' => 'Edit: :name',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Availability
    |--------------------------------------------------------------------------
    */
    'availability' => [
        'title' => 'Manage Availability: :hall',
        'breadcrumb' => 'Availability',
        'today' => 'Today',
        'legend' => 'Legend',
        'available' => 'Available',
        'blocked' => 'Blocked',
        'booked' => 'Booked',
        'maintenance' => 'Maintenance',
        'past' => 'Past',
        'dates_selected' => ':count dates selected',
        'clear_selection' => 'Clear Selection',
        'block_reason' => 'Block Reason',
        'block_selected' => 'Block Selected',
        'unblock_selected' => 'Unblock Selected',
        'custom_price' => 'Custom Price (OMR)',
        'set_price' => 'Set Price',
        'clear_price' => 'Clear Price',
        'slot_filter' => 'Time Slot Filter',
        'select_all_future' => 'Select All Future Dates',
        'pricing_info' => 'Pricing Information',
        'base_price' => 'Base Price',
        'date' => 'Date',
        'time_slot' => 'Time Slot',
        'is_available' => 'Available',
        'reason' => 'Reason',
        'status' => 'Status',
        'future_only' => 'Future Dates Only',
        'block' => 'Block',
        'unblock' => 'Unblock',
        'bulk_operations' => 'Bulk Operations',
        'time_slots' => 'Time Slots',

        // Reasons
        'reasons' => [
            'blocked' => 'Blocked by Owner',
            'maintenance' => 'Maintenance',
            'holiday' => 'Holiday',
            'private_event' => 'Private Event',
            'renovation' => 'Renovation',
            'booked' => 'Booked',
            'other' => 'Other',
        ],

        // Actions
        'actions' => [
            'back' => 'Back to Hall',
            'regenerate' => 'Generate 90 Days',
        ],

        // Modals
        'modals' => [
            'regenerate_heading' => 'Regenerate Availability',
            'regenerate_description' => 'This will create availability slots for the next 90 days.',
        ],

        // Notifications
        'notifications' => [
            'blocked' => 'Slot Blocked',
            'unblocked' => 'Slot Unblocked',
            'no_selection' => 'No Selection',
            'select_dates_slots' => 'Please select dates and time slots first.',
            'slots_blocked' => 'Slots Blocked',
            'slots_blocked_count' => ':count slots have been blocked.',
            'slots_unblocked' => 'Slots Unblocked',
            'slots_unblocked_count' => ':count slots have been unblocked.',
            'invalid_price' => 'Please enter a valid price.',
            'price_updated' => 'Custom Price Updated',
            'price_cleared' => 'Custom Price Cleared',
            'regenerated' => 'Availability Regenerated',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Slots
    |--------------------------------------------------------------------------
    */
    'slots' => [
        'morning' => 'Morning (8:00 AM - 12:00 PM)',
        'afternoon' => 'Afternoon (1:00 PM - 5:00 PM)',
        'evening' => 'Evening (6:00 PM - 11:00 PM)',
        'full_day' => 'Full Day',
    ],

    /*
    |--------------------------------------------------------------------------
    | FullCalendar Widget
    |--------------------------------------------------------------------------
    */
    'fullcalendar' => [
        // Navigation & Page
        'navigation' => 'Calendar',
        'page_title' => 'Availability Calendar',
        'heading' => 'Availability Calendar',
        'subheading' => 'Visual calendar for managing hall availability',

        // Hall Selection
        'select_hall' => 'Select Hall',
        'all_halls' => 'All Halls',

        // Actions
        'actions' => [
            'create' => 'Create Slot',
            'edit' => 'Edit Slot',
            'delete' => 'Delete Slot',
            'generate' => 'Generate Slots',
            'view' => 'View Details',
            'block' => 'Block',
            'unblock' => 'Unblock',
        ],

        // Empty State
        'no_events' => 'No availability slots found',

        // Instructions
        'instructions' => [
            'title' => 'How to use the calendar:',
            'click_date' => 'Click on a date to create a new availability slot',
            'click_event' => 'Click on an event to view or edit details',
            'drag_event' => 'Drag events to move them to a different date',
            'filter_hall' => 'Use the filter button to view slots for a specific hall',
        ],

        // Notifications
        'notifications' => [
            'select_hall_first' => 'Please select a hall first',
            'cannot_select_past' => 'Cannot create slots in the past',
            'cannot_move_to_past' => 'Cannot move slots to past dates',
            'cannot_move_booked' => 'Cannot move booked slots',
            'moved_success' => 'Slot moved successfully',
            'created_success' => 'Slot created successfully',
            'updated_success' => 'Slot updated successfully',
            'deleted_success' => 'Slot deleted successfully',
        ],

        // Tooltips
        'tooltips' => [
            'available' => 'Available',
            'blocked' => 'Blocked',
            'booked' => 'Booked',
            'maintenance' => 'Under Maintenance',
        ],

        // View Labels
        'views' => [
            'month' => 'Month',
            'week' => 'Week',
            'day' => 'Day',
            'list' => 'List',
        ],

        // Time Slots Short
        'slots_short' => [
            'morning' => 'M',
            'afternoon' => 'A',
            'evening' => 'E',
            'full_day' => 'F',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Status
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'paid' => 'Paid',
        'pending' => 'Pending',
        'partial' => 'Partial',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ],

    /*
    |--------------------------------------------------------------------------
    | Relation Managers
    |--------------------------------------------------------------------------
    */
    'relation' => [
        'availabilities' => 'Availability',
        'extra_services' => 'Extra Services',
        'images' => 'Gallery',
        'bookings' => 'Bookings',
        'reviews' => 'Reviews',
    ],

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */
    'services' => [
        'basic_info' => 'Service Information',
        'name' => 'Service Name',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
        'description_en' => 'Description (English)',
        'description_ar' => 'Description (Arabic)',
        'pricing' => 'Pricing',
        'price' => 'Price',
        'pricing_unit' => 'Pricing Unit',
        'min_quantity' => 'Minimum Quantity',
        'max_quantity' => 'Maximum Quantity',
        'settings' => 'Settings',
        'is_active' => 'Active',
        'is_required' => 'Required',
        'is_required_help' => 'Required services will be automatically included in every booking.',
        'required' => 'Required',
        'active' => 'Active',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'units' => [
            'fixed' => 'Fixed Price',
            'per_person' => 'Per Person',
            'per_hour' => 'Per Hour',
            'per_item' => 'Per Item',
            'per_table' => 'Per Table',
            'per_plate' => 'Per Plate',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Features Resource
    |--------------------------------------------------------------------------
    */
    'features' => [
        // Navigation & Labels
        'navigation' => 'Features & Amenities',
        'singular' => 'Feature',
        'plural' => 'Features & Amenities',
        'title' => 'Features & Amenities',
        'heading' => 'Hall Features',
        'subheading' => 'Browse and manage features for your halls',

        // Columns
        'columns' => [
            'name' => 'Feature',
            'description' => 'Description',
            'your_halls' => 'Your Halls',
            'added' => 'Added',
        ],

        // Fields
        'fields' => [
            'icon' => 'Icon',
            'name' => 'Feature Name',
            'description' => 'Description',
            'select_halls' => 'Select Halls to Add Feature',
            'select_halls_remove' => 'Select Halls to Remove Feature From',
            'your_halls_with' => 'Your Halls with This Feature',
            'feature_name_en' => 'Feature Name (English)',
            'feature_name_ar' => 'Feature Name (Arabic)',
        ],

        // Placeholders
        'placeholders' => [
            'describe_feature' => 'Describe why you need this feature...',
        ],

        // Filters
        'filters' => [
            'all' => 'All Features',
            'added_status' => 'Added Status',
            'added_only' => 'Added to My Halls',
            'not_added' => 'Not Added Yet',
        ],

        // Tabs
        'tabs' => [
            'all' => 'All Features',
            'added' => 'Added to Halls',
            'not_added' => 'Not Added',
            'popular' => 'Most Popular',
        ],

        // Actions
        'actions' => [
            'view' => 'View Details',
            'add_to_hall' => 'Add to Hall',
            'remove_from_hall' => 'Remove from Hall',
            'confirm_remove' => 'Confirm Remove Feature',
            'manage_halls' => 'Manage Hall Features',
            'request_feature' => 'Request New Feature',
            'back_to_list' => 'Back to Features',
            'save_changes' => 'Save All Changes',
            'select_all' => 'Select All',
            'deselect_all' => 'Deselect All',
            'copy_from' => 'Copy features from',
        ],

        // Bulk Actions
        'bulk' => [
            'add_to_hall' => 'Add Selected to Hall',
        ],

        // Notifications
        'notifications' => [
            'added' => 'Feature Added',
            'added_body' => ':feature added to :count hall(s)',
            'already_added' => 'Already Added',
            'already_added_body' => ':count hall(s) already have this feature',
            'removed' => 'Feature Removed',
            'removed_body' => ':feature removed from :count hall(s)',
            'bulk_added' => 'Features Added',
            'bulk_added_body' => ':count feature(s) added successfully',
            'request_sent' => 'Request Submitted',
            'request_sent_body' => 'Your feature request has been submitted for admin review. You will be notified once it\'s approved.',
            'feature_added' => ':feature added',
            'feature_removed' => ':feature removed',
            'all_added' => 'All features added',
            'all_removed' => 'All features removed',
            'copied' => 'Features Copied',
            'copied_body' => 'Features copied from :source',
            'all_saved' => 'All changes saved',
        ],

        // Empty State
        'empty' => [
            'heading' => 'No Features Available',
            'description' => 'There are no features available in the system yet.',
        ],

        // Info
        'no_description' => 'No description available',
        'not_added_yet' => 'Not added to any of your halls yet',
        'items' => 'features',

        // Stats
        'stats' => [
            'selected' => 'Selected',
            'available' => 'Available',
            'coverage' => 'Coverage',
        ],

        // Legend
        'legend' => [
            'title' => 'Legend',
            'selected' => 'Feature is selected',
            'not_selected' => 'Feature is not selected',
        ],

        // Manage Page
        'manage' => [
            'title' => 'Manage Hall Features',
            'heading' => 'Manage Features',
            'subheading' => 'Add or remove features from your halls',
            'select_hall' => 'Select a Hall',
            'available_features' => 'Available Features',
            'matrix_view' => 'Matrix View',
            'single_view' => 'Single Hall View',
            'feature' => 'Feature',
            'no_hall_selected' => 'No Hall Selected',
            'select_hall_prompt' => 'Please select a hall to manage its features',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */
    'images' => [
        'image' => 'Image',
        'preview' => 'Preview',
        'type' => 'Type',
        'alt' => 'Alt Text',
        'alt_en' => 'Alt Text (English)',
        'alt_ar' => 'Alt Text (Arabic)',
        'alt_help' => 'Describes the image for accessibility',
        'caption_en' => 'Caption (English)',
        'caption_ar' => 'Caption (Arabic)',
        'order' => 'Order',
        'set_featured' => 'Set as Featured',
        'types' => [
            'featured' => 'Featured',
            'gallery' => 'Gallery',
            'floor_plan' => 'Floor Plan',
            'panorama' => 'Panorama',
            'exterior' => 'Exterior',
            'interior' => 'Interior',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Resource
    |--------------------------------------------------------------------------
    */
    'gallery' => [
        // Navigation & Labels
        'navigation' => 'Gallery',
        'singular' => 'Image',
        'plural' => 'Gallery Images',
        'title' => 'Gallery Management',
        'heading' => 'Hall Gallery',
        'subheading' => 'Manage images for your halls',

        // Sections
        'sections' => [
            'image' => 'Image Upload',
            'image_desc' => 'Upload and configure the image',
            'metadata' => 'Image Metadata',
            'metadata_desc' => 'Optional title, caption and SEO information',
            'preview' => 'Image Preview',
            'info' => 'Image Information',
        ],

        // Fields
        'fields' => [
            'hall' => 'Hall',
            'image' => 'Image',
            'type' => 'Image Type',
            'is_featured' => 'Featured Image',
            'is_active' => 'Active',
            'order' => 'Display Order',
            'title' => 'Title',
            'title_en' => 'Title (English)',
            'title_ar' => 'Title (Arabic)',
            'caption' => 'Caption',
            'caption_en' => 'Caption (English)',
            'caption_ar' => 'Caption (Arabic)',
            'alt_text' => 'Alt Text',
            'file_size' => 'File Size',
            'dimensions' => 'Dimensions',
            'format' => 'Format',
            'uploaded_at' => 'Uploaded At',
        ],

        // Placeholders
        'placeholders' => [
            'title' => 'e.g., Main Hall Entrance',
            'title_ar' => 'مثال: مدخل القاعة الرئيسية',
        ],

        // Helpers
        'helpers' => [
            'image' => 'Max 5MB. Formats: JPEG, PNG, WebP. Recommended: 1920×1080 pixels',
            'is_featured' => 'Show this image in featured sections and thumbnails',
            'is_active' => 'Only active images are shown to customers',
            'alt_text' => 'Describe the image for accessibility and SEO',
        ],

        // Types
        'types' => [
            'gallery' => 'Gallery',
            'featured' => 'Featured',
            'floor_plan' => 'Floor Plan',
            'exterior' => 'Exterior',
            'interior' => 'Interior',
        ],

        // Columns
        'columns' => [
            'image' => 'Image',
            'hall' => 'Hall',
            'title' => 'Title',
            'type' => 'Type',
            'featured' => 'Featured',
            'active' => 'Active',
            'size' => 'Size',
            'dimensions' => 'Dimensions',
            'order' => 'Order',
            'uploaded' => 'Uploaded',
        ],

        // Filters
        'filters' => [
            'hall' => 'Filter by Hall',
            'type' => 'Image Type',
            'featured' => 'Featured',
            'featured_only' => 'Featured Only',
            'not_featured' => 'Not Featured',
            'status' => 'Status',
            'active_only' => 'Active Only',
            'inactive_only' => 'Inactive Only',
            'all' => 'All',
            'all_types' => 'All Types',
        ],

        // Tabs
        'tabs' => [
            'all' => 'All Images',
            'active' => 'Active',
            'featured' => 'Featured',
            'gallery' => 'Gallery',
            'exterior' => 'Exterior',
            'interior' => 'Interior',
            'inactive' => 'Inactive',
        ],

        // Actions
        'actions' => [
            'upload' => 'Upload Image',
            'bulk_upload' => 'Bulk Upload',
            'visual_manager' => 'Visual Manager',
            'mark_featured' => 'Mark as Featured',
            'unmark_featured' => 'Remove Featured',
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
            'download' => 'Download',
            'delete' => 'Delete',
            'edit_details' => 'Edit Details',
            'back_to_gallery' => 'Back to Gallery',
            'set_hall_featured' => 'Set as Hall Cover',
        ],

        // Bulk Actions
        'bulk' => [
            'activate' => 'Activate Selected',
            'deactivate' => 'Deactivate Selected',
            'change_type' => 'Change Type',
        ],

        // Notifications
        'notifications' => [
            'uploaded' => 'Image Uploaded',
            'uploaded_body' => 'The image has been uploaded successfully.',
            'updated' => 'Image Updated',
            'updated_body' => 'The image details have been updated.',
            'deleted' => 'Image Deleted',
            'marked_featured' => 'Marked as Featured',
            'unmarked_featured' => 'Removed from Featured',
            'activated' => 'Image Activated',
            'deactivated' => 'Image Deactivated',
            'order_updated' => 'Order Updated',
            'bulk_activated' => ':count image(s) activated',
            'bulk_deactivated' => ':count image(s) deactivated',
            'bulk_type_changed' => ':count image(s) type changed',
            'bulk_uploaded' => 'Images Uploaded',
            'bulk_uploaded_body' => ':count image(s) uploaded successfully',
            'select_hall_first' => 'Please select a hall first',
            'no_files' => 'No files selected',
            'some_failed' => 'Some uploads failed',
            'some_failed_body' => ':count image(s) failed to upload',
            'set_featured' => 'Hall Cover Set',
            'set_featured_body' => 'This image is now the hall\'s main cover image',
            'type_changed' => 'Image type changed',
        ],

        // Empty State
        'empty' => [
            'heading' => 'No Images Yet',
            'description' => 'Upload images to showcase your hall to potential customers.',
            'action' => 'Upload First Image',
        ],

        // Create/Edit
        'create' => [
            'title' => 'Upload Image',
            'heading' => 'Upload New Image',
        ],
        'edit' => [
            'title' => 'Edit Image',
        ],

        // Stats
        'stats' => [
            'total' => 'total',
            'active' => 'active',
            'featured' => 'featured',
        ],

        // Status
        'status' => [
            'inactive' => 'Inactive',
        ],

        // Badges
        'badges' => [
            'featured' => 'Featured',
        ],

        // Confirm
        'confirm_delete' => 'Are you sure you want to delete this image? This action cannot be undone.',

        // Bulk Upload Page
        'bulk_upload' => [
            'title' => 'Bulk Upload',
            'heading' => 'Bulk Image Upload',
            'subheading' => 'Upload multiple images at once',
            'select_hall' => 'Select a hall...',
            'current_images' => 'Current images',
            'max_20' => 'Max 20 images per upload',
            'upload_images' => 'Upload Images',
            'drag_drop' => 'Drag and drop images here',
            'or' => 'or',
            'browse' => 'Browse Files',
            'allowed_formats' => 'JPEG, PNG, WebP • Max 5MB each • Up to 20 files',
            'uploading' => 'Uploading...',
            'selected_files' => 'Selected Files',
            'clear_all' => 'Clear All',
            'cancel' => 'Cancel',
            'upload_all' => 'Upload All Images',
            'success_count' => ':count image(s) uploaded successfully',
            'failed_count' => ':count image(s) failed',
            'instructions_title' => 'Upload Tips',
            'instruction_1' => 'Use high-quality images (1920×1080 or larger recommended)',
            'instruction_2' => 'Images will be automatically resized and optimized',
            'instruction_3' => 'You can reorder images after uploading',
            'instruction_4' => 'Set featured images to highlight key photos',
        ],

        // Manage Page
        'manage' => [
            'title' => 'Manage Gallery',
            'heading' => 'Visual Gallery Manager',
            'subheading' => 'Drag to reorder, click to manage images',
            'no_hall_selected' => 'No Hall Selected',
            'select_hall_prompt' => 'Please select a hall to manage its gallery',
            'gallery_images' => 'Gallery Images',
            'drag_to_reorder' => 'Drag images to reorder',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bookings
    |--------------------------------------------------------------------------
    */
    'bookings' => [
        'title' => 'Bookings',
        'number' => 'Booking #',
        'number_copied' => 'Booking number copied!',
        'hall' => 'Hall',
        'date' => 'Event Date',
        'event_date' => 'Event Date',
        'customer' => 'Customer',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Customer Email',
        'customer_phone' => 'Customer Phone',
        'guests' => 'Guests',
        'guest' => 'Guest',
        'total' => 'Total',
        'amount' => 'Amount',
        'payment' => 'Payment',
        'payment_status' => 'Payment Status',
        'payment_method' => 'Payment Method',
        'payment_reference' => 'Payment Reference',
        'status' => 'Status',
        'slot' => 'Time Slot',
        'time_slot' => 'Time Slot',
        'source' => 'Source',
        'booked_at' => 'Booked At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'upcoming' => 'Upcoming',
        'from_date' => 'From Date',
        'until_date' => 'Until Date',
        'notes' => 'Notes',
        'special_requests' => 'Special Requests',
        'booking_date' => 'Booking Date',
        'booking_details' => 'Booking Details',
        'payment_details' => 'Payment Details',
        'customer_details' => 'Customer Details',
        'actions' => 'Actions',
        'view_title' => 'Booking: :number',
        'view_booking' => 'View Booking',
        'confirm' => 'Confirm',
        'complete' => 'Complete',
        'cancel' => 'Cancel',
        'contact' => 'Contact',
        'confirm_selected' => 'Confirm Selected',
        'cancellation_reason' => 'Cancellation Reason',
        'confirmed_success' => 'Booking Confirmed Successfully',
        'completed_success' => 'Booking Completed Successfully',
        'cancelled_success' => 'Booking Cancelled',
        'bulk_confirmed' => ':count bookings confirmed',
        'no_recent' => 'No recent bookings',
        'no_recent_description' => 'Your recent bookings will appear here',
        'empty_heading' => 'No Bookings',
        'empty_description' => 'This hall has no bookings yet.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */
    'reviews' => [
        'customer' => 'Customer',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'responded' => 'Responded',
        'approved' => 'Approved',
        'date' => 'Date',
        'needs_response' => 'Needs Response',
        'view_title' => 'Review Details',
        'rating_section' => 'Rating',
        'overall' => 'Overall Rating',
        'cleanliness' => 'Cleanliness',
        'service' => 'Service',
        'value' => 'Value for Money',
        'location' => 'Location',
        'content_section' => 'Review Content',
        'response_section' => 'Your Response',
        'no_response' => 'No response yet',
        'add_response' => 'Add Response',
        'edit_response' => 'Edit Response',
        'delete_response' => 'Delete Response',
        'your_response' => 'Your Response',
        'response_help' => 'Your response will be visible to the customer and other users.',
        'response_saved' => 'Response Saved Successfully',
        'response_deleted' => 'Response Deleted',
        'empty_heading' => 'No Reviews',
        'empty_description' => 'This hall has no reviews yet.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions (Dashboard & Widgets)
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'complete_profile' => 'Complete Profile',
        'confirm_booking' => 'Confirm Booking',
        'booking_needs_confirmation' => 'Booking #:number for :date needs confirmation',
        'payment_pending' => 'Payment Pending',
        'payment_needs_follow_up' => 'OMR :amount payment is :status (:days days)',
        'overdue' => 'overdue',
        'due_soon' => 'due soon',
        'respond_to_review' => 'Respond to Review',
        'review_needs_response' => ':rating star review from :customer needs response',
        'guest' => 'Guest',
        'respond_to_ticket' => 'Respond to Ticket',
        'ticket_needs_response' => 'Support ticket ":subject" with :priority priority',
        'complete_hall_profile' => 'Complete Hall Profile',
        'hall_missing_info' => ':name is missing: :missing',
        'featured_image' => 'featured image',
        'description' => 'description',
        'gallery' => 'gallery photos',
        'update_availability' => 'Update Availability',
        'availability_needs_update' => 'Hall availability calendar needs updating',
        'set_up_payments' => 'Set Up Payments',
        'payments_not_set_up' => 'Payment methods are not set up',
        'verify_identity' => 'Verify Identity',
        'identity_not_verified' => 'Owner identity is not verified',
        'view' => 'View',
        'confirm' => 'Confirm',
        'manage' => 'Manage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Availability Resource
    |--------------------------------------------------------------------------
    */
    'availability_resource' => [
        // Navigation
        'navigation' => 'Availability',
        'singular' => 'Availability Slot',
        'plural' => 'Availability',
        'title' => 'Manage Availability',
        'heading' => 'Availability Management',
        'subheading' => 'Manage availability slots across all your halls',

        // Sections
        'sections' => [
            'slot_info' => 'Slot Information',
            'slot_info_desc' => 'Configure the availability slot details',
            'blocking' => 'Block Configuration',
            'blocking_desc' => 'Set reason and notes for blocked slots',
            'pricing' => 'Custom Pricing',
            'pricing_desc' => 'Override default pricing for this slot',
        ],

        // Fields
        'fields' => [
            'hall' => 'Hall',
            'date' => 'Date',
            'time_slot' => 'Time Slot',
            'time_slots' => 'Time Slots',
            'is_available' => 'Available',
            'reason' => 'Block Reason',
            'notes' => 'Notes',
            'custom_price' => 'Custom Price',
            'date_mode' => 'Date Selection',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'days_to_generate' => 'Days to Generate',
        ],

        // Date modes
        'date_modes' => [
            'single' => 'Single Date',
            'range' => 'Date Range',
        ],

        // Columns
        'columns' => [
            'hall' => 'Hall',
            'date' => 'Date',
            'time_slot' => 'Time Slot',
            'status' => 'Status',
            'reason' => 'Reason',
            'price' => 'Custom Price',
            'notes' => 'Notes',
        ],

        // Filters
        'filters' => [
            'hall' => 'Filter by Hall',
            'time_slot' => 'Time Slot',
            'status' => 'Status',
            'reason' => 'Reason',
            'future_only' => 'Future Only',
            'available_only' => 'Available Only',
            'blocked_only' => 'Blocked Only',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'from' => 'From',
            'to' => 'To',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
        ],

        // Tabs
        'tabs' => [
            'all' => 'All Slots',
            'available' => 'Available',
            'blocked' => 'Blocked',
            'booked' => 'Booked',
            'maintenance' => 'Maintenance',
            'today' => 'Today',
            'this_week' => 'This Week',
        ],

        // Actions
        'actions' => [
            'create' => 'Add Slot',
            'block' => 'Block',
            'unblock' => 'Unblock',
            'calendar_view' => 'Calendar View',
            'list_view' => 'List View',
            'bulk_generate' => 'Generate Slots',
            'view_hall' => 'View Hall',
        ],

        // Bulk Actions
        'bulk' => [
            'block' => 'Block Selected',
            'unblock' => 'Unblock Selected',
            'set_price' => 'Set Custom Price',
            'clear_price' => 'Clear Custom Price',
        ],

        // Placeholders
        'placeholders' => [
            'use_hall_price' => 'Use hall default price',
            'default_price' => 'Default',
        ],

        // Helpers
        'helpers' => [
            'custom_price' => 'Leave empty to use the hall\'s default pricing',
        ],

        // Suffixes
        'suffixes' => [
            'days' => 'days',
        ],

        // Wizard Steps
        'wizard' => [
            'select_hall' => 'Select Hall',
            'select_dates' => 'Select Dates',
            'select_slots' => 'Select Slots',
            'configure' => 'Configure',
        ],

        // Empty State
        'empty' => [
            'heading' => 'No Availability Slots',
            'description' => 'Generate availability slots for your halls to start accepting bookings.',
            'action' => 'Generate Slots',
        ],

        // Create/Edit
        'create' => [
            'title' => 'Create Availability Slot',
        ],
        'edit' => [
            'title' => 'Edit :date - :slot',
        ],

        // Calendar
        'calendar' => [
            'title' => 'Availability Calendar',
            'heading' => 'Calendar View',
            'subheading' => 'Visual overview of your halls availability',
            'select_hall' => 'Select Hall',
            'all_halls' => 'All Halls (Summary View)',
        ],

        // Notifications
        'notifications' => [
            'generated' => 'Availability Generated',
            'generated_body' => ':days days of availability created for :hall',
            'updated' => 'Availability Updated',
            'updated_body' => 'The availability slot has been updated.',
            'bulk_created' => 'Slots Created',
            'bulk_created_body' => ':count availability slots have been created.',
            'select_hall_first' => 'Please select a hall first',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing Resource
    |--------------------------------------------------------------------------
    */
    'pricing' => [
        // Navigation & Labels
        'navigation' => 'Pricing Rules',
        'singular' => 'Pricing Rule',
        'plural' => 'Pricing Rules',
        'title' => 'Pricing Management',
        'heading' => 'Pricing Rules',
        'subheading' => 'Manage seasonal pricing, discounts, and special rates',

        // Sections
        'sections' => [
            'basic' => 'Basic Information',
            'basic_desc' => 'Set up the pricing rule name and type',
            'date_range' => 'Date Range',
            'date_range_desc' => 'When this pricing rule should apply',
            'adjustment' => 'Price Adjustment',
            'adjustment_desc' => 'How the price should be modified',
            'status' => 'Status & Notes',
        ],

        // Fields
        'fields' => [
            'hall' => 'Hall',
            'name_en' => 'Rule Name (English)',
            'name_ar' => 'Rule Name (Arabic)',
            'type' => 'Rule Type',
            'priority' => 'Priority',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_recurring' => 'Recurring Rule',
            'recurrence_type' => 'Recurrence Type',
            'days_of_week' => 'Days of Week',
            'adjustment_type' => 'Adjustment Type',
            'adjustment_value' => 'Adjustment Value',
            'percentage_value' => 'Percentage',
            'increase_amount' => 'Increase Amount',
            'fixed_amount' => 'Fixed Price',
            'min_price' => 'Minimum Price',
            'max_price' => 'Maximum Price',
            'apply_to_slots' => 'Apply to Time Slots',
            'is_active' => 'Active',
            'notes' => 'Notes',
            'weekend_increase' => 'Weekend Price Increase',
            'time_slot' => 'Time Slot',
            'date' => 'Date',
        ],

        // Placeholders
        'placeholders' => [
            'name_en' => 'e.g., Eid Holiday Pricing',
            'name_ar' => 'e.g., أسعار عيد الفطر',
            'no_minimum' => 'No minimum',
            'no_maximum' => 'No maximum',
        ],

        // Helpers
        'helpers' => [
            'priority' => 'Higher priority rules are applied first (0-100)',
            'is_recurring' => 'Rule will repeat based on recurrence type',
            'percentage' => 'Enter positive number for increase (e.g., 20 = +20%), negative for discount (e.g., -10 = 10% off)',
            'fixed_increase' => 'Amount to add to the base price',
            'fixed_price' => 'Override the base price with this exact amount',
            'min_price' => 'Final price will not go below this amount',
            'max_price' => 'Final price will not exceed this amount',
            'apply_to_slots' => 'Leave empty to apply to all time slots',
            'is_active' => 'Inactive rules will not affect pricing',
        ],

        // Types
        'types' => [
            'seasonal' => 'Seasonal',
            'holiday' => 'Holiday',
            'weekend' => 'Weekend',
            'special_event' => 'Special Event',
            'early_bird' => 'Early Bird',
            'last_minute' => 'Last Minute',
        ],

        // Adjustment Types
        'adjustment_types' => [
            'percentage' => 'Percentage (+/-)',
            'fixed_increase' => 'Fixed Increase (+OMR)',
            'fixed_price' => 'Fixed Price (OMR)',
        ],

        // Recurrence Types
        'recurrence' => [
            'weekly' => 'Weekly (Same days every week)',
            'yearly' => 'Yearly (Same dates every year)',
        ],

        // Days of Week
        'days' => [
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
        ],

        // Status
        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'expired' => 'Expired',
            'scheduled' => 'Scheduled',
            'recurring' => 'Recurring',
        ],

        // Columns
        'columns' => [
            'hall' => 'Hall',
            'name' => 'Rule Name',
            'type' => 'Type',
            'date_range' => 'Date Range',
            'adjustment' => 'Adjustment',
            'priority' => 'Priority',
            'status' => 'Status',
            'slots' => 'Slots',
            'all_slots' => 'All Slots',
            'every_week' => 'Every Week',
            'every_year' => 'Every Year',
        ],

        // Filters
        'filters' => [
            'hall' => 'Filter by Hall',
            'type' => 'Rule Type',
            'status' => 'Status',
            'recurring' => 'Recurrence',
            'active_only' => 'Active Only',
            'inactive_only' => 'Inactive Only',
            'recurring_only' => 'Recurring Only',
            'one_time_only' => 'One-time Only',
        ],

        // Tabs
        'tabs' => [
            'all' => 'All Rules',
            'active' => 'Active',
            'seasonal' => 'Seasonal',
            'weekend' => 'Weekend',
            'holiday' => 'Holiday',
            'expired' => 'Expired',
        ],

        // Actions
        'actions' => [
            'create' => 'Add Pricing Rule',
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
            'duplicate' => 'Duplicate',
            'calculator' => 'Price Calculator',
            'quick_weekend' => 'Quick Weekend Pricing',
            'back_to_list' => 'Back to List',
        ],

        // Bulk Actions
        'bulk' => [
            'activate' => 'Activate Selected',
            'deactivate' => 'Deactivate Selected',
        ],

        // Notifications
        'notifications' => [
            'created' => 'Pricing Rule Created',
            'created_body' => 'The pricing rule has been created successfully.',
            'updated' => 'Pricing Rule Updated',
            'updated_body' => 'The pricing rule has been updated successfully.',
            'activated' => 'Pricing Rule Activated',
            'deactivated' => 'Pricing Rule Deactivated',
            'duplicated' => 'Pricing Rule Duplicated',
            'deleted' => 'Pricing Rule Deleted',
            'weekend_created' => 'Weekend Pricing Rule Created',
            'bulk_activated' => ':count rules activated',
            'bulk_deactivated' => ':count rules deactivated',
        ],

        // Empty State
        'empty' => [
            'heading' => 'No Pricing Rules',
            'description' => 'Create pricing rules to set special rates for holidays, weekends, and seasonal periods.',
            'action' => 'Create First Rule',
        ],

        // Create/Edit
        'create' => [
            'title' => 'Create Pricing Rule',
        ],
        'edit' => [
            'title' => 'Edit: :name',
        ],

        // Calculator
        'calculator' => [
            'title' => 'Price Calculator',
            'heading' => 'Price Calculator',
            'subheading' => 'Preview how pricing rules affect the final price',
            'select_options' => 'Select Options',
            'select_hall' => 'Select a hall...',
            'breakdown' => 'Price Breakdown',
            'slot_comparison' => 'Compare All Slots',
            'week_preview' => '7-Day Preview',
            'base_price' => 'Base Price',
            'slot_price' => 'Slot Price',
            'custom_date_price' => 'Custom Date Price',
            'rules_applied' => 'Applied Rules',
            'no_rules_applied' => 'No pricing rules apply to this selection',
            'final_price' => 'Final Price',
            'from_base' => 'from base price',
            'weekend' => 'Weekend',
            'custom' => 'Custom',
            'has_rules' => 'Has pricing rules',
            'rules' => 'rule|rules',
            'empty_title' => 'Select Options to Calculate',
            'empty_description' => 'Choose a hall, date, and time slot to see the price breakdown.',
        ],
    ],
];
