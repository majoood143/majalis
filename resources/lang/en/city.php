<?php

return [
    // Resource Labels
    'singular' => 'City',
    'plural' => 'Cities',
    'navigation_label' => 'Cities',
    
    // Form Sections
    'city_information' => 'City Information',
    'location' => 'Location',
    'settings' => 'Settings',
    
    // Form Fields
    'region' => 'Region',
    'name_en' => 'Name (English)',
    'name_ar' => 'Name (Arabic)',
    'code' => 'Code',
    'description_en' => 'Description (English)',
    'description_ar' => 'Description (Arabic)',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'order' => 'Order',
    'is_active' => 'Is Active',
    
    // Table Columns
    'name' => 'Name',
    'region_name' => 'Region',
    'halls' => 'Halls',
    'created_at' => 'Created At',
    
    // Filters
    'filters' => [
        'region' => 'Region',
        'active' => 'Active',
        'active_only' => 'Active only',
        'inactive_only' => 'Inactive only',
    ],
    
    // Table Actions
    'table_actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
    
    // List Page - Header Actions
    'list_actions' => [
        'create' => 'Create City',
        'export' => 'Export Cities',
    ],
    
    // Export Modal
    'export_modal' => [
        'heading' => 'Export Cities Data',
        'description' => 'This will export all cities data to a CSV file.',
        'submit_label' => 'Export',
    ],
    
    // List Page - Tabs
    'tabs' => [
        'all' => 'All Cities',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'with_halls' => 'With Halls',
        'without_halls' => 'Without Halls',
    ],
    
    // Export CSV Headers
    'export_headers' => [
        'id' => 'ID',
        'name_en' => 'Name (EN)',
        'name_ar' => 'Name (AR)',
        'code' => 'Code',
        'region' => 'Region',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'halls_count' => 'Halls Count',
        'active' => 'Active',
        'order' => 'Order',
        'created_at' => 'Created At',
    ],
    
    // Export Values
    'export_values' => [
        'yes' => 'Yes',
        'no' => 'No',
        'not_applicable' => 'N/A',
    ],
    
    // Notifications
    'notifications' => [
        'export_successful' => 'Export Successful',
        'export_body' => 'Cities exported successfully to: :filename',
        'download' => 'Download',
    ],
    
    // Messages
    'messages' => [
        'created' => 'City created successfully',
        'updated' => 'City updated successfully',
        'deleted' => 'City deleted successfully',
    ],
];