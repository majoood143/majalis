<?php

return [
    // Resource Labels
    'singular' => 'المنطقة',
    'plural' => 'المناطق',
    'navigation_label' => 'المناطق',
    'navigation_group' => 'الإعدادات العامة',

    // Form Sections
    'region_information' => 'معلومات المنطقة',
    'location' => 'الموقع',
    'settings' => 'الإعدادات',

    // Form Fields
    'name_en' => 'الاسم (الإنجليزية)',
    'name_ar' => 'الاسم (العربية)',
    'code' => 'الكود',
    'description_en' => 'الوصف (الإنجليزية)',
    'description_ar' => 'الوصف (العربية)',
    'latitude' => 'خط العرض',
    'longitude' => 'خط الطول',
    'order' => 'الترتيب',
    'is_active' => 'نشط',

    // Table Columns
    'name' => 'الاسم',
    'cities' => 'المدن',
    'created_at' => 'تاريخ الإنشاء',

    // Filters
    'filters' => [
        'active' => 'نشط',
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
    ],

    // Table Actions
    'table_actions' => [
        'edit' => 'تعديل',
        'delete' => 'حذف',
    ],

    // List Page - Header Actions
    'list_actions' => [
        'create' => 'إنشاء منطقة',
        'export' => 'تصدير المناطق',
    ],

    // List Page - Tabs
    'tabs' => [
        'all' => 'جميع المناطق',
        'active' => 'نشطة',
        'inactive' => 'غير نشطة',
        'with_cities' => 'بمدن',
    ],

    // Export CSV Headers
    'export_headers' => [
        'id' => 'المعرف',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
        'code' => 'الكود',
        'cities' => 'المدن',
        'active' => 'نشط',
        'order' => 'الترتيب',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Export Values
    'export_values' => [
        'yes' => 'نعم',
        'no' => 'لا',
    ],

    // Notifications
    'notifications' => [
        'export_successful' => 'التصدير ناجح',
        'download' => 'تحميل',
    ],

    // Messages
    'messages' => [
        'created' => 'تم إنشاء المنطقة بنجاح',
        'updated' => 'تم تحديث المنطقة بنجاح',
        'deleted' => 'تم حذف المنطقة بنجاح',
    ],
];
