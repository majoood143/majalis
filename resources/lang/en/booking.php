<?php

declare(strict_types=1);

/**
 * English Translation File for Booking Resource
 *
 * This file contains all translation strings for the BookingResource
 * in the Majalis Hall Booking Management System.
 *
 * @package    Majalis
 * @subpackage Translations
 * @author     Majalis Development Team
 * @version    1.0.0
 * @since      2025-01-01
 *
 * Usage:
 * - In Filament Resource: __('booking.key.subkey')
 * - In Blade: {{ __('booking.key.subkey') }}
 * - With parameters: __('booking.notifications.slot_booked', ['number' => $bookingNumber])
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Resource Labels
    |--------------------------------------------------------------------------
    |
    | Labels for resource navigation and general identification.
    |
    */
    'resource' => [
        'label' => 'Booking',
        'plural_label' => 'Bookings',
        'navigation_label' => 'Bookings',
        'navigation_group' => 'Booking Management',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Sections
    |--------------------------------------------------------------------------
    |
    | Section titles and descriptions for the booking form.
    |
    */
    'sections' => [
        'hall_selection' => [
            'title' => 'Hall Selection',
            'description' => 'Filter and select the hall for booking',
        ],
        'booking_details' => [
            'title' => 'Booking Details',
            'description' => 'Enter the booking information',
        ],
        'customer_details' => [
            'title' => 'Customer Details',
            'description' => 'Customer contact information',
        ],
        'extra_services' => [
            'title' => 'Extra Services',
            'description' => 'Select additional services for this booking',
        ],
        'pricing' => [
            'title' => 'Pricing Summary',
            'description' => 'Booking cost breakdown',
        ],
        'pricing_breakdown' => [
            'title' => 'Pricing Breakdown',
            'description' => 'Detailed cost analysis',
        ],
        'status_payment' => [
            'title' => 'Status & Payment',
            'description' => 'Booking and payment status',
        ],
        'timestamps' => [
            'title' => 'Timestamps',
            'description' => 'Record audit trail',
        ],
        'cancellation_details' => [
            'title' => 'Cancellation Details',
            'description' => 'Information about booking cancellation',
        ],
        'admin_notes' => [
            'title' => 'Admin Notes',
            'description' => 'Internal notes for administrators',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    |
    | Labels, placeholders, and helper texts for form fields.
    |
    */
    'fields' => [
        // Hall Selection
        'region_id' => [
            'label' => 'Region',
            'placeholder' => 'Select a region',
            'helper' => 'Select a region to filter cities',
        ],
        'city_id' => [
            'label' => 'City',
            'placeholder' => 'Select a city',
            'helper' => 'Select a city to filter halls',
        ],
        'hall_id' => [
            'label' => 'Hall',
            'placeholder' => 'Select a hall',
            'helper' => 'Select a hall to proceed',
        ],

        // Booking Details
        'booking_number' => [
            'label' => 'Booking Number',
            'helper' => 'Auto-generated unique identifier',
        ],
        'user_id' => [
            'label' => 'Customer',
            'placeholder' => 'Select customer',
            'helper' => 'Select the customer making this booking',
        ],
        'booking_date' => [
            'label' => 'Event Date',
            'placeholder' => 'Select date',
            'helper' => 'Select date to see available time slots',
            'helper_select_hall' => 'Select a hall first',
        ],
        'time_slot' => [
            'label' => 'Time Slot',
            'placeholder' => 'Select time slot',
            'helper' => 'Select hall and date first',
            'helper_available' => ':count slot(s) available',
            'helper_all_booked' => 'All slots are booked for this date',
        ],
        'number_of_guests' => [
            'label' => 'Number of Guests',
            'placeholder' => 'Enter number of guests',
            'helper' => 'Capacity: :min - :max guests',
            'helper_select_hall' => 'Select a hall first',
        ],
        'event_type' => [
            'label' => 'Event Type',
            'placeholder' => 'Select event type',
            'helper' => 'Type of event being held',
        ],

        // Customer Details
        'customer_name' => [
            'label' => 'Customer Name',
            'placeholder' => 'Enter customer name',
        ],
        'customer_email' => [
            'label' => 'Customer Email',
            'placeholder' => 'Enter email address',
        ],
        'customer_phone' => [
            'label' => 'Customer Phone',
            'placeholder' => 'Enter phone number',
        ],
        'customer_notes' => [
            'label' => 'Customer Notes',
            'placeholder' => 'Enter any special requests or notes',
        ],

        // Extra Services
        'service_id' => [
            'label' => 'Service',
            'placeholder' => 'Select a service',
        ],
        'service_name' => [
            'label' => 'Service Name',
        ],
        'unit_price' => [
            'label' => 'Unit Price',
        ],
        'quantity' => [
            'label' => 'Quantity',
        ],
        'total_price' => [
            'label' => 'Total',
        ],

        // Pricing
        'hall_price' => [
            'label' => 'Hall Price',
            'helper' => 'Select hall, date, and time slot to see pricing',
            'helper_custom' => 'Custom price for this date/slot',
            'helper_default' => 'Default hall price for :slot',
        ],
        'services_price' => [
            'label' => 'Services Price',
        ],
        'subtotal' => [
            'label' => 'Subtotal',
        ],
        'commission_rate' => [
            'label' => 'Commission Rate',
        ],
        'commission_amount' => [
            'label' => 'Commission Amount',
        ],
        'platform_fee' => [
            'label' => 'Platform Fee',
        ],
        'total_amount' => [
            'label' => 'Total Amount',
        ],
        'owner_payout' => [
            'label' => 'Owner Payout',
        ],

        // Status & Payment
        'status' => [
            'label' => 'Booking Status',
            'placeholder' => 'Select status',
        ],
        'payment_status' => [
            'label' => 'Payment Status',
            'placeholder' => 'Select payment status',
        ],
        'payment_method' => [
            'label' => 'Payment Method',
            'placeholder' => 'Select payment method',
        ],
        'payment_reference' => [
            'label' => 'Payment Reference',
            'placeholder' => 'Enter payment reference',
        ],

        // Cancellation
        'cancellation_reason' => [
            'label' => 'Cancellation Reason',
            'placeholder' => 'Enter reason for cancellation',
        ],
        'refund_amount' => [
            'label' => 'Refund Amount',
        ],

        // Admin
        'admin_notes' => [
            'label' => 'Admin Notes',
            'placeholder' => 'Enter internal notes',
        ],

        // Timestamps
        'created_at' => [
            'label' => 'Created At',
        ],
        'confirmed_at' => [
            'label' => 'Confirmed At',
        ],
        'completed_at' => [
            'label' => 'Completed At',
        ],
        'cancelled_at' => [
            'label' => 'Cancelled At',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    |
    | Labels for table columns in the list view.
    |
    */
    'table' => [
        'columns' => [
            'booking_number' => 'Booking #',
            'hall' => 'Hall',
            'customer_name' => 'Customer',
            'booking_date' => 'Event Date',
            'time_slot' => 'Time Slot',
            'number_of_guests' => 'Guests',
            'total_amount' => 'Total',
            'status' => 'Status',
            'payment_status' => 'Payment',
            'created_at' => 'Created',
        ],
        'filters' => [
            'status' => 'Filter by Status',
            'payment_status' => 'Filter by Payment',
            'date_range' => 'Date Range',
            'hall' => 'Filter by Hall',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    |
    | Labels for list view tabs.
    |
    */
    'tabs' => [
        'all' => 'All',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'today' => 'Today',
        'upcoming' => 'Upcoming',
        'past' => 'Past',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuses
    |--------------------------------------------------------------------------
    |
    | Booking and payment status labels.
    |
    */
    'statuses' => [
        'booking' => [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
        ],
        'payment' => [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'partial' => 'Partial',
            'refunded' => 'Refunded',
            'failed' => 'Failed',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Slots
    |--------------------------------------------------------------------------
    |
    | Labels for available time slots.
    |
    */
    'time_slots' => [
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'full_day' => 'Full Day',
        'morning_afternoon' => 'Morning & Afternoon',
        'afternoon_evening' => 'Afternoon & Evening',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Types
    |--------------------------------------------------------------------------
    |
    | Labels for event types.
    |
    */
    'event_types' => [
        'wedding' => 'Wedding',
        'engagement' => 'Engagement',
        'birthday' => 'Birthday',
        'corporate' => 'Corporate Event',
        'conference' => 'Conference',
        'seminar' => 'Seminar',
        'workshop' => 'Workshop',
        'exhibition' => 'Exhibition',
        'graduation' => 'Graduation',
        'anniversary' => 'Anniversary',
        'memorial' => 'Memorial',
        'religious' => 'Religious Event',
        'social' => 'Social Gathering',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Labels for payment methods.
    |
    */
    'payment_methods' => [
        'thawani' => 'Thawani',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'card' => 'Credit/Debit Card',
        'cheque' => 'Cheque',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | Labels for action buttons and their confirmations.
    |
    */
    'actions' => [
        'create' => 'Create Booking',
        'edit' => 'Edit Booking',
        'view' => 'View Booking',
        'delete' => 'Delete Booking',
        'confirm' => [
            'label' => 'Confirm',
            'modal_heading' => 'Confirm Booking',
            'modal_description' => 'Are you sure you want to confirm this booking?',
            'modal_submit' => 'Yes, Confirm',
        ],
        'cancel' => [
            'label' => 'Cancel',
            'modal_heading' => 'Cancel Booking',
            'modal_description' => 'Are you sure you want to cancel this booking?',
            'modal_submit' => 'Yes, Cancel',
        ],
        'complete' => [
            'label' => 'Complete',
            'modal_heading' => 'Complete Booking',
            'modal_description' => 'Are you sure you want to mark this booking as completed?',
            'modal_submit' => 'Yes, Complete',
        ],
        'generate_invoice' => [
            'label' => 'Generate Invoice',
            'modal_heading' => 'Generate Invoice',
            'modal_description' => 'This will generate a PDF invoice for this booking.',
            'modal_submit' => 'Generate',
        ],
        'download_invoice' => [
            'label' => 'Download Invoice',
        ],
        'send_reminder' => [
            'label' => 'Send Reminder',
            'modal_heading' => 'Send Reminder',
            'modal_description' => 'Send a reminder notification to the customer?',
            'modal_submit' => 'Send',
        ],
        'create_confirm' => [
            'modal_heading' => 'Confirm Booking Creation',
            'modal_description' => 'Are you sure you want to create this booking? Please verify all details are correct.',
            'modal_submit' => 'Yes, Create Booking',
        ],
        'add_service' => 'Add Service',
        'remove_service' => 'Remove Service',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Notification titles and messages.
    |
    */
    'notifications' => [
        'created' => [
            'title' => 'Booking Created',
            'body' => 'Booking has been created successfully.',
        ],
        'updated' => [
            'title' => 'Booking Updated',
            'body' => 'Booking has been updated successfully.',
        ],
        'confirmed' => [
            'title' => 'Booking Confirmed',
            'body' => 'Booking has been confirmed successfully.',
        ],
        'cancelled' => [
            'title' => 'Booking Cancelled',
            'body' => 'Booking has been cancelled successfully.',
        ],
        'completed' => [
            'title' => 'Booking Completed',
            'body' => 'Booking has been marked as completed.',
        ],
        'slot_already_booked' => [
            'title' => 'Slot Already Booked',
            'body' => 'This time slot is already booked (Booking #:number). Please select a different date or time slot.',
        ],
        'slot_just_booked' => [
            'title' => 'Slot Already Booked',
            'body' => 'This time slot was just booked by another user. Please select a different time slot.',
        ],
        'no_available_slots' => [
            'title' => 'No Available Slots',
            'body' => 'All time slots are booked for this date. Please select another date.',
        ],
        'guests_below_minimum' => [
            'title' => 'Guest Count Below Minimum',
            'body' => 'Minimum capacity is :min guests. Guest count has been adjusted.',
        ],
        'guests_exceeds_maximum' => [
            'title' => 'Guest Count Exceeds Maximum',
            'body' => 'Maximum capacity is :max guests.',
        ],
        'invoice_generated' => [
            'title' => 'Invoice Generated',
            'body' => 'Invoice saved as: :filename',
        ],
        'invoice_error' => [
            'title' => 'Invoice Generation Failed',
            'body' => 'Failed to generate invoice: :error',
        ],
        'invoice_not_available' => [
            'title' => 'Invoice Not Available',
            'body' => 'Invoice has not been generated yet.',
        ],
        'reminder_sent' => [
            'title' => 'Reminder Sent',
            'body' => 'Reminder notification has been sent to the customer.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Texts & Messages
    |--------------------------------------------------------------------------
    |
    | General helper texts and informational messages.
    |
    */
    'messages' => [
        'select_hall_first' => 'Select a hall first',
        'select_date_first' => 'Select a date first',
        'select_time_slot' => 'Select a time slot',
        'no_services_available' => 'No extra services available for this hall',
        'no_notes_provided' => 'No notes provided',
        'no_admin_notes' => 'No admin notes',
        'fully_booked' => 'Fully booked',
        'slots_available' => ':count slot(s) available',
        'custom_price_applied' => 'Custom price for this date/slot',
        'default_price' => 'Default hall price for :slot',
    ],

    /*
    |--------------------------------------------------------------------------
    | Infolist Labels
    |--------------------------------------------------------------------------
    |
    | Labels specific to the view/infolist page.
    |
    */
    'infolist' => [
        'booking_info' => 'Booking Information',
        'event_details' => 'Event Details',
        'hall_info' => 'Hall Information',
        'financial_summary' => 'Financial Summary',
        'service_details' => 'Service Details',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    |
    | Custom validation error messages.
    |
    */
    'validation' => [
        'hall_required' => 'Please select a hall.',
        'date_required' => 'Please select a booking date.',
        'time_slot_required' => 'Please select a time slot.',
        'customer_required' => 'Please select a customer.',
        'guests_required' => 'Please enter the number of guests.',
        'guests_min' => 'Number of guests must be at least :min.',
        'guests_max' => 'Number of guests cannot exceed :max.',
        'slot_not_available' => 'The selected time slot is not available.',
        'date_past' => 'Booking date cannot be in the past.',
    ],
];
