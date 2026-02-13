<?php

declare(strict_types=1);

/**
 * Arabic translations for Service Fee Settings.
 *
 * File: resources/lang/ar/service-fee.php
 *
 * @package Lang\Ar
 */
return [

    // ── تسميات المورد ──
    'singular'         => 'رسوم الخدمة',
    'plural'           => 'رسوم الخدمات',
    'navigation_label' => 'رسوم الخدمات',
    'navigation_group' => 'المالية',
    'subheading'       => 'إدارة رسوم الخدمة المفروضة على الحجوزات والظاهرة للعملاء',

    // ── أقسام النموذج ──
    'fee_scope'        => 'نطاق الرسوم',
    'scope_description'=> 'اختر قاعة محددة أو مالك، أو اتركهما فارغين لرسوم عامة',
    'fee_details'      => 'تفاصيل الرسوم',
    'validity_period'  => 'فترة السريان',

    // ── حقول النموذج ──
    'hall'             => 'القاعة (اختياري)',
    'owner'            => 'المالك (اختياري)',
    'name_en'          => 'الاسم (الإنجليزية)',
    'name_ar'          => 'الاسم (العربية)',
    'fee_type'         => 'نوع الرسوم',
    'fee_value'        => 'قيمة الرسوم',
    'description_en'   => 'الوصف (الإنجليزية)',
    'description_ar'   => 'الوصف (العربية)',
    'effective_from'   => 'ساري من',
    'effective_to'     => 'ساري إلى',
    'is_active'        => 'نشط',

    // ── مساعدات الحقول ──
    'hall_helper'           => 'اتركه فارغًا لرسوم مستوى المالك أو الرسوم العامة',
    'owner_helper'          => 'اتركه فارغًا للرسوم العامة',
    'scope_note_title'      => 'أولوية النطاق',
    'scope_note'            => 'الأولوية: خاصة بالقاعة > خاصة بالمالك > عامة. يتم تطبيق الرسوم الأعلى أولوية فقط.',
    'effective_from_helper' => 'اتركه فارغًا للتأثير الفوري',
    'effective_to_helper'   => 'اتركه فارغًا لفترة غير محددة',

    // ── أنواع الرسوم ──
    'percentage' => 'نسبة مئوية',
    'fixed'      => 'مبلغ ثابت',

    // ── أعمدة الجدول ──
    'scope'      => 'النطاق',
    'value'      => 'القيمة',
    'created_at' => 'تاريخ الإنشاء',

    // ── أنواع النطاق ──
    'global'         => 'عام',
    'hall_specific'  => 'القاعة: :name',
    'owner_specific' => 'المالك: :name',

    // ── الفلاتر ──
    'filters' => [
        'scope_type' => 'نوع النطاق',
        'active'     => 'نشط',
        'global'     => 'عام',
        'owner'      => 'خاص بالمالك',
        'hall'       => 'خاص بالقاعة',
    ],

    // ── التبويبات ──
    'tabs' => [
        'all'            => 'الكل',
        'active'         => 'نشط',
        'inactive'       => 'غير نشط',
        'global'         => 'عام',
        'hall_specific'  => 'خاص بالقاعة',
        'owner_specific' => 'خاص بالمالك',
    ],

    // ── الإجراءات ──
    'edit'            => 'تعديل',
    'delete'          => 'حذف',
    'create'          => 'إنشاء رسوم خدمة',
    'cleanup_expired' => 'تنظيف المنتهية',

    // ── النوافذ المنبثقة ──
    'cleanup_modal_title' => 'حذف رسوم الخدمة المنتهية',
    'cleanup_modal_desc'  => 'سيتم حذف جميع إعدادات رسوم الخدمة المنتهية وغير النشطة نهائيًا.',
    'cleanup_done'        => 'تم التنظيف',
    'cleanup_done_body'   => 'تم حذف :count من رسوم الخدمة المنتهية.',

    // ── الإشعارات ──
    'created'              => 'تم إنشاء رسوم الخدمة',
    'created_body'         => 'تم إنشاء رسوم خدمة :scope جديدة بنجاح.',
    'updated'              => 'تم تحديث رسوم الخدمة',
    'deleted'              => 'تم حذف رسوم الخدمة',
    'scope_adjusted'       => 'تم تعديل النطاق',
    'scope_adjusted_body'  => 'تم اختيار كل من القاعة والمالك. سيتم إنشاء رسوم خاصة بالقاعة.',
    'invalid_value'        => 'قيمة رسوم غير صالحة',
    'percentage_max'       => 'لا يمكن أن تتجاوز رسوم النسبة المئوية 100%.',
    'value_positive'       => 'لا يمكن أن تكون قيمة الرسوم سالبة.',
    'invalid_dates'        => 'نطاق تاريخ غير صالح',
    'date_range_error'     => 'يجب أن يكون تاريخ البداية قبل تاريخ النهاية.',
    'overlap_warning'      => 'تم اكتشاف رسوم متداخلة',
    'overlap_warning_body' => 'يوجد :count إعداد(ات) رسوم نشطة بنفس النطاق. سيتم تطبيق أول تطابق فقط.',

    // ── تسميات العميل (تظهر في عرض الحجز) ──
    'customer_label'       => 'رسوم الخدمة',
    'customer_description' => 'رسوم خدمة المنصة',
];
