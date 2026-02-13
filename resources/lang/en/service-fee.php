<?php

declare(strict_types=1);

/**
 * English translations for Service Fee Settings.
 *
 * File: resources/lang/en/service-fee.php
 *
 * @package Lang\En
 */
return [

    // ── Resource Labels ──
    'singular'         => 'Service Fee',
    'plural'           => 'Service Fees',
    'navigation_label' => 'Service Fees',
    'navigation_group' => 'Financial',
    'subheading'       => 'Manage customer-visible service fees charged on bookings',

    // ── Form Sections ──
    'fee_scope'        => 'Fee Scope',
    'scope_description'=> 'Select a specific hall, owner, or leave both empty for a global fee',
    'fee_details'      => 'Fee Details',
    'validity_period'  => 'Validity Period',

    // ── Form Fields ──
    'hall'             => 'Hall (Optional)',
    'owner'            => 'Owner (Optional)',
    'name_en'          => 'Name (English)',
    'name_ar'          => 'Name (Arabic)',
    'fee_type'         => 'Fee Type',
    'fee_value'        => 'Fee Value',
    'description_en'   => 'Description (English)',
    'description_ar'   => 'Description (Arabic)',
    'effective_from'   => 'Effective From',
    'effective_to'     => 'Effective To',
    'is_active'        => 'Active',

    // ── Field Helpers ──
    'hall_helper'           => 'Leave empty for owner-level or global fee',
    'owner_helper'          => 'Leave empty for global fee',
    'scope_note_title'      => 'Scope Priority',
    'scope_note'            => 'Priority: Hall-specific > Owner-specific > Global. Only the highest-priority active fee applies.',
    'effective_from_helper' => 'Leave empty for immediate effect',
    'effective_to_helper'   => 'Leave empty for indefinite period',

    // ── Fee Types ──
    'percentage' => 'Percentage',
    'fixed'      => 'Fixed Amount',

    // ── Table Columns ──
    'scope'      => 'Scope',
    'value'      => 'Value',
    'created_at' => 'Created At',

    // ── Scope Types ──
    'global'         => 'Global',
    'hall_specific'  => 'Hall: :name',
    'owner_specific' => 'Owner: :name',

    // ── Filters ──
    'filters' => [
        'scope_type' => 'Scope Type',
        'active'     => 'Active',
        'global'     => 'Global',
        'owner'      => 'Owner-Specific',
        'hall'       => 'Hall-Specific',
    ],

    // ── Tabs ──
    'tabs' => [
        'all'            => 'All',
        'active'         => 'Active',
        'inactive'       => 'Inactive',
        'global'         => 'Global',
        'hall_specific'  => 'Hall-Specific',
        'owner_specific' => 'Owner-Specific',
    ],

    // ── Actions ──
    'edit'            => 'Edit',
    'delete'          => 'Delete',
    'create'          => 'Create Service Fee',
    'cleanup_expired' => 'Cleanup Expired',

    // ── Modals ──
    'cleanup_modal_title' => 'Delete Expired Service Fees',
    'cleanup_modal_desc'  => 'This will permanently delete all expired and inactive service fee settings.',
    'cleanup_done'        => 'Cleanup Completed',
    'cleanup_done_body'   => ':count expired service fee(s) have been deleted.',

    // ── Notifications ──
    'created'              => 'Service Fee Created',
    'created_body'         => 'A new :scope service fee has been created successfully.',
    'updated'              => 'Service Fee Updated',
    'deleted'              => 'Service Fee Deleted',
    'scope_adjusted'       => 'Scope Adjusted',
    'scope_adjusted_body'  => 'Both hall and owner were selected. Hall-specific fee will be created.',
    'invalid_value'        => 'Invalid Fee Value',
    'percentage_max'       => 'Percentage fee cannot exceed 100%.',
    'value_positive'       => 'Fee value cannot be negative.',
    'invalid_dates'        => 'Invalid Date Range',
    'date_range_error'     => 'Effective-from date must be before effective-to date.',
    'overlap_warning'      => 'Overlapping Fee Detected',
    'overlap_warning_body' => 'There are :count active fee setting(s) with the same scope. Only the first match will apply.',

    // ── Customer-Facing Labels (used in booking views) ──
    'customer_label'       => 'Service Fee',
    'customer_description' => 'Platform service fee',
];
