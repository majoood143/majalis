<?php

return [
    // Resource Labels
    'singular' => 'إعداد العمولة',
    'plural' => 'إعدادات العمولات',
    'navigation_label' => 'إعدادات العمولات',

    // Form Sections
    'commission_scope' => 'نطاق العمولة',
    'scope_description' => 'اختر إما قاعة محددة، مالك، أو اترك كليهما فارغًا للإعدادات العامة',
    'commission_details' => 'تفاصيل العمولة',
    'validity_period' => 'فترة السريان',

    // Form Fields
    'hall' => 'القاعة (اختياري)',
    'owner' => 'المالك (اختياري)',
    'name_en' => 'الاسم (الإنجليزية)',
    'name_ar' => 'الاسم (العربية)',
    'commission_type' => 'نوع العمولة',
    'commission_value' => 'قيمة العمولة',
    'description_en' => 'الوصف (الإنجليزية)',
    'description_ar' => 'الوصف (العربية)',
    'effective_from' => 'ساري من',
    'effective_to' => 'ساري إلى',
    'is_active' => 'نشط',

    // Field Helpers
    'hall_helper' => 'اتركه فارغًا لعمولة مستوى المالك أو العمولة العامة',
    'owner_helper' => 'اتركه فارغًا للعمولة العامة',
    'scope_note' => 'الأولوية: خاصة بالقاعة > خاصة بالمالك > عامة',
    'effective_from_helper' => 'اتركه فارغًا للتأثير الفوري',
    'effective_to_helper' => 'اتركه فارغًا لفترة غير محددة',

    // Commission Types
    'percentage' => 'نسبة مئوية',
    'fixed' => 'مبلغ ثابت',

    // Table Columns
    'scope' => 'النطاق',
    'value' => 'القيمة',
    'created_at' => 'تاريخ الإنشاء',

    // Scope Types
    'global' => 'عام',
    'hall_specific' => 'القاعة: :name',
    'owner_specific' => 'المالك: :name',

    // Filters
    'filters' => [
        'scope_type' => 'نوع النطاق',
        'active' => 'نشط',
        'global' => 'عام',
        'owner' => 'خاص بالمالك',
        'hall' => 'خاص بالقاعة',
    ],

    // Actions
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'create' => 'إنشاء إعداد عمولة',
    'export' => 'تصدير',
    'bulk_activate' => 'تفعيل جماعي',
    'cleanup_expired' => 'تنظيف المنتهية الصلاحية',

    'tabs' => [
        'all' => 'الكل',
        'global' => 'عام',
        'owner_specific' => 'خاص بالمالك',
        'hall_specific' => 'خاص بالقاعة',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'percentage' => 'نسبة مئوية',
        'fixed' => 'مبلغ ثابت',
        'expired' => 'منتهية الصلاحية',
        'expiring_soon' => 'ستنتهي قريبًا',
    ],

    // Messages
    'created' => 'تم إنشاء إعداد العمولة بنجاح',
    'updated' => 'تم تحديث إعداد العمولة بنجاح',
    'deleted' => 'تم حذف إعداد العمولة بنجاح',
];
