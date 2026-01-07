<?php

return [
    // Resource Labels
    'singular' => 'توفر القاعة',
    'plural' => 'توفر القاعات',
    'navigation_label' => 'توفر القاعات',
    
    // Form Sections
    'availability_details' => 'تفاصيل التوفر',
    'block_reason' => 'سبب الحظر',
    'custom_pricing' => 'التسعير المخصص',
    
    // Form Fields
    'hall' => 'القاعة',
    'date' => 'التاريخ',
    'time_slot' => 'الفترة الزمنية',
    'is_available' => 'متاح',
    'reason' => 'السبب',
    'notes' => 'ملاحظات',
    'custom_price' => 'السعر المخصص',
    
    // Time Slot Options
    'time_slots' => [
        'morning' => 'الصباح (8 صباحًا - 12 ظهرًا)',
        'afternoon' => 'بعد الظهر (12 ظهرًا - 5 مساءً)',
        'evening' => 'المساء (5 مساءً - 11 مساءً)',
        'full_day' => 'طوال اليوم (8 صباحًا - 11 مساءً)',
    ],
    
    // Time Slot Short Labels
    'time_slots_short' => [
        'morning' => 'الصباح',
        'afternoon' => 'بعد الظهر',
        'evening' => 'المساء',
        'full_day' => 'طوال اليوم',
    ],
    
    // Reason Options
    'reasons' => [
        'maintenance' => 'تحت الصيانة',
        'blocked' => 'محظور بواسطة المالك',
        'holiday' => 'عطلة',
        'custom' => 'حظر مخصص',
    ],
    
    // Field Helpers
    'is_available_helper' => 'قم بإلغاء التحديد لحظر هذه الفترة',
    'custom_pricing_description' => 'تجاوز سعر القاعة الافتراضي لهذا التاريخ والفترة المحددة',
    'custom_price_helper' => 'اتركه فارغًا لاستخدام التسعير الافتراضي',
    
    // Table Columns
    'hall_name' => 'القاعة',
    'time_slot_label' => 'الفترة الزمنية',
    'reason_label' => 'السبب',
    'effective_price' => 'السعر الفعلي',
    'created_at' => 'تاريخ الإنشاء',
    
    // Placeholders
    'unnamed_hall' => 'قاعة بدون اسم',
    'hall_deleted' => 'تم حذف القاعة',
    'default_price' => 'افتراضي',
    'not_applicable' => 'غير متوفر',
    
    // Filters
    'filters' => [
        'hall' => 'القاعة',
        'time_slot' => 'الفترة الزمنية',
        'available' => 'متاح',
        'date_range' => 'نطاق التاريخ',
        'from' => 'من',
        'until' => 'إلى',
        'available_only' => 'المتاحة فقط',
        'blocked_only' => 'المحظورة فقط',
    ],
    
    // Table Actions
    'table_actions' => [
        'view' => 'عرض',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'toggle' => 'تبديل التوفر',
        'block' => 'حظر',
        'unblock' => 'إلغاء الحظر',
    ],
    
    // Bulk Actions
    'bulk_actions' => [
        'block_selected' => 'حظر المحدد',
        'unblock_selected' => 'إلغاء حظر المحدد',
    ],
    
    // List Page - Header Actions
    'list_actions' => [
        'create' => 'إنشاء توفر',
        'bulk_block' => 'حظر التواريخ بالجملة',
        'generate_availability' => 'توليد التوفر',
        'export_calendar' => 'تصدير التقويم',
        'cleanup_past' => 'تنظيف التواريخ السابقة',
    ],
    
    // List Page - Tabs
    'tabs' => [
        'all' => 'الكل',
        'available' => 'متاح',
        'blocked' => 'محظور',
        'today' => 'اليوم',
        'this_week' => 'هذا الأسبوع',
        'this_month' => 'هذا الشهر',
        'custom_pricing' => 'التسعير المخصص',
        'maintenance' => 'الصيانة',
        'past' => 'سابق',
    ],
    
    // Bulk Block Modal
    'bulk_block_modal' => [
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'time_slots' => 'الفترات الزمنية',
        'time_slot_options' => [
            'morning' => 'الصباح (8 صباحًا - 12 ظهرًا)',
            'afternoon' => 'بعد الظهر (12 ظهرًا - 5 مساءً)',
            'evening' => 'المساء (5 مساءً - 11 مساءً)',
            'full_day' => 'طوال اليوم (8 صباحًا - 11 مساءً)',
        ],
        'block_reason' => 'سبب الحظر',
    ],
    
    // Generate Availability Modal
    'generate_availability_modal' => [
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'time_slots_to_generate' => 'الفترات الزمنية لتوليدها',
        'skip_existing' => 'تخطي السجلات الموجودة',
        'skip_existing_helper' => 'إنشاء التوفر فقط للتواريخ غير الموجودة مسبقًا',
    ],
    
    // Export Calendar Modal
    'export_calendar_modal' => [
        'hall_optional' => 'القاعة (اختياري)',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
    ],
    
    // Cleanup Modal
    'cleanup_modal' => [
        'heading' => 'حذف سجلات التوفر السابقة',
        'description' => 'سيؤدي هذا إلى حذف جميع سجلات التوفر قبل اليوم بشكل دائم.',
    ],
    
    // Export CSV Headers
    'export_headers' => [
        'hall' => 'القاعة',
        'date' => 'التاريخ',
        'day' => 'اليوم',
        'time_slot' => 'الفترة الزمنية',
        'available' => 'متاح',
        'reason' => 'السبب',
        'custom_price' => 'السعر المخصص',
        'effective_price' => 'السعر الفعلي',
        'notes' => 'ملاحظات',
    ],
    
    // Export Values
    'export_values' => [
        'yes' => 'نعم',
        'no' => 'لا',
        'default' => 'افتراضي',
    ],
    
    // Notifications
    'notifications' => [
        'bulk_block_completed' => 'تم إكمال الحظر بالجملة',
        'availability_generated' => 'تم توليد التوفر',
        'calendar_exported' => 'تم تصدير التقويم',
        'cleanup_completed' => 'تم إكمال التنظيف',
        'download' => 'تحميل الملف',
        
        'created_updated' => 'تم الإنشاء: :created، تم التحديث: :updated سجل توفر.',
        'created_skipped' => 'تم الإنشاء/التحديث: :created، تم التخطي: :skipped سجلاً.',
        'deleted_records' => 'تم حذف :count سجل توفر سابق.',
    ],
    
    // Messages
    'messages' => [
        'created' => 'تم إنشاء توفر القاعة بنجاح',
        'updated' => 'تم تحديث توفر القاعة بنجاح',
        'deleted' => 'تم حذف توفر القاعة بنجاح',
    ],
];