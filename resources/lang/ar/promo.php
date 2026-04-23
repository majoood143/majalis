<?php

declare(strict_types=1);

return [
    // Navigation
    'navigation_group' => 'العروض الترويجية',
    'navigation_label' => 'رموز الخصم',
    'singular'         => 'رمز خصم',
    'plural'           => 'رموز الخصم',

    // Form sections
    'section_details'  => 'تفاصيل الرمز',
    'section_discount' => 'الخصم',
    'section_validity' => 'الصلاحية',
    'section_scope'    => 'النطاق (القاعة)',

    // Form fields
    'field_code'                 => 'الرمز',
    'field_name'                 => 'الاسم / التسمية',
    'field_description'          => 'الوصف',
    'field_discount_type'        => 'نوع الخصم',
    'field_discount_value_pct'   => 'نسبة الخصم (%)',
    'field_discount_value_fixed' => 'مبلغ الخصم',
    'field_valid_from'           => 'صالح من',
    'field_valid_until'          => 'صالح حتى',
    'field_max_uses'             => 'الحد الأقصى للاستخدام',
    'field_max_uses_helper'      => 'اتركه فارغاً للاستخدام غير المحدود.',
    'field_is_active'            => 'مفعّل',
    'field_hall'                 => 'القاعة (اختياري)',
    'field_hall_helper'          => 'اتركه فارغاً للسماح باستخدام الرمز على جميع القاعات.',
    'field_hall_helper_owner'    => 'حدد القاعة التي ينطبق عليها هذا الرمز.',

    // Discount types
    'type_percentage' => 'نسبة مئوية (%)',
    'type_fixed'      => 'مبلغ ثابت',

    // Table columns
    'col_code'        => 'الرمز',
    'col_name'        => 'الاسم',
    'col_discount'    => 'الخصم',
    'col_hall'        => 'القاعة',
    'col_used'        => 'الاستخدامات',
    'col_valid_until' => 'ينتهي',
    'col_active'      => 'مفعّل',

    // Filters
    'filter_active' => 'حالة التفعيل',
    'filter_type'   => 'نوع الخصم',
    'filter_hall'   => 'القاعة',

    // Values
    'all_halls' => 'جميع القاعات',
    'no_expiry' => 'بدون انتهاء',

    // Frontend labels
    'label'          => 'رمز الخصم',
    'placeholder'    => 'أدخل رمز الخصم',
    'apply'          => 'تطبيق',
    'applied'        => '✓ تم التطبيق',
    'checking'       => 'جارٍ التحقق...',
    'discount_label' => 'خصم الكوبون',
    'error'          => 'تعذّر التحقق من رمز الخصم. يرجى المحاولة مجدداً.',

    // Validation messages
    'invalid_code'      => 'رمز الخصم غير صحيح.',
    'code_inactive'     => 'رمز الخصم هذا غير مفعّل.',
    'code_not_started'  => 'رمز الخصم هذا لم يصبح صالحاً بعد.',
    'code_expired'      => 'انتهت صلاحية رمز الخصم هذا.',
    'code_used_up'      => 'وصل رمز الخصم هذا إلى الحد الأقصى للاستخدام.',
    'code_applied'      => 'تم تطبيق رمز الخصم! وفّرت :amount ريال عماني.',

    // Bookings relation manager
    'rel_bookings_title'       => 'الحجوزات التي استخدمت هذا الرمز',
    'rel_col_booking_number'   => 'رقم الحجز',
    'rel_col_customer'         => 'العميل',
    'rel_col_hall'             => 'القاعة',
    'rel_col_booking_date'     => 'تاريخ الحجز',
    'rel_col_discount'         => 'الخصم',
    'rel_col_total'            => 'الإجمالي',
    'rel_col_status'           => 'الحالة',
    'rel_col_payment_status'   => 'الدفع',
    'rel_col_created_at'       => 'تاريخ الإنشاء',
    'rel_export_bookings'      => 'تصدير الحجوزات',

    // Export
    'export_btn'           => 'تصدير CSV',
    'export_col_email'     => 'البريد الإلكتروني',
    'export_col_phone'     => 'الهاتف',
    'export_success_title' => 'الملف جاهز',
    'export_success_body'  => 'تم إنشاء الملف :filename.',
    'export_download'      => 'تحميل',
];
