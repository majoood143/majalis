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
];
