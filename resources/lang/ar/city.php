<?php

return [
    // Resource Labels
    'singular' => 'المدينة',
    'plural' => 'المدن',
    'navigation_label' => 'المدن',
    
    // Form Sections
    'city_information' => 'معلومات المدينة',
    'location' => 'الموقع',
    'settings' => 'الإعدادات',
    
    // Form Fields
    'region' => 'المنطقة',
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
    'region_name' => 'المنطقة',
    'halls' => 'القاعات',
    'created_at' => 'تاريخ الإنشاء',
    
    // Filters
    'filters' => [
        'region' => 'المنطقة',
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
        'create' => 'إنشاء مدينة',
        'export' => 'تصدير المدن',
    ],
    
    // Export Modal
    'export_modal' => [
        'heading' => 'تصدير بيانات المدن',
        'description' => 'سيؤدي هذا إلى تصدير جميع بيانات المدن إلى ملف CSV.',
        'submit_label' => 'تصدير',
    ],
    
    // List Page - Tabs
    'tabs' => [
        'all' => 'جميع المدن',
        'active' => 'نشطة',
        'inactive' => 'غير نشطة',
        'with_halls' => 'بقاعات',
        'without_halls' => 'بدون قاعات',
    ],
    
    // Export CSV Headers
    'export_headers' => [
        'id' => 'المعرف',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
        'code' => 'الكود',
        'region' => 'المنطقة',
        'latitude' => 'خط العرض',
        'longitude' => 'خط الطول',
        'halls_count' => 'عدد القاعات',
        'active' => 'نشط',
        'order' => 'الترتيب',
        'created_at' => 'تاريخ الإنشاء',
    ],
    
    // Export Values
    'export_values' => [
        'yes' => 'نعم',
        'no' => 'لا',
        'not_applicable' => 'غير متوفر',
    ],
    
    // Notifications
    'notifications' => [
        'export_successful' => 'التصدير ناجح',
        'export_body' => 'تم تصدير المدن بنجاح إلى: :filename',
        'download' => 'تحميل',
    ],
    
    // Messages
    'messages' => [
        'created' => 'تم إنشاء المدينة بنجاح',
        'updated' => 'تم تحديث المدينة بنجاح',
        'deleted' => 'تم حذف المدينة بنجاح',
    ],
];