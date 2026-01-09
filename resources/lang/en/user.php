<?php

return [
    // General
    'export_users' => 'Export Users',
    'yes' => 'Yes',
    'no' => 'No',
    
    // Resource
    'resource' => [
        'model_label' => 'User',
        'plural_model_label' => 'Users',
        'navigation_label' => 'Users',
        'navigation_group' => 'User Management',
    ],
    
    // Tabs
    'tabs' => [
        'all_users' => 'All Users',
        'administrators' => 'Administrators',
        'hall_owners' => 'Hall Owners',
        'customers' => 'Customers',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'email_verified' => 'Email Verified',
        'unverified' => 'Unverified',
    ],
    
    // Form
    'form' => [
        'sections' => [
            'user_information' => 'User Information',
            'contact_information' => 'Contact Information',
        ],
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_helper' => 'Leave empty to keep current password',
        'role' => 'Role',
        'phone' => 'Phone',
        'phone_country_code' => 'Country Code',
        'is_active' => 'Active',
    ],
    
    // Table
    'table' => [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'phone' => 'Phone',
        'verified' => 'Verified',
        'active' => 'Active',
        'created_at' => 'Created At',
    ],
    
    // Filters
    'filters' => [
        'role' => 'Role',
        'active' => 'Active',
        'active_true' => 'Active users',
        'active_false' => 'Inactive users',
        'email_verified' => 'Email Verified',
        'verified_true' => 'Verified',
        'verified_false' => 'Not verified',
    ],
    
    // Actions
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_bulk' => 'Delete Selected',
    ],
    
    // Export
    'export' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'phone' => 'Phone',
        'email_verified' => 'Email Verified',
        'active' => 'Active',
        'created_at' => 'Created At',
        'success_title' => 'Export Successful',
        'success_body' => 'The file :filename has been exported successfully.',
        'download' => 'Download',
    ],
    
    // Roles
    'roles' => [
        'admin' => 'Administrator',
        'hall_owner' => 'Hall Owner',
        'customer' => 'Customer',
    ],
];