<?php

/**
 * English translations for Advance Payment feature
 *
 * Add these to your existing resources/lang/en/halls.php file
 */

return [
    // Advance Payment - Hall Settings
    'advance_payment' => 'Advance Payment',
    'advance_payment_settings' => 'Advance Payment Settings',
    'allows_advance_payment' => 'Enable Advance Payment',
    'allows_advance_payment_help' => 'Require customers to pay advance amount when booking',
    'advance_payment_type' => 'Payment Type',
    'advance_payment_type_help' => 'How to calculate the advance amount',
    'advance_type_fixed' => 'Fixed Amount',
    'advance_type_percentage' => 'Percentage of Total',
    'advance_payment_amount' => 'Advance Amount',
    'advance_payment_amount_help' => 'Fixed amount to be paid in advance (OMR)',
    'advance_payment_amount_placeholder' => 'e.g., 500.000',
    'advance_payment_percentage' => 'Advance Percentage',
    'advance_payment_percentage_help' => 'Percentage of total booking to be paid in advance',
    'advance_payment_percentage_placeholder' => 'e.g., 20',
    'minimum_advance_payment' => 'Minimum Advance',
    'minimum_advance_payment_help' => 'Minimum advance amount required (optional)',
    'minimum_advance_payment_placeholder' => 'e.g., 100.000',

    // Advance Payment - Preview & Display
    'advance_payment_preview' => 'Preview',
    'advance_payment_preview_help' => 'Example calculation based on your settings',
    'preview_for_price' => 'If total booking = :price OMR:',
    'customer_pays_advance' => 'Customer pays advance',
    'balance_due_before_event' => 'Balance due before event',
    'advance_required' => 'Advance Payment Required',
    'advance_payment_required' => 'This hall requires advance payment',
    'advance_payment_info' => 'You need to pay :amount OMR as advance. The remaining balance of :balance OMR must be paid before the event.',

    // Booking - Payment Type
    'payment_type' => 'Payment Type',
    'payment_type_full' => 'Full Payment',
    'payment_type_advance' => 'Advance Payment',
    'full_payment' => 'Full Payment',
    'advance_payment_only' => 'Advance Only',
    'pay_full_amount' => 'Pay Full Amount',
    'pay_advance_only' => 'Pay Advance Only',

    // Booking - Advance Payment Details
    'advance_paid' => 'Advance Paid',
    'balance_due' => 'Balance Due',
    'balance_pending' => 'Balance Pending',
    'balance_paid' => 'Balance Paid',
    'balance_payment_status' => 'Balance Payment Status',
    'balance_not_paid' => 'Not Paid Yet',
    'balance_paid_on' => 'Paid on :date',
    'balance_payment_method' => 'Balance Payment Method',
    'balance_payment_reference' => 'Payment Reference',
    'mark_balance_as_paid' => 'Mark Balance as Paid',
    'balance_payment_details' => 'Balance Payment Details',

    // Payment Methods
    'bank_transfer' => 'Bank Transfer',
    'cash' => 'Cash',
    'card' => 'Card',
    'online_payment' => 'Online Payment',

    // Messages & Notifications
    'advance_payment_calculated' => 'Advance payment has been calculated based on hall settings',
    'balance_marked_as_paid' => 'Balance payment has been marked as paid successfully',
    'balance_payment_recorded' => 'Balance payment recorded successfully',
    'invalid_advance_settings' => 'Invalid advance payment settings for this hall',
    'advance_amount_exceeds_total' => 'Advance amount cannot exceed total booking amount',

    // Validation
    'advance_amount_required' => 'Advance amount is required when using fixed type',
    'advance_percentage_required' => 'Advance percentage is required when using percentage type',
    'advance_percentage_max' => 'Advance percentage cannot exceed 100%',
    'minimum_advance_min' => 'Minimum advance cannot be negative',

    // Help Text
    'advance_payment_explanation' => 'Advance payment allows you to secure bookings with a partial payment upfront. The remaining balance must be paid before the event date.',
    'advance_includes_services' => 'Note: Advance amount includes both hall price and services, as services need to be reserved from suppliers.',
    'balance_payment_explanation' => 'After advance payment, customers must pay the remaining balance via bank transfer or cash before the event. You can manually mark it as paid here.',
    'balance_payment_required' => 'Note: Booking cannot be completed without paying the balance before the event.',
    'advance_paid_success'=>'Advance payment has been made successfully.',
    'advance_amount' => 'Advance Amount',
];
