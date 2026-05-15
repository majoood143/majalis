<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Resource Labels
    |--------------------------------------------------------------------------
    */
    'resource' => [
        'label'            => 'Event Type',
        'plural_label'     => 'Event Types',
        'navigation_label' => 'Event Types',
        'navigation_group' => 'Booking Management',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Sections
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'details' => [
            'title'       => 'Event Type Details',
            'description' => 'Define the event type name in English and Arabic',
        ],
        'settings' => [
            'title'       => 'Settings',
            'description' => 'Control visibility and ordering',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */
    'fields' => [
        'name_en'    => 'Name (English)',
        'name_ar'    => 'Name (Arabic)',
        'name'       => 'Name',
        'sort_order' => 'Sort Order',
        'is_active'  => 'Active',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */
    'columns' => [
        'name'           => 'Name',
        'sort_order'     => 'Order',
        'is_active'      => 'Active',
        'bookings_count' => 'Bookings',
        'created_at'     => 'Created At',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */
    'filters' => [
        'is_active' => 'Active Status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'edit'   => 'Edit',
        'delete' => 'Delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'created' => 'Event type created successfully.',
        'updated' => 'Event type updated successfully.',
        'deleted' => 'Event type deleted successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Texts
    |--------------------------------------------------------------------------
    */
    'helpers' => [
        'sort_order' => 'Lower numbers appear first in selection lists.',
        'is_active'  => 'Inactive types will not appear in booking forms.',
    ],
];
