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
    'view' => 'عرض',
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

        // Toggle Active
        'cannot_deactivate_title' => 'تعذّر إلغاء التفعيل',
        'cannot_deactivate_body' => 'لا يمكن إلغاء تفعيل الخدمات المطلوبة. أزل علامة "مطلوب" أولاً.',
        'cannot_deactivate_body_short' => 'لا يمكن إلغاء تفعيل الخدمات المطلوبة.',
        'status_updated_title' => 'تم تحديث الحالة',
        'status_updated_body' => 'تم تحديث حالة الخدمة بنجاح.',

        // Toggle Required
        'requirement_updated_title' => 'تم تحديث حالة الإلزامية',
        'requirement_updated_body' => 'تم تحديث حالة إلزامية الخدمة.',

        // Update Price
        'price_updated_title' => 'تم تحديث السعر',
        'price_updated_body' => 'تم تحديث سعر الخدمة بنجاح.',

        // Duplicate
        'service_duplicated_title' => 'تم نسخ الخدمة',
        'service_duplicated_body' => 'تم نسخ الخدمة بنجاح.',

        // Replace Image
        'image_updated_title' => 'تم تحديث الصورة',
        'image_updated_body' => 'تم استبدال صورة الخدمة بنجاح.',

        // Delete
        'cannot_delete_required_title' => 'تعذّر حذف الخدمة المطلوبة',
        'cannot_delete_required_body' => 'لا يمكن حذف الخدمات المطلوبة. اجعلها اختيارية أولاً.',
        'cannot_delete_title' => 'تعذّر الحذف',
        'cannot_delete_body' => 'لا يمكن حذف الخدمات المطلوبة.',
        'service_deleted_title' => 'تم حذف الخدمة',
        'service_deleted_body' => 'تم حذف الخدمة الإضافية بنجاح.',

        // Save / Create
        'service_updated_title' => 'تم تحديث الخدمة',
        'service_updated_body' => 'تم تحديث الخدمة الإضافية بنجاح.',
        'extra_service_created_title' => 'تم إنشاء الخدمة الإضافية',
        'extra_service_created_body' => 'تم إنشاء الخدمة الإضافية بنجاح.',

        // Validation
        'invalid_price_title' => 'سعر غير صالح',
        'invalid_price_body' => 'لا يمكن أن يكون السعر سالباً.',
        'invalid_quantity_range_title' => 'نطاق كمية غير صالح',
        'invalid_quantity_range_body' => 'يجب أن يكون الحد الأقصى للكمية أكبر من أو يساوي الحد الأدنى للكمية.',
        'auto_activation_title' => 'تفعيل تلقائي',
        'auto_activation_body' => 'يجب أن تكون الخدمات المطلوبة نشطة. تم تفعيل الخدمة تلقائياً.',
        'hall_changed_title' => 'تغيير القاعة',
        'hall_changed_body' => 'جارٍ نقل الخدمة إلى قاعة مختلفة. لن تتأثر الحجوزات الحالية.',
        'similar_service_title' => 'خدمة مماثلة موجودة',
        'similar_service_body' => 'توجد خدمة بالاسم نفسه أو مشابه لهذه القاعة.',
    ],

    // Page Actions (Edit & View pages)
    'page_actions' => [
        // Toggle Active
        'deactivate' => 'إلغاء التفعيل',
        'activate' => 'تفعيل',
        'deactivate_heading' => 'إلغاء تفعيل الخدمة',
        'activate_heading' => 'تفعيل الخدمة',
        'deactivate_description' => 'سيؤدي هذا إلى إلغاء تفعيل الخدمة. لن تكون متاحة للحجوزات الجديدة.',
        'activate_description' => 'سيؤدي هذا إلى تفعيل الخدمة وإتاحتها للحجوزات.',

        // Toggle Required
        'make_optional' => 'جعلها اختيارية',
        'make_required' => 'جعلها مطلوبة',
        'make_optional_heading' => 'جعل الخدمة اختيارية',
        'make_required_heading' => 'جعل الخدمة مطلوبة',
        'make_optional_description' => 'لن تُضاف هذه الخدمة تلقائياً إلى الحجوزات بعد الآن.',
        'make_required_description' => 'ستُضاف هذه الخدمة تلقائياً إلى جميع الحجوزات الجديدة لهذه القاعة.',

        // Other Actions
        'view_bookings' => 'عرض الحجوزات',
        'view_hall' => 'عرض القاعة',
        'calculate_revenue' => 'احتساب الإيرادات',
        'revenue_analysis' => 'تحليل الإيرادات',
        'service_revenue_analysis_heading' => 'تحليل إيرادات الخدمة',
        'close' => 'إغلاق',
        'update_price' => 'تحديث السعر',
        'replace_image' => 'استبدال الصورة',
        'duplicate' => 'نسخ',
        'edit_duplicate' => 'تعديل النسخة',
        'view_duplicate' => 'عرض النسخة',

        // Update Price Modal Fields
        'new_price' => 'السعر الجديد',
        'reason_for_price_change' => 'سبب تغيير السعر',
        'apply_to_pending' => 'تطبيق على الحجوزات المعلقة',
        'apply_to_pending_helper' => 'تحديث السعر في الحجوزات المعلقة التي تتضمن هذه الخدمة',

        // Duplicate Modal Fields
        'target_hall' => 'القاعة الهدف',
        'copy_image' => 'نسخ الصورة',

        // Replace Image Modal Fields
        'new_image' => 'الصورة الجديدة',
    ],

    // Infolist (View page)
    'infolist' => [
        // Section Titles
        'service_overview' => 'نظرة عامة على الخدمة',
        'description_section' => 'الوصف',
        'pricing_details' => 'تفاصيل التسعير',
        'service_settings' => 'إعدادات الخدمة',
        'usage_statistics' => 'إحصائيات الاستخدام',
        'system_information' => 'معلومات النظام',
        'activity_history' => 'سجل الأنشطة',

        // Entry Labels
        'service_name' => 'اسم الخدمة',
        'hall' => 'القاعة',
        'name_en' => 'الاسم (الإنجليزية)',
        'name_ar' => 'الاسم (العربية)',
        'description_en' => 'الوصف (الإنجليزية)',
        'description_ar' => 'الوصف (العربية)',
        'price' => 'السعر',
        'unit' => 'الوحدة',
        'min_quantity' => 'الحد الأدنى للكمية',
        'max_quantity' => 'الحد الأقصى للكمية',
        'price_range' => 'نطاق السعر',
        'active_status' => 'حالة التفعيل',
        'required_service' => 'خدمة مطلوبة',
        'display_order' => 'ترتيب العرض',
        'image' => 'الصورة',
        'total_bookings' => 'إجمالي الحجوزات',
        'total_revenue' => 'إجمالي الإيرادات',
        'avg_quantity' => 'متوسط الكمية',
        'last_booked' => 'آخر حجز',
        'service_id' => 'معرّف الخدمة',
        'created_at' => 'تاريخ الإنشاء',
        'last_updated' => 'آخر تحديث',

        // Values
        'image_available' => 'متوفرة',
        'no_image' => 'لا توجد صورة',
        'never' => 'لم يُحجز بعد',
        'unlimited' => 'غير محدود',
        'fixed_price_display' => ':price ريال عماني (ثابت)',

        // Unit Display Labels
        'unit_per_person' => 'لكل شخص',
        'unit_per_item' => 'لكل عنصر',
        'unit_per_hour' => 'لكل ساعة',
        'unit_fixed' => 'ثابت',
    ],

    // Status Labels (used in page titles/subheadings)
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'required' => 'مطلوب',
    ],

    // Page Titles & Subheadings
    'page_titles' => [
        'create' => 'إنشاء خدمة إضافية',
        'edit' => 'تعديل الخدمة: :name',
        'view' => 'عرض الخدمة: :name',
    ],
    'page_subheadings' => [
        'create' => 'إضافة خدمة إضافية جديدة إلى قاعة',
    ],
];
