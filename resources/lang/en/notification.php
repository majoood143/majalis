<?php

return [
    // Resource Labels
    'singular' => 'Notification',
    'plural' => 'Notifications',
    'navigation_label' => 'Notifications',
    'navigation_group' => 'System',

    // Table Columns
    'columns' => [
        'type' => 'Type',
        'user' => 'User',
        'title' => 'Title',
        'message' => 'Message',
        'read' => 'Read',
        'created_at' => 'Created At',
    ],

    // Filters
    'filters' => [
        'read_status' => 'Read Status',
    ],

    // Table Actions
    'actions' => [
        'mark_as_read' => 'Mark as Read',
        'mark_all_as_read' => 'Mark All as Read',
        'delete_read' => 'Delete Read',
    ],

    // Tabs
    'tabs' => [
        'all' => 'All',
        'unread' => 'Unread',
        'read' => 'Read',
    ],

    // Notifications (Filament toast messages)
    'notifications' => [
        'all_marked_read_title' => 'All notifications marked as read',
        'deleted_title' => ':count notification(s) deleted',
    ],

    // Infolist (View page)
    'infolist' => [
        'details_section' => 'Notification Details',
        'type' => 'Type',
        'user' => 'User',
        'title' => 'Title',
        'message' => 'Message',
        'created_at' => 'Created At',
        'read_at' => 'Read At',
        'unread_placeholder' => 'Unread',
    ],
];
