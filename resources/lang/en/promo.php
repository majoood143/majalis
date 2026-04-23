<?php

declare(strict_types=1);

return [
    // Navigation
    'navigation_group' => 'Promotions',
    'navigation_label' => 'Promo Codes',
    'singular'         => 'Promo Code',
    'plural'           => 'Promo Codes',

    // Form sections
    'section_details'  => 'Code Details',
    'section_discount' => 'Discount',
    'section_validity' => 'Validity',
    'section_scope'    => 'Scope (Hall)',

    // Form fields
    'field_code'                 => 'Code',
    'field_name'                 => 'Label / Name',
    'field_description'          => 'Description',
    'field_discount_type'        => 'Discount Type',
    'field_discount_value_pct'   => 'Discount (%)',
    'field_discount_value_fixed' => 'Discount Amount',
    'field_valid_from'           => 'Valid From',
    'field_valid_until'          => 'Valid Until',
    'field_max_uses'             => 'Max Uses',
    'field_max_uses_helper'      => 'Leave empty for unlimited uses.',
    'field_is_active'            => 'Active',
    'field_hall'                 => 'Hall (optional)',
    'field_hall_helper'          => 'Leave empty to allow the code on all halls.',
    'field_hall_helper_owner'    => 'Select which of your halls this code applies to.',

    // Discount types
    'type_percentage' => 'Percentage (%)',
    'type_fixed'      => 'Fixed Amount',

    // Table columns
    'col_code'        => 'Code',
    'col_name'        => 'Name',
    'col_discount'    => 'Discount',
    'col_hall'        => 'Hall',
    'col_used'        => 'Uses',
    'col_valid_until' => 'Expires',
    'col_active'      => 'Active',

    // Filters
    'filter_active' => 'Active status',
    'filter_type'   => 'Discount type',
    'filter_hall'   => 'Hall',

    // Values
    'all_halls' => 'All Halls',
    'no_expiry' => 'No expiry',

    // Frontend labels
    'label'          => 'Promo Code',
    'placeholder'    => 'Enter promo code',
    'apply'          => 'Apply',
    'applied'        => '✓ Applied',
    'checking'       => 'Checking...',
    'discount_label' => 'Promo Discount',
    'error'          => 'Could not validate the promo code. Please try again.',

    // Validation messages
    'invalid_code'      => 'Invalid promo code.',
    'code_inactive'     => 'This promo code is not active.',
    'code_not_started'  => 'This promo code is not valid yet.',
    'code_expired'      => 'This promo code has expired.',
    'code_used_up'      => 'This promo code has reached its usage limit.',
    'code_applied'      => 'Promo code applied! You save :amount OMR.',

    // Bookings relation manager
    'rel_bookings_title'       => 'Bookings Using This Code',
    'rel_col_booking_number'   => 'Booking #',
    'rel_col_customer'         => 'Customer',
    'rel_col_hall'             => 'Hall',
    'rel_col_booking_date'     => 'Booking Date',
    'rel_col_discount'         => 'Discount',
    'rel_col_total'            => 'Total',
    'rel_col_status'           => 'Status',
    'rel_col_payment_status'   => 'Payment',
    'rel_col_created_at'       => 'Created At',
    'rel_export_bookings'      => 'Export Bookings',

    // Export
    'export_btn'          => 'Export CSV',
    'export_col_email'    => 'Email',
    'export_col_phone'    => 'Phone',
    'export_success_title' => 'Export Ready',
    'export_success_body'  => 'File :filename has been generated.',
    'export_download'      => 'Download',
];
