<?php

declare(strict_types=1);

return [
    // التنقل والتسميات
    'navigation_label' => 'الصفحات',
    'plural_label' => 'الصفحات',
    'singular_label' => 'صفحة',

    // عناوين الأقسام
    'sections' => [
        'main_content' => 'المحتوى الرئيسي',
        'main_content_description' => 'عنوان الصفحة والمعرف والمعلومات الأساسية',
        'english_content' => 'المحتوى الإنجليزي',
        'arabic_content' => 'المحتوى العربي',
        'seo' => 'إعدادات تحسين محركات البحث',
        'seo_description' => 'العلامات الوصفية لمحركات البحث',
        'settings' => 'إعدادات العرض',
    ],

    // حقول النموذج
    'fields' => [
        'slug' => 'المعرف',
        'slug_helper' => 'معرف متوافق مع الرابط (مثال: about-us)',
        'title_en' => 'العنوان (إنجليزي)',
        'title_ar' => 'العنوان (عربي)',
        'content_en' => 'المحتوى (إنجليزي)',
        'content_ar' => 'المحتوى (عربي)',
        'meta_title_en' => 'عنوان تحسين محركات البحث (إنجليزي)',
        'meta_title_ar' => 'عنوان تحسين محركات البحث (عربي)',
        'meta_title_helper' => 'الموصى به: ٥٠-٦٠ حرف',
        'meta_description_en' => 'وصف تحسين محركات البحث (إنجليزي)',
        'meta_description_ar' => 'وصف تحسين محركات البحث (عربي)',
        'meta_description_helper' => 'الموصى به: ١٥٠-١٦٠ حرف',
        'is_active' => 'نشطة',
        'is_active_helper' => 'الصفحة مرئية للمستخدمين',
        'order' => 'ترتيب العرض',
        'order_helper' => 'الأرقام الأقل تظهر أولاً',
        'show_in_header' => 'إظهار في الترويسة',
        'show_in_header_helper' => 'عرض الرابط في قائمة التنقل العلوية',
        'show_in_footer' => 'إظهار في التذييل',
        'show_in_footer_helper' => 'عرض الرابط في قائمة التنقل السفلية',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    // المرشحات
    'filters' => [
        'is_active' => 'الحالة',
        'all' => 'جميع الصفحات',
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
        'show_in_footer' => 'إظهار في التذييل',
        'show_in_header' => 'إظهار في الترويسة',
    ],

    // الواجهة الأمامية
    'last_updated' => 'آخر تحديث',
    'need_help' => 'تحتاج مساعدة؟',
    'contact_description' => 'فريق الدعم لدينا جاهز لمساعدتك',
    'contact_us' => 'اتصل بنا',
    'related_pages' => 'صفحات ذات صلة',
    'read_more' => 'اقرأ المزيد',
];
