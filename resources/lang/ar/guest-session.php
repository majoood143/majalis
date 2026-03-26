<?php

return [
    // Resource labels
    'singular'          => 'جلسة ضيف',
    'plural'            => 'جلسات الضيوف',
    'navigation_label'  => 'جلسات الضيوف',

    // Sections
    'guest_information'    => 'معلومات الضيف',
    'session_status'       => 'حالة الجلسة',
    'booking_information'  => 'معلومات الحجز',
    'security_information' => 'معلومات الأمان',
    'timestamps'           => 'التواريخ',

    // Fields
    'name'           => 'الاسم',
    'email'          => 'البريد الإلكتروني',
    'phone'          => 'الهاتف',
    'session_token'  => 'رمز الجلسة',
    'status'         => 'الحالة',
    'is_verified'    => 'تم التحقق',
    'otp_attempts'   => 'محاولات OTP',
    'verified_at'    => 'وقت التحقق',
    'expires_at'     => 'وقت الانتهاء',
    'otp_expires_at' => 'انتهاء صلاحية OTP',
    'hall'           => 'المجلس',
    'booking'        => 'الحجز',
    'booking_data'   => 'بيانات الحجز',
    'ip_address'     => 'عنوان IP',
    'user_agent'     => 'المتصفح',
    'created_at'     => 'تاريخ الإنشاء',
    'updated_at'     => 'تاريخ التعديل',

    // Statuses
    'status_pending'   => 'بانتظار التحقق',
    'status_verified'  => 'تم التحقق',
    'status_booking'   => 'إنشاء حجز',
    'status_payment'   => 'معالجة الدفع',
    'status_completed' => 'مكتمل',
    'status_expired'   => 'منتهي الصلاحية',
    'status_cancelled' => 'ملغي',

    // Filters
    'verified_only'   => 'المتحقق منها فقط',
    'unverified_only' => 'غير المتحقق منها فقط',
    'filter_expired'  => 'الجلسات المنتهية',
    'filter_active'   => 'الجلسات النشطة',

    // Tabs
    'tabs' => [
        'all'       => 'الكل',
        'active'    => 'نشطة',
        'pending'   => 'قيد الانتظار',
        'verified'  => 'تم التحقق',
        'completed' => 'مكتملة',
        'expired'   => 'منتهية',
        'cancelled' => 'ملغاة',
    ],

    // Hard delete actions
    'hard_delete'                  => 'حذف نهائي',
    'hard_delete_bulk'             => 'حذف نهائي للمحدد',
    'hard_delete_heading'          => 'حذف الجلسة نهائياً',
    'hard_delete_description'      => 'سيتم حذف سجل الجلسة بشكل دائم. لا يمكن التراجع عن هذا الإجراء.',
    'hard_delete_bulk_heading'     => 'حذف الجلسات نهائياً',
    'hard_delete_bulk_description' => 'سيتم حذف سجلات الجلسات المحددة بشكل دائم. لا يمكن التراجع عن هذا الإجراء.',
    'hard_delete_confirm'          => 'نعم، حذف نهائياً',
];
