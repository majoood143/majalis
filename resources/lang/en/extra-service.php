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
    'view' => 'View',
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

        // Toggle Active
        'cannot_deactivate_title' => 'Cannot Deactivate',
        'cannot_deactivate_body' => 'Required services cannot be deactivated. Remove the required flag first.',
        'cannot_deactivate_body_short' => 'Required services cannot be deactivated.',
        'status_updated_title' => 'Status Updated',
        'status_updated_body' => 'Service status has been updated successfully.',

        // Toggle Required
        'requirement_updated_title' => 'Requirement Status Updated',
        'requirement_updated_body' => 'Service requirement status has been updated.',

        // Update Price
        'price_updated_title' => 'Price Updated',
        'price_updated_body' => 'Service price has been updated successfully.',

        // Duplicate
        'service_duplicated_title' => 'Service Duplicated',
        'service_duplicated_body' => 'The service has been duplicated successfully.',

        // Replace Image
        'image_updated_title' => 'Image Updated',
        'image_updated_body' => 'Service image has been replaced successfully.',

        // Delete
        'cannot_delete_required_title' => 'Cannot Delete Required Service',
        'cannot_delete_required_body' => 'Required services cannot be deleted. Make it optional first.',
        'cannot_delete_title' => 'Cannot Delete',
        'cannot_delete_body' => 'Required services cannot be deleted.',
        'service_deleted_title' => 'Service Deleted',
        'service_deleted_body' => 'The extra service has been deleted successfully.',

        // Save / Create
        'service_updated_title' => 'Service Updated',
        'service_updated_body' => 'The extra service has been updated successfully.',
        'extra_service_created_title' => 'Extra Service Created',
        'extra_service_created_body' => 'The extra service has been created successfully.',

        // Validation
        'invalid_price_title' => 'Invalid Price',
        'invalid_price_body' => 'Price cannot be negative.',
        'invalid_quantity_range_title' => 'Invalid Quantity Range',
        'invalid_quantity_range_body' => 'Maximum quantity must be greater than or equal to minimum quantity.',
        'auto_activation_title' => 'Auto-Activation',
        'auto_activation_body' => 'Required services must be active. Service has been activated automatically.',
        'hall_changed_title' => 'Hall Changed',
        'hall_changed_body' => 'Moving service to a different hall. Existing bookings will not be affected.',
        'similar_service_title' => 'Similar Service Found',
        'similar_service_body' => 'A service with a similar name already exists for this hall.',
    ],

    // Page Actions (Edit & View pages)
    'page_actions' => [
        // Toggle Active
        'deactivate' => 'Deactivate',
        'activate' => 'Activate',
        'deactivate_heading' => 'Deactivate Service',
        'activate_heading' => 'Activate Service',
        'deactivate_description' => 'This will deactivate the service. It will no longer be available for new bookings.',
        'activate_description' => 'This will activate the service and make it available for bookings.',

        // Toggle Required
        'make_optional' => 'Make Optional',
        'make_required' => 'Make Required',
        'make_optional_heading' => 'Make Service Optional',
        'make_required_heading' => 'Make Service Required',
        'make_optional_description' => 'This service will no longer be automatically added to bookings.',
        'make_required_description' => 'This service will be automatically added to all new bookings for this hall.',

        // Other Actions
        'view_bookings' => 'View Bookings',
        'view_hall' => 'View Hall',
        'calculate_revenue' => 'Calculate Revenue',
        'revenue_analysis' => 'Revenue Analysis',
        'service_revenue_analysis_heading' => 'Service Revenue Analysis',
        'close' => 'Close',
        'update_price' => 'Update Price',
        'replace_image' => 'Replace Image',
        'duplicate' => 'Duplicate',
        'edit_duplicate' => 'Edit Duplicate',
        'view_duplicate' => 'View Duplicate',

        // Update Price Modal Fields
        'new_price' => 'New Price',
        'reason_for_price_change' => 'Reason for Price Change',
        'apply_to_pending' => 'Apply to Pending Bookings',
        'apply_to_pending_helper' => 'Update price for pending bookings that include this service',

        // Duplicate Modal Fields
        'target_hall' => 'Target Hall',
        'copy_image' => 'Copy Image',

        // Replace Image Modal Fields
        'new_image' => 'New Image',
    ],

    // Infolist (View page)
    'infolist' => [
        // Section Titles
        'service_overview' => 'Service Overview',
        'description_section' => 'Description',
        'pricing_details' => 'Pricing Details',
        'service_settings' => 'Service Settings',
        'usage_statistics' => 'Usage Statistics',
        'system_information' => 'System Information',
        'activity_history' => 'Activity History',

        // Entry Labels
        'service_name' => 'Service Name',
        'hall' => 'Hall',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
        'description_en' => 'Description (English)',
        'description_ar' => 'Description (Arabic)',
        'price' => 'Price',
        'unit' => 'Unit',
        'min_quantity' => 'Min Quantity',
        'max_quantity' => 'Max Quantity',
        'price_range' => 'Price Range',
        'active_status' => 'Active Status',
        'required_service' => 'Required Service',
        'display_order' => 'Display Order',
        'image' => 'Image',
        'total_bookings' => 'Total Bookings',
        'total_revenue' => 'Total Revenue',
        'avg_quantity' => 'Avg Quantity',
        'last_booked' => 'Last Booked',
        'service_id' => 'Service ID',
        'created_at' => 'Created At',
        'last_updated' => 'Last Updated',

        // Values
        'image_available' => 'Available',
        'no_image' => 'No Image',
        'never' => 'Never',
        'unlimited' => 'Unlimited',
        'fixed_price_display' => ':price OMR (Fixed)',

        // Unit Display Labels
        'unit_per_person' => 'Per Person',
        'unit_per_item' => 'Per Item',
        'unit_per_hour' => 'Per Hour',
        'unit_fixed' => 'Fixed',
    ],

    // Status Labels (used in page titles/subheadings)
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'required' => 'Required',
    ],

    // Page Titles & Subheadings
    'page_titles' => [
        'create' => 'Create Extra Service',
        'edit' => 'Edit Service: :name',
        'view' => 'View Service: :name',
    ],
    'page_subheadings' => [
        'create' => 'Add a new extra service to a hall',
    ],
];
