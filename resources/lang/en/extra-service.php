<?php

return [
    // Resource Labels
    'singular' => 'Extra Service',
    'plural' => 'Extra Services',
    'navigation_label' => 'Extra Services',

    // Form Sections
    'service_information' => 'Service Information',
    'pricing' => 'Pricing',
    'media' => 'Media',
    'settings' => 'Settings',

    // Form Fields
    'hall' => 'Hall',
    'name_en' => 'Name (English)',
    'name_ar' => 'Name (Arabic)',
    'description_en' => 'Description (English)',
    'description_ar' => 'Description (Arabic)',
    'price' => 'Price',
    'unit' => 'Unit',
    'minimum_quantity' => 'Minimum Quantity',
    'maximum_quantity' => 'Maximum Quantity',
    'image' => 'Image',
    'order' => 'Order',
    'is_active' => 'Is Active',
    'is_required' => 'Required Service',

    // Unit Options
    'units' => [
        'per_person' => 'Per Person',
        'per_item' => 'Per Item',
        'per_hour' => 'Per Hour',
        'fixed' => 'Fixed Price',
    ],

    // Field Helpers
    'maximum_quantity_helper' => 'Leave empty for unlimited',
    'is_required_helper' => 'Auto-added to all bookings',

    // Table Columns
    'name' => 'Name',
    'hall_name' => 'Hall',
    'unit_label' => 'Unit',
    'required' => 'Required',
    'active' => 'Active',
    'created_at' => 'Created At',

    // Filters
    'filters' => [
        'hall' => 'Hall',
        'unit' => 'Unit',
        'active' => 'Active',
        'required' => 'Required',
    ],

    // Actions
    'edit' => 'Edit',
    'delete' => 'Delete',
    'create' => 'Create Extra Service',

    // Messages
    'created' => 'Extra service created successfully',
    'updated' => 'Extra service updated successfully',
    'deleted' => 'Extra service deleted successfully',

    // Hall Label Format
    'hall_label_format' => ':hall_name - :city_name - (:owner_name)',
    'unnamed_hall' => 'Unnamed Hall',
    'unknown_city' => 'Unknown City',
    'no_owner' => 'No Owner',

    // List Page - Tabs
    'tabs' => [
        'all' => 'All Services',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'required' => 'Required Services',
        'per_person' => 'Per Person',
        'per_item' => 'Per Item',
        'per_hour' => 'Per Hour',
        'fixed' => 'Fixed Price',
        'with_image' => 'With Images',
        'without_image' => 'Without Images',
    ],

    // List Page - Header Actions
    'actions' => [
        'create' => 'Create Service',
        'export' => 'Export Services',
        'bulk_price_update' => 'Bulk Price Update',
        'duplicate_services' => 'Duplicate to Another Hall',

        // Export Modal
        'export_modal' => [
            'heading' => 'Export Extra Services',
            'description' => 'Export all extra services data to CSV.',
            'submit_label' => 'Export',
        ],

        // Bulk Price Update Modal
        'bulk_price_update_modal' => [
            'update_type' => 'Update Type',
            'update_type_options' => [
                'percentage_increase' => 'Percentage Increase',
                'percentage_decrease' => 'Percentage Decrease',
                'fixed_increase' => 'Fixed Amount Increase',
                'fixed_decrease' => 'Fixed Amount Decrease',
            ],
            'value' => 'Value',
            'hall_optional' => 'Apply to Hall (Optional)',
            'hall_helper' => 'Leave empty to apply to all halls',
        ],

        // Duplicate Services Modal
        'duplicate_services_modal' => [
            'source_hall' => 'Source Hall',
            'target_hall' => 'Target Hall',
            'copy_inactive' => 'Include Inactive Services',
        ],
    ],

    // Export CSV Headers
    'export_headers' => [
        'id' => 'ID',
        'hall' => 'Hall',
        'name_en' => 'Name (EN)',
        'name_ar' => 'Name (AR)',
        'description_en' => 'Description (EN)',
        'description_ar' => 'Description (AR)',
        'price' => 'Price (OMR)',
        'unit' => 'Unit',
        'min_quantity' => 'Min Quantity',
        'max_quantity' => 'Max Quantity',
        'required' => 'Required',
        'active' => 'Active',
        'order' => 'Order',
        'has_image' => 'Has Image',
        'created_at' => 'Created At',
    ],

    // Export Values
    'export_values' => [
        'unlimited' => 'Unlimited',
        'yes' => 'Yes',
        'no' => 'No',
        'n_a' => 'N/A',
    ],

    // Notifications
    'notifications' => [
        'export_successful' => 'Export Successful',
        'export_body' => 'Extra services exported successfully.',
        'download' => 'Download File',

        'prices_updated' => 'Prices Updated',
        'services_updated' => ':count service(s) updated successfully.',

        'services_duplicated' => 'Services Duplicated',
        'services_duplicated_body' => ':count service(s) duplicated successfully.',
    ],
];
