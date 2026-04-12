<?php

return [
    // Resource Labels
    'singular' => 'إشعار',
    'plural' => 'الإشعارات',
    'navigation_label' => 'الإشعارات',
    'navigation_group' => 'النظام',

    // Table Columns
    'columns' => [
        'type' => 'النوع',
        'user' => 'المستخدم',
        'title' => 'العنوان',
        'message' => 'الرسالة',
        'read' => 'مقروء',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Filters
    'filters' => [
        'read_status' => 'حالة القراءة',
    ],

    // Table Actions
    'actions' => [
        'mark_as_read' => 'تعيين كمقروء',
        'mark_all_as_read' => 'تعيين الكل كمقروء',
        'delete_read' => 'حذف المقروءة',
    ],

    // Tabs
    'tabs' => [
        'all' => 'الكل',
        'unread' => 'غير مقروءة',
        'read' => 'مقروءة',
    ],

    // Notifications (Filament toast messages)
    'notifications' => [
        'all_marked_read_title' => 'تم تعيين جميع الإشعارات كمقروءة',
        'deleted_title' => 'تم حذف :count إشعار',
    ],

    // Infolist (View page)
    'infolist' => [
        'details_section' => 'تفاصيل الإشعار',
        'type' => 'النوع',
        'user' => 'المستخدم',
        'title' => 'العنوان',
        'message' => 'الرسالة',
        'created_at' => 'تاريخ الإنشاء',
        'read_at' => 'تاريخ القراءة',
        'unread_placeholder' => 'غير مقروء',
    ],
];
