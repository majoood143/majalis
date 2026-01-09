<?php

return [


    // Resource Labels
    'singular' => 'Commission Setting',
    'plural' => 'Commission Settings',
    'navigation_label' => 'Commission Settings',
    'navigation_group' => 'Financial',

    // Form Sections
    'commission_scope' => 'Commission Scope',
    'scope_description' => 'Select either a specific hall, owner, or leave both empty for global settings',
    'commission_details' => 'Commission Details',
    'validity_period' => 'Validity Period',

    // Form Fields
    'hall' => 'Hall (Optional)',
    'owner' => 'Owner (Optional)',
    'name_en' => 'Name (English)',
    'name_ar' => 'Name (Arabic)',
    'commission_type' => 'Commission Type',
    'commission_value' => 'Commission Value',
    'description_en' => 'Description (English)',
    'description_ar' => 'Description (Arabic)',
    'effective_from' => 'Effective From',
    'effective_to' => 'Effective To',
    'is_active' => 'Is Active',

    // Field Helpers
    'hall_helper' => 'Leave empty for owner-level or global commission',
    'owner_helper' => 'Leave empty for global commission',
    'scope_note' => 'Priority: Hall-specific > Owner-specific > Global',
    'effective_from_helper' => 'Leave empty for immediate effect',
    'effective_to_helper' => 'Leave empty for indefinite period',

    // Commission Types
    'percentage' => 'Percentage',
    'fixed' => 'Fixed Amount',

    // Table Columns
    'scope' => 'Scope',
    'value' => 'Value',
    'created_at' => 'Created At',

    // Scope Types
    'global' => 'Global',
    'hall_specific' => 'Hall: :name',
    'owner_specific' => 'Owner: :name',

    // Filters
    'filters' => [
        'scope_type' => 'Scope Type',
        'active' => 'Active',
        'global' => 'Global',
        'owner' => 'Owner-Specific',
        'hall' => 'Hall-Specific',
    ],

    // Actions
    'edit' => 'Edit',
    'delete' => 'Delete',
    'create' => 'Create Commission Setting',
    'export' => 'Export',
    'bulk_activate' => 'Bulk Activate',
    'cleanup_expired' => 'Cleanup Expired',

    //tabs
    'tabs' => [
        'all' => 'All',
        'global' => 'Global',
        'owner_specific' => 'Owner-Specific',
        'hall_specific' => 'Hall-Specific',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'percentage' => 'Percentage',
        'fixed' => 'Fixed Amount',
        'expired' => 'Expired',
        'expiring_soon' => 'Expiring Soon',
    ],

    // Messages
    'created' => 'Commission setting created successfully',
    'updated' => 'Commission setting updated successfully',
    'deleted' => 'Commission setting deleted successfully',


];
