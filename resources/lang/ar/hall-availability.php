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
        'created' => 'تم إنشاء التوفر',
        'created_body' => 'تم تعيين توفر القاعة إلى :status.',
        'availability_updated' => 'تم تحديث التوفر',
        'availability_updated_body' => 'تم تحديث توفر الفترة.',
        'record_updated_body' => 'تم تحديث سجل التوفر بنجاح.',
        'price_updated' => 'تم تحديث السعر',
        'price_updated_body' => 'تم تحديث السعر المخصص بنجاح.',
        'duplication_completed' => 'تمت عملية النسخ',
        'duplication_body' => 'تم الإنشاء: :created، تم التخطي: :skipped سجلاً.',
        'block_extended' => 'تم تمديد فترة الحظر',
        'block_extended_body' => 'تم حظر :count فترة.',
        'deleted' => 'تم حذف التوفر',
        'deleted_body' => 'تم حذف سجل التوفر.',
        'slot_now_available' => 'الفترة متاحة الآن للحجز.',
        'slot_blocked' => 'تم حظر الفترة.',
        'duplicated' => 'تم نسخ التوفر',
        'duplicated_body' => 'تم إنشاء توفر جديد ليوم :date',
    ],

    // Messages
    'messages' => [
        'created' => 'تم إنشاء توفر القاعة بنجاح',
        'updated' => 'تم تحديث توفر القاعة بنجاح',
        'deleted' => 'تم حذف توفر القاعة بنجاح',
    ],

    // Status Labels
    'status' => [
        'available' => 'متاح',
        'blocked' => 'محظور',
        'days_suffix' => ' يوم',
        'week_prefix' => 'الأسبوع ',
        'guests_suffix' => ' ضيف',
    ],

    // Error / Validation Messages
    'errors' => [
        'invalid_date' => 'تاريخ غير صالح',
        'invalid_date_body' => 'لا يمكن إنشاء توفر لتواريخ ماضية.',
        'invalid_date_edit_body' => 'لا يمكن تعيين توفر لتواريخ ماضية.',
        'duplicate_slot' => 'فترة مكررة',
        'duplicate_slot_body' => 'هذه الفترة الزمنية موجودة بالفعل للقاعة والتاريخ المحددين.',
        'invalid_price' => 'سعر غير صالح',
        'invalid_price_body' => 'لا يمكن أن يكون السعر المخصص سالبًا.',
        'missing_reason' => 'السبب مفقود',
        'missing_reason_body' => 'يرجى تقديم سبب لحظر هذه الفترة.',
        'existing_bookings' => 'توجد حجوزات موجودة',
        'existing_bookings_body' => 'توجد :count حجز لهذه الفترة. قد تحتاج إلى إلغائها.',
        'pending_bookings' => 'حجوزات معلقة',
        'pending_bookings_body' => 'توجد :count حجز معلق لهذه الفترة المحظورة.',
    ],

    // Create Page
    'create_page' => [
        'title' => 'إنشاء توفر قاعة',
        'subheading' => 'تحديد توفر أو حظر فترات زمنية محددة للقاعات',
    ],

    // Edit Page
    'edit_page' => [
        'title' => 'تعديل التوفر',
        'toggle_block' => 'حظر الفترة',
        'toggle_unblock' => 'إلغاء حظر الفترة',
        'block_heading' => 'حظر هذه الفترة',
        'unblock_heading' => 'إلغاء حظر هذه الفترة',
        'block_description' => 'سيؤدي هذا إلى حظر الفترة ومنع الحجوزات الجديدة.',
        'unblock_description' => 'سيجعل هذا الفترة متاحة للحجز مرة أخرى.',
        'block_reason_label' => 'سبب الحظر',
        'view_bookings' => 'عرض الحجوزات',
        'update_price' => 'تحديث السعر',
        'default_hall_price' => 'السعر الافتراضي للقاعة',
        'price_change_reason' => 'سبب تغيير السعر',
        'leave_empty_default' => 'اتركه فارغًا لاستخدام التسعير الافتراضي للقاعة',
        'duplicate' => 'نسخ إلى تواريخ أخرى',
        'same_time_slot' => 'نفس الفترة الزمنية فقط',
        'same_time_slot_helper' => 'نسخ نفس الفترة الزمنية فقط، أو جميع الفترات الزمنية',
        'extend_block' => 'تمديد فترة الحظر',
        'extend_until' => 'تمديد حتى',
        'copy_settings' => 'نسخ إعدادات الحظر',
        'copy_settings_helper' => 'استخدام نفس السبب والملاحظات',
        'view_history' => 'عرض السجل',
        'close' => 'إغلاق',
    ],

    // View Page
    'view_page' => [
        'title' => 'عرض التوفر',
        'slot_information' => 'معلومات الفترة',
        'status_availability' => 'الحالة والتوفر',
        'pricing_information' => 'معلومات التسعير',
        'hall_details' => 'تفاصيل القاعة',
        'statistics_insights' => 'الإحصائيات والتحليلات',
        'system_information' => 'معلومات النظام',
        'hall_label' => 'القاعة',
        'date_label' => 'التاريخ',
        'time_slot_label' => 'الفترة الزمنية',
        'availability_status' => 'حالة التوفر',
        'block_reason' => 'سبب الحظر',
        'active_bookings' => 'الحجوزات النشطة',
        'notes_label' => 'ملاحظات',
        'custom_price_label' => 'السعر المخصص',
        'default_hall_price' => 'السعر الافتراضي للقاعة',
        'effective_price_label' => 'السعر الفعلي',
        'city' => 'المدينة',
        'hall_owner' => 'مالك القاعة',
        'hall_capacity' => 'سعة القاعة',
        'days_until' => 'الأيام المتبقية',
        'same_day_slots' => 'فترات نفس اليوم',
        'day_of_week' => 'يوم الأسبوع',
        'week_number' => 'رقم الأسبوع',
        'availability_id' => 'معرف التوفر',
        'created_at' => 'تاريخ الإنشاء',
        'last_updated' => 'آخر تحديث',
        'no_notes' => 'لا توجد ملاحظات إضافية',
        'using_default_price' => 'يستخدم السعر الافتراضي',
        'view_hall' => 'عرض القاعة',
        'view_bookings' => 'عرض الحجوزات',
        'duplicate' => 'نسخ',
        'duplicate_heading' => 'نسخ التوفر',
        'duplicate_description' => 'إنشاء نسخة من هذا التوفر لليوم التالي.',
        'view_duplicate' => 'عرض النسخة',
        'available_status' => '✓ متاح',
        'blocked_status' => '✗ محظور',
        'block_slot' => 'حظر الفترة',
        'unblock_slot' => 'إلغاء حظر الفترة',
        'block_modal_heading' => 'حظر هذه الفترة؟',
        'unblock_modal_heading' => 'إلغاء حظر هذه الفترة؟',
        'block_modal_description' => 'سيمنع هذا الحجوزات الجديدة لهذه الفترة الزمنية.',
        'unblock_modal_description' => 'سيجعل هذا الفترة متاحة للحجز مرة أخرى.',
        'time_slot_morning' => 'الصباح',
        'time_slot_afternoon' => 'بعد الظهر',
        'time_slot_evening' => 'المساء',
        'time_slot_full_day' => 'طوال اليوم',
        'reason_maintenance' => 'تحت الصيانة',
        'reason_blocked' => 'محظور بواسطة المالك',
        'reason_custom' => 'حظر مخصص',
        'reason_holiday' => 'عطلة',
        'reason_na' => 'غير متوفر',
    ],
];
