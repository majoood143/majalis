<?php

declare(strict_types=1);

/**
 * English Translations for Expenses Module
 * 
 * @package Resources\Lang\En
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Expense Types
    |--------------------------------------------------------------------------
    */
    'types' => [
        'booking' => 'Booking Expense',
        'operational' => 'Operational',
        'recurring' => 'Recurring',
        'one_time' => 'One-Time',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Statuses
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Statuses
    |--------------------------------------------------------------------------
    */
    'payment_statuses' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'cancelled' => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */
    'payment_methods' => [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'card' => 'Card',
        'cheque' => 'Cheque',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recurring Frequencies
    |--------------------------------------------------------------------------
    */
    'frequencies' => [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Labels
    |--------------------------------------------------------------------------
    */
    'resource' => [
        'label' => 'Expense',
        'plural_label' => 'Expenses',
        'navigation_group' => 'Financial Management',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Labels
    |--------------------------------------------------------------------------
    */
    'form' => [
        'expense_number' => 'Expense Number',
        'expense_type' => 'Expense Type',
        'hall' => 'Hall',
        'booking' => 'Booking',
        'category' => 'Category',
        'title' => 'Title',
        'title_ar' => 'Title (Arabic)',
        'title_en' => 'Title (English)',
        'description' => 'Description',
        'description_ar' => 'Description (Arabic)',
        'description_en' => 'Description (English)',
        'amount' => 'Amount',
        'tax_amount' => 'Tax',
        'total' => 'Total',
        'expense_date' => 'Expense Date',
        'due_date' => 'Due Date',
        'payment_method' => 'Payment Method',
        'payment_status' => 'Payment Status',
        'payment_reference' => 'Payment Reference',
        'vendor_name' => 'Vendor Name',
        'vendor_phone' => 'Vendor Phone',
        'vendor_email' => 'Vendor Email',
        'is_recurring' => 'Recurring Expense',
        'recurring_frequency' => 'Frequency',
        'recurring_start_date' => 'Start Date',
        'recurring_end_date' => 'End Date',
        'attachments' => 'Attachments',
        'notes' => 'Notes',
        'status' => 'Status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Labels
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'expense_information' => 'Expense Information',
        'description' => 'Description',
        'financial_details' => 'Financial Details',
        'vendor_information' => 'Vendor Information',
        'recurring_settings' => 'Recurring Settings',
        'attachments' => 'Attachments',
        'notes' => 'Notes',
        'booking_profitability' => 'Booking Profitability',
        'audit_information' => 'Audit Information',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */
    'tabs' => [
        'all' => 'All',
        'booking' => 'Booking Expenses',
        'operational' => 'Operational',
        'recurring' => 'Recurring',
        'pending' => 'Pending Payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stats Labels
    |--------------------------------------------------------------------------
    */
    'stats' => [
        'this_month' => 'This Month',
        'pending_payments' => 'Pending Payments',
        'year_to_date' => 'Year to Date',
        'booking_expenses' => 'Booking Expenses',
        'increase_from_last_month' => '%s%% increase from last month',
        'decrease_from_last_month' => '%s%% decrease from last month',
        'expenses_pending' => '%d expenses pending',
        'linked_to_bookings' => 'Linked to bookings this month',
    ],

    /*
    |--------------------------------------------------------------------------
    | Profitability Labels
    |--------------------------------------------------------------------------
    */
    'profitability' => [
        'revenue' => 'Revenue',
        'expenses' => 'Expenses',
        'profit' => 'Profit',
        'margin' => 'Profit Margin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'created_successfully' => 'Expense created successfully',
        'updated_successfully' => 'Expense updated successfully',
        'deleted_successfully' => 'Expense deleted successfully',
        'marked_as_paid' => 'Expense marked as paid',
        'select_hall_hint' => 'Select the hall related to this expense (optional)',
        'link_to_booking_hint' => 'Link this expense to a specific booking',
        'attachments_hint' => 'Upload images or PDF files (max 5MB each)',
        'indefinite_recurring' => 'Leave empty for indefinite recurring',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'add_expense' => 'Add Expense',
        'mark_paid' => 'Mark Paid',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Text
    |--------------------------------------------------------------------------
    */
    'helper' => [
        'booking_expense_desc' => 'Expenses directly linked to a specific booking (catering, decoration, cleaning)',
        'operational_expense_desc' => 'Day-to-day operational costs for running the hall',
        'recurring_expense_desc' => 'Regularly occurring expenses (rent, utilities, subscriptions)',
        'one_time_expense_desc' => 'One-off expenses (equipment purchase, major repairs)',
    ],
];
