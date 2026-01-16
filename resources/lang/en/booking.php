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

        'booking_information' => 'Booking Information',
        'hall_date_information' => 'Hall & Date Information',
        'customer_details' => 'Customer Details',
        'pricing_breakdown' => 'Pricing Breakdown',
        'advance_payment_details' => 'Advance Payment Details',
        'payment_type_helper' => 'Set by hall configuration ',
        'extra_services' => 'Extra Services',
        'timestamps' => 'Timestamps',
        'cancellation_details' => 'Cancellation Details',
        'admin_notes' => 'Admin Notes',
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
            'select_hall_first' => 'Select hall, date, and time slot to see pricing',
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

        'edit' => 'Edit',
        'confirm' => 'Confirm Booking',
        'confirm_modal_heading' => 'Confirm Booking',
        'confirm_modal_description' => 'Are you sure you want to confirm this booking? This will notify the customer.',
        'cancel' => 'Cancel Booking',
        'cancel_modal_heading' => 'Cancel Booking',
        'cancel_modal_description' => 'Are you sure you want to cancel this booking? This action cannot be undone.',
        'complete' => 'Complete Booking',
        'complete_modal_heading' => 'Complete Booking',
        'complete_modal_description' => 'Mark this booking as completed? This will finalize the booking.',
        'download_invoice' => 'Download Invoice',
        'generate_invoice' => 'Generate Invoice',
        'send_reminder' => 'Send Reminder',
        'send_reminder_modal_heading' => 'Send Reminder',
        'send_reminder_modal_description' => 'Send a reminder notification to the customer about their upcoming booking?',
        'mark_balance_paid' => 'Mark Balance as Paid',
        'create_modal_heading' => 'Confirm Booking Creation',
        'create_modal_description' => 'Are you sure you want to create this booking? Please verify all details are correct.',
        'create_modal_submit_label' => 'Yes, Create Booking',
        'download_invoice' => 'Download Invoice',
        'print_invoice' => 'Print Invoice',
        'send_invoice_email' => 'Email Invoice',
        'send_email' => 'Send Email',
        'send_reminder' => 'Send Reminder',
        'contact_customer' => 'WhatsApp Customer',
        'add_note' => 'Add Note',
        'duplicate' => 'Duplicate Booking',
        'request_review' => 'Request Review',
        'view_new_booking' => 'View New Booking',
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
        'slot_already_booked_title' => 'Slot Already Booked',
        'slot_already_booked_body' => 'This time slot is already booked (Booking #:booking_number). Please select a different date or time slot.',
        'guest_count_below_min_title' => 'Guest Count Below Minimum',
        'guest_count_below_min_body' => 'Minimum capacity is :capacity_min guests. Guest count has been adjusted.',
        'guest_count_exceeds_max_title' => 'Guest Count Exceeds Maximum',
        'guest_count_exceeds_max_body' => 'Maximum capacity is :capacity_max guests.',
        'slot_just_booked_title' => 'Slot Already Booked',
        'slot_just_booked_body' => 'This time slot was just booked by another user. Please select a different time slot.',
        'advance_payment_booking_title' => 'Advance Payment Booking',
        'advance_payment_booking_body' => 'This booking requires advance payment. Customer must pay :advance_amount OMR upfront. Balance of :balance_due OMR due before event.',
        'booking_created_title' => 'Booking created successfully',
        'booking_summary_title' => '📋 Booking Summary',
        'booking_summary_body' => "**Booking:** :booking_number\n**Total Amount:** :total_amount OMR\n**Payment Type:** Advance Payment\n**Advance Required:** :advance_amount OMR\n**Balance Due:** :balance_due OMR\n\nCustomer must pay advance amount before event confirmation.",
        'invoice_email_queued' => 'Invoice Email Queued',
        'invoice_email_queued_body' => 'The invoice will be sent to :email shortly.',
        'email_failed' => 'Email Failed',
        'reminder_sent' => 'Reminder Sent',
        'note_saved' => 'Note Saved',
        'booking_duplicated' => 'Booking Duplicated',
        'booking_duplicated_body' => 'New booking :number created successfully.',
        'review_request_sent' => 'Review Request Sent',
        'booking_confirmed_title' => 'Booking confirmed successfully',
        'booking_cancelled_title' => 'Booking cancelled successfully',
        'booking_completed_title' => 'Booking completed successfully',
        'invoice_not_available_title' => 'Invoice not available',
        'invoice_generated_title' => 'Invoice generated successfully',
        'invoice_generated_body' => 'Invoice saved as: :filename',
        'invoice_generation_failed_title' => 'Invoice generation failed',
        'reminder_sent_title' => 'Reminder sent successfully',
        'balance_marked_paid_title' => 'Balance marked as paid',
        'balance_marked_paid_body' => 'Balance payment has been recorded successfully.',
        'email_sent_title' => 'Email sent successfully',
        'email_sent_body' => 'The email has been sent to the customer.',
        'email_failed_title' => 'Email sending failed',
        'booking_completed' => 'Booking has been marked as completed.',
        'booking_cancelled' => 'Booking has been cancelled successfully.',
        'booking_confirmed' => 'Booking has been confirmed successfully.',
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
        'event_details_placeholder' => 'Describe your event...',
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



    // Form
    'form' => [
        'cancellation_reason' => 'Cancellation Reason',
        'cancellation_reason_placeholder' => 'Enter the reason for cancellation...',
        'balance_payment_method' => 'Payment Method',
        'balance_payment_reference' => 'Payment Reference',
        'balance_payment_reference_placeholder' => 'Transaction ID or Receipt Number',
        'payment_date' => 'Payment Date',
        'recipient_email' => 'Recipient Email',
        'email_subject' => 'Subject',
        'email_message' => 'Message',
        'email_message_helper' => 'This message will appear in the email body',
        'attach_pdf' => 'Attach Invoice PDF',
        'notification_channels' => 'Send Via',
        'channel_email' => 'Email',
        'channel_sms' => 'SMS',
        'custom_message' => 'Custom Message (Optional)',
        'custom_message_placeholder' => 'Add a personalized message...',
        'new_booking_date' => 'New Booking Date',
        'admin_notes' => 'Admin Notes',
        'admin_notes_placeholder' => 'Internal notes about this booking...',
    ],


    // Labels
    'labels' => [
        'booking_number' => 'Booking Number',
        'status' => 'Status',
        'payment_status' => 'Payment Status',
        'hall' => 'Hall',
        'location' => 'Location',
        'booking_date' => 'Booking Date',
        'time_slot' => 'Time Slot',
        'number_of_guests' => 'Number of Guests',
        'guests_suffix' => ' guests',
        'event_type' => 'Event Type',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Customer Email',
        'customer_phone' => 'Customer Phone',
        'customer_notes' => 'Customer Notes',
        'hall_price' => 'Hall Price',
        'services_price' => 'Services Price',
        'subtotal' => 'Subtotal',
        'commission_amount' => 'Platform Fee',
        'total_amount' => 'Total Amount',
        'owner_payout' => 'Owner Payout',
        'payment_type' => 'Payment Type',
        'advance_amount' => 'Advance Amount',
        'balance_due' => 'Balance Due',
        'balance_payment_status' => 'Balance Payment Status',
        'balance_paid_at' => 'Balance Paid On',
        'balance_payment_method' => 'Payment Method',
        'balance_payment_reference' => 'Payment Reference',
        'service_name' => 'Service',
        'unit_price' => 'Unit Price',
        'quantity' => 'Quantity',
        'total_price' => 'Total',
        'created_at' => 'Created At',
        'confirmed_at' => 'Confirmed At',
        'completed_at' => 'Completed At',
        'cancelled_at' => 'Cancelled At',
        'cancellation_reason' => 'Cancellation Reason',
        'refund_amount' => 'Refund Amount',
        'admin_notes' => 'Admin Notes',
    ],

    // Placeholders
    'placeholders' => [
        'no_notes' => 'No notes provided',
        'balance_not_paid' => 'Balance not paid yet',
        'no_admin_notes' => 'No admin notes',
    ],

    // Descriptions
    'descriptions' => [
        'advance_payment_pending' => '⚠️ This booking requires advance payment. Customer must pay remaining balance before the event.',
        'advance_payment_paid' => '✅ This booking required advance payment. Balance has been paid.',
        'full_payment' => 'This is a full payment booking. Customer pays the entire amount.',
    ],

    //Statuses
    'statuses' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'balance_paid' => 'Balance Paid',
        'balance_pending' => 'Balance Pending',
        'no_show' => 'No Show',
        'partial' => 'Partial',
        'refunded' => 'Refunded',
        'failed' => 'Failed',
        'paid' => 'Paid',
    ],

    //Payment Statuses
    'payment_statuses' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'partially_paid' => 'Partially Paid',
    ],

    // Payment Types
    'payment_types' => [
        'full' => 'Full Payment',
        'advance' => 'Advance Payment',
    ],

    // Time Slots
    'time_slots' => [
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'night' => 'Night',
        'full_day' => 'Full Day',
    ],

    // Event Types
    'event_types' => [
        'wedding' => 'Wedding',
        'birthday' => 'Birthday',
        'corporate' => 'Corporate',
        'graduation' => 'Graduation',
        'engagement' => 'Engagement',
        'other' => 'Other',
    ],

    // Payment Methods
    'payment_methods' => [
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'card' => 'Card',
    ],

    // Messages
    'messages' => [
        'reference_copied' => 'Reference copied!',
    ],

    'exceptions' => [
        'slot_already_booked' => 'Slot already booked',
    ],

    'modals' => [
        'send_invoice_email' => 'Send Invoice via Email',
        'send_reminder' => 'Send Booking Reminder',
        'admin_notes' => 'Admin Notes',
        'duplicate_booking' => 'Duplicate Booking',
        'duplicate_booking_description' => 'Create a new booking with the same details for a different date.',
        'request_review' => 'Request Customer Review',
        'request_review_description' => 'Send a review request email to the customer.',
        'complete_booking_description' => 'Mark this booking as completed. This action cannot be undone.',
        'confirm_booking_description' => 'Confirm this booking and notify the customer.',
        'cancel_booking_description' => 'Cancel this booking. This action cannot be undone.',
        'mark_balance_paid_description' => 'Mark the balance as paid for this booking.',
        'complete_booking' => 'Complete Booking',
        'confirm_booking' => 'Confirm Booking',
        'cancel_booking' => 'Cancel Booking',
        'mark_balance_paid' => 'Mark Balance as Paid',
    ],
    'email' => [
        'invoice_subject' => 'Invoice for Booking #:number',
        'invoice_default_message' => 'Please find attached the invoice for your booking. Thank you for choosing us!',
        // =========================================================
        // INVOICE EMAIL
        // =========================================================
        'invoice_subject' => 'Invoice for Booking #:number',
        'invoice_default_message' => 'Please find attached the invoice for your booking. Thank you for choosing us!',

        // =========================================================
        // REVIEW REQUEST EMAIL
        // =========================================================
        'review_request_subject' => 'How was your experience at :hall?',
        'review_greeting' => 'Hello :name,',
        'review_intro' => 'We hope you had a wonderful time at :hall on :date.',
        'review_message' => 'We would love to hear about your experience! Your feedback helps us improve our service and helps other customers make informed decisions.',
        'review_button' => 'Leave a Review',
        'booking_details' => 'Booking Details',
        'booking_number' => 'Booking Number',
        'hall' => 'Hall',
        'event_date' => 'Event Date',
        'time_slot' => 'Time Slot',
        'review_importance' => 'Your honest review, whether positive or constructive, is valuable to us. It only takes a minute and makes a big difference!',
        'review_thanks' => 'Thank you for taking the time to share your experience with us.',
        'regards' => 'Best regards',
        'review_footer_note' => 'If you have any concerns about your booking that you would prefer to discuss privately, please reply to this email and we will be happy to assist you.',

        // =========================================================
        // BOOKING REMINDER EMAIL
        // =========================================================
        'reminder_subject' => 'Reminder: Your booking at :hall is in :days days',
        'reminder_greeting' => 'Hello :name,',
        'reminder_today' => 'Your booking is TODAY!',
        'reminder_tomorrow' => 'Your booking is TOMORROW!',
        'reminder_days' => 'Your booking is in :days days',
        'your_booking_details' => 'Your Booking Details',
        'location' => 'Location',
        'guests' => 'Guests',
        'persons' => 'persons',

        // Balance reminder
        'balance_reminder_title' => 'Payment Reminder',
        'balance_reminder_message' => 'You have an outstanding balance of :amount OMR that needs to be paid before your event.',
        'pay_balance_button' => 'Pay Balance Now',

        // Preparation tips
        'preparation_tips_title' => 'Preparation Tips',
        'tip_arrive_early' => 'Please arrive 30 minutes before your scheduled time for setup.',
        'tip_contact_hall' => 'Contact the hall directly if you have any special requirements.',
        'tip_bring_confirmation' => 'Bring your booking confirmation (this email) for reference.',

        // Closing
        'questions_contact' => 'If you have any questions, please do not hesitate to contact us.',
        'we_look_forward' => 'We look forward to hosting your event!',

        // =========================================================
        // BOOKING CONFIRMATION EMAIL
        // =========================================================
        'confirmation_subject' => 'Booking Confirmed - :hall on :date',
        'confirmation_greeting' => 'Great news, :name!',
        'confirmation_intro' => 'Your booking has been confirmed. Here are your booking details:',
        'confirmation_footer' => 'Please keep this email for your records.',

        // =========================================================
        // BOOKING CANCELLATION EMAIL
        // =========================================================
        'cancellation_subject' => 'Booking Cancelled - #:number',
        'cancellation_greeting' => 'Hello :name,',
        'cancellation_intro' => 'We are sorry to inform you that your booking has been cancelled.',
        'cancellation_reason' => 'Reason',
        'cancellation_refund' => 'Refund Amount',
        'cancellation_refund_note' => 'If applicable, your refund will be processed within 5-7 business days.',

        // =========================================================
        // GENERAL
        // =========================================================
        'view_booking_button' => 'View Booking',
        'contact_support' => 'Contact Support',
        'footer_address' => 'Muscat, Oman',
        'unsubscribe_note' => 'You are receiving this email because you have a booking with us.',
    ],

    // =========================================================
    // SMS MESSAGES
    // =========================================================
    'sms' => [
        'reminder_default' => 'Hi :name! Reminder: Your booking at :hall is on :date (:time). Booking #:booking_number. See you soon! - Majalis',
        'confirmation' => 'Booking confirmed! :hall on :date. Booking #:booking_number. Thank you! - Majalis',
        'cancellation' => 'Your booking #:booking_number has been cancelled. Contact us for questions. - Majalis',
    ],

    'whatsapp' => [
        'greeting' => 'Hello :name! This is regarding your booking #:booking with Majalis.',
        'reminder' => 'Hi :name! Just a reminder about your upcoming booking at :hall on :date. Looking forward to seeing you!',
    ],

    'invoice' => [
        // Header
        'title' => 'Invoice',
        'number' => 'Invoice No',
        'date' => 'Date',
        'print' => 'Print',
        'close' => 'Close',

        // Company
        'company_tagline' => 'Premium Hall Booking Platform',
        'phone' => 'Phone',
        'email' => 'Email',

        // Bill To Section
        'bill_to' => 'Bill To',
        'customer_name' => 'Name',
        'account' => 'Account',

        // Booking Details
        'booking_details' => 'Booking Details',
        'booking_date' => 'Event Date',
        'time_slot' => 'Time Slot',
        'guests' => 'Guests',
        'persons' => 'persons',
        'event_type' => 'Event Type',
        'capacity' => 'Capacity',

        // Table Headers
        'description' => 'Description',
        'quantity' => 'Qty',
        'unit_price' => 'Unit Price',
        'total' => 'Total',

        // Line Items
        'hall_rental' => 'Hall Rental',

        // Totals
        'hall_price' => 'Hall Price',
        'services_total' => 'Services Total',
        'subtotal' => 'Subtotal',
        'platform_fee' => 'Platform Fee',
        'grand_total' => 'Grand Total',
        'currency' => 'OMR',

        // Advance Payment
        'advance_payment_details' => 'Advance Payment Details',
        'advance_paid' => 'Advance Paid',
        'balance_due' => 'Balance Due',
        'balance_status' => 'Status',
        'paid_on' => 'Paid on',
        'pending_payment' => 'Pending Payment',

        // Footer
        'customer_notes' => 'Customer Notes',
        'terms_title' => 'Terms & Conditions',
        'terms_1' => 'Payment is due upon confirmation of booking.',
        'terms_2' => 'Cancellation policy applies as per hall terms.',
        'terms_3' => 'Please arrive 30 minutes before your event.',
        'thank_you' => 'Thank you for choosing us!',
    ],
];
