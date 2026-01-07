<?php

return [
    // Resource Labels
    'singular' => 'تقييم',
    'plural' => 'التقييمات',
    'navigation_label' => 'التقييمات',
    
    // Sections
    'sections' => [
        'review_information' => 'معلومات التقييم',
        'detailed_ratings' => 'التقييمات التفصيلية',
        'moderation' => 'المراجعة',
        'owner_response' => 'رد المالك',
    ],

    // Fields
    'fields' => [
        'hall' => 'القاعة',
        'booking' => 'الحجز',
        'user' => 'المستخدم',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'cleanliness_rating' => 'تقييم النظافة',
        'service_rating' => 'تقييم الخدمة',
        'value_rating' => 'تقييم القيمة',
        'location_rating' => 'تقييم الموقع',
        'is_approved' => 'مقبول',
        'is_featured' => 'مميز',
        'admin_notes' => 'ملاحظات الإدارة',
        'owner_response' => 'رد المالك',
        'owner_response_at' => 'تاريخ رد المالك',
        'rejection_reason' => 'سبب الرفض',
    ],

    // Ratings
    'ratings' => [
        '1_star' => '⭐ نجمة واحدة',
        '2_stars' => '⭐⭐ نجمتان',
        '3_stars' => '⭐⭐⭐ ثلاث نجوم',
        '4_stars' => '⭐⭐⭐⭐ أربع نجوم',
        '5_stars' => '⭐⭐⭐⭐⭐ خمس نجوم',
    ],

    // Status
    'status' => [
        'approved' => 'مقبول',
        'pending' => 'قيد الانتظار',
        'featured' => 'مميز',
        'not_featured' => 'غير مميز',
    ],

    // Actions
    'actions' => [
        'export' => 'تصدير التقييمات',
        'export_modal_heading' => 'تصدير التقييمات',
        'export_modal_description' => 'تصدير جميع بيانات التقييمات إلى ملف CSV.',
        'bulk_approve' => 'الموافقة على المعلقة',
        'bulk_approve_modal_heading' => 'الموافقة على التقييمات المعلقة',
        'bulk_approve_modal_description' => 'سيتم الموافقة على جميع التقييمات المعلقة.',
        'approve' => 'قبول',
        'reject' => 'رفض',
        'download' => 'تحميل الملف',
    ],

    // Tabs
    'tabs' => [
        'all' => 'جميع التقييمات',
        'pending' => 'بانتظار الموافقة',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        '5_stars' => '5 نجوم',
        'low_rated' => 'تقييم منخفض (≤2)',
        'with_response' => 'بها رد',
    ],

    // Columns
    'columns' => [
        'hall' => 'القاعة',
        'user' => 'المستخدم',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'is_approved' => 'مقبول',
        'is_featured' => 'مميز',
        'owner_response' => 'رد المالك',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Filters
    'filters' => [
        'hall' => 'القاعة',
        'rating' => 'التقييم',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        'has_owner_response' => 'يحتوي على رد المالك',
    ],

    // Export Headers
    'export' => [
        'id' => 'المعرف',
        'hall' => 'القاعة',
        'user' => 'المستخدم',
        'booking' => 'الحجز',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'cleanliness' => 'النظافة',
        'service' => 'الخدمة',
        'value' => 'القيمة',
        'location' => 'الموقع',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        'owner_response' => 'رد المالك',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'تم التصدير بنجاح',
        'export_success_body' => 'تم تصدير بيانات التقييمات بنجاح.',
        'export_error' => 'فشل التصدير',
        'bulk_approve_success' => 'تمت الموافقة على التقييمات',
        'bulk_approve_success_body' => 'تمت الموافقة على :count تقييم.',
        'update_error' => 'فشلت العملية',
    ],

    // Common
    'yes' => 'نعم',
    'no' => 'لا',
    'n_a' => 'غير متوفر',
    'placeholder' => [
        'no_response' => 'لا يوجد رد',
    ],
];
