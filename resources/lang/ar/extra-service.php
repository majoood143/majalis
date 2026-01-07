<?php

return [
    // Resource Labels
    'singular' => 'خدمة إضافية',
    'plural' => 'خدمات إضافية',
    'navigation_label' => 'خدمات إضافية',

    // Form Sections
    'service_information' => 'معلومات الخدمة',
    'pricing' => 'التسعير',
    'media' => 'الوسائط',
    'settings' => 'الإعدادات',

    // Form Fields
    'hall' => 'القاعة',
    'name_en' => 'الاسم (الإنجليزية)',
    'name_ar' => 'الاسم (العربية)',
    'description_en' => 'الوصف (الإنجليزية)',
    'description_ar' => 'الوصف (العربية)',
    'price' => 'السعر',
    'unit' => 'الوحدة',
    'minimum_quantity' => 'الحد الأدنى للكمية',
    'maximum_quantity' => 'الحد الأقصى للكمية',
    'image' => 'الصورة',
    'order' => 'الترتيب',
    'is_active' => 'نشط',
    'is_required' => 'خدمة مطلوبة',

    // Unit Options
    'units' => [
        'per_person' => 'لكل شخص',
        'per_item' => 'لكل عنصر',
        'per_hour' => 'لكل ساعة',
        'fixed' => 'سعر ثابت',
    ],

    // Field Helpers
    'maximum_quantity_helper' => 'اتركه فارغًا للغير محدود',
    'is_required_helper' => 'يتم إضافتها تلقائيًا لجميع الحجوزات',

    // Table Columns
    'name' => 'الاسم',
    'hall_name' => 'القاعة',
    'unit_label' => 'الوحدة',
    'required' => 'مطلوب',
    'active' => 'نشط',
    'created_at' => 'تاريخ الإنشاء',

    // Filters
    'filters' => [
        'hall' => 'القاعة',
        'unit' => 'الوحدة',
        'active' => 'نشط',
        'required' => 'مطلوب',
    ],

    // Actions
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'create' => 'إنشاء خدمة إضافية',

    // Messages
    'created' => 'تم إنشاء الخدمة الإضافية بنجاح',
    'updated' => 'تم تحديث الخدمة الإضافية بنجاح',
    'deleted' => 'تم حذف الخدمة الإضافية بنجاح',

    // Hall Label Format
    'hall_label_format' => ':hall_name - :city_name - (:owner_name)',
    'unnamed_hall' => 'قاعة بدون اسم',
    'unknown_city' => 'مدينة غير معروفة',
    'no_owner' => 'بدون مالك',

    // List Page - Tabs
    'tabs' => [
        'all' => 'جميع الخدمات',
        'active' => 'نشطة',
        'inactive' => 'غير نشطة',
        'required' => 'خدمات مطلوبة',
        'per_person' => 'لكل شخص',
        'per_item' => 'لكل عنصر',
        'per_hour' => 'لكل ساعة',
        'fixed' => 'سعر ثابت',
        'with_image' => 'بصور',
        'without_image' => 'بدون صور',
    ],

    // List Page - Header Actions
    'actions' => [
        'create' => 'إنشاء خدمة',
        'export' => 'تصدير الخدمات',
        'bulk_price_update' => 'تحديث السعر بالجملة',
        'duplicate_services' => 'نسخ لقاعة أخرى',

        // Export Modal
        'export_modal' => [
            'heading' => 'تصدير الخدمات الإضافية',
            'description' => 'تصدير جميع بيانات الخدمات الإضافية إلى CSV.',
            'submit_label' => 'تصدير',
        ],

        // Bulk Price Update Modal
        'bulk_price_update_modal' => [
            'update_type' => 'نوع التحديث',
            'update_type_options' => [
                'percentage_increase' => 'زيادة نسبية',
                'percentage_decrease' => 'نقص نسبي',
                'fixed_increase' => 'زيادة مبلغ ثابت',
                'fixed_decrease' => 'نقص مبلغ ثابت',
            ],
            'value' => 'القيمة',
            'hall_optional' => 'تطبيق على القاعة (اختياري)',
            'hall_helper' => 'اتركه فارغًا للتطبيق على جميع القاعات',
        ],

        // Duplicate Services Modal
        'duplicate_services_modal' => [
            'source_hall' => 'القاعة المصدر',
            'target_hall' => 'القاعة الهدف',
            'copy_inactive' => 'تضمين الخدمات غير النشطة',
        ],
    ],

    // Export CSV Headers
    'export_headers' => [
        'id' => 'المعرف',
        'hall' => 'القاعة',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
        'description_en' => 'الوصف (إنجليزي)',
        'description_ar' => 'الوصف (عربي)',
        'price' => 'السعر (ريال عماني)',
        'unit' => 'الوحدة',
        'min_quantity' => 'الحد الأدنى للكمية',
        'max_quantity' => 'الحد الأقصى للكمية',
        'required' => 'مطلوب',
        'active' => 'نشط',
        'order' => 'الترتيب',
        'has_image' => 'يحتوي على صورة',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Export Values
    'export_values' => [
        'unlimited' => 'غير محدود',
        'yes' => 'نعم',
        'no' => 'لا',
        'n_a' => 'غير متوفر',
    ],

    // Notifications
    'notifications' => [
        'export_successful' => 'التصدير ناجح',
        'export_body' => 'تم تصدير الخدمات الإضافية بنجاح.',
        'download' => 'تحميل الملف',

        'prices_updated' => 'تم تحديث الأسعار',
        'services_updated' => 'تم تحديث :count خدمة بنجاح.',

        'services_duplicated' => 'تم نسخ الخدمات',
        'services_duplicated_body' => 'تم نسخ :count خدمة بنجاح.',
    ],
];
