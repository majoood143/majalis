<?php

return [
    // Resource Labels
    'singular' => 'مالك القاعة',
    'plural' => 'مالكو القاعات',
    'navigation_label' => 'مالكو القاعات',
    
    // Actions
    'actions' => [
        'export' => 'تصدير المالكين',
        'export_modal_heading' => 'تصدير مالكي القاعات',
        'export_modal_description' => 'تصدير جميع بيانات مالكي القاعات إلى ملف CSV.',
        'bulk_verify' => 'التحقق بالجملة',
        'bulk_verify_modal_heading' => 'التحقق من جميع المالكين المعلقين',
        'bulk_verify_modal_description' => 'سيتم التحقق من جميع مالكي القاعات غير الموثقين.',
        'send_notification' => 'إرسال إشعار',
        'generate_report' => 'إنشاء تقرير',
        'verify' => 'تحقق',
        'reject' => 'رفض',
        'download' => 'تحميل الملف',
        'download_report' => 'تحميل التقرير',
        'view' => 'عرض',
        'edit' => 'تعديل',
        'delete' => 'حذف',
    ],

    // Fields
    'fields' => [
        'user_id' => 'المالك',
        'business_name' => 'اسم العمل',
        'business_name_ar' => 'اسم العمل (عربي)',
        'commercial_registration' => 'السجل التجاري',
        'tax_number' => 'الرقم الضريبي',
        'business_phone' => 'هاتف العمل',
        'business_email' => 'بريد العمل الإلكتروني',
        'business_address' => 'عنوان العمل',
        'business_address_ar' => 'عنوان العمل (عربي)',
        'bank_name' => 'اسم البنك',
        'bank_account_name' => 'اسم الحساب البنكي',
        'bank_account_number' => 'رقم الحساب البنكي',
        'iban' => 'رقم الآيبان',
        'commission_type' => 'نوع العمولة',
        'commission_value' => 'قيمة العمولة',
        'is_verified' => 'تم التحقق',
        'is_active' => 'نشط',
        'verification_notes' => 'ملاحظات التحقق',
        'notes' => 'ملاحظات',
        'filter' => 'إرسال إلى',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'rejection_reason' => 'سبب الرفض',
    ],

    // Options
    'options' => [
        'all' => 'جميع المالكين',
        'verified' => 'الموثقين فقط',
        'unverified' => 'غير الموثقين فقط',
        'active' => 'النشطين فقط',
        'percentage' => 'نسبة مئوية',
        'fixed' => 'مبلغ ثابت',
        'verify' => 'تحقق',
        'reject' => 'رفض',
    ],

    // Tabs
    'tabs' => [
        'all' => 'جميع المالكين',
        'pending_verification' => 'بانتظار التحقق',
        'verified' => 'موثق',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'custom_commission' => 'عمولة مخصصة',
        'with_halls' => 'بملك قاعات',
        'without_halls' => 'بدون قاعات',
        'incomplete_documents' => 'وثائق غير مكتملة',
        'business_info' => 'معلومات العمل',
        'contact' => 'جهات الاتصال',
        'bank_details' => 'تفاصيل البنك',
        'documents' => 'الوثائق',
        'verification' => 'التحقق',
        'commission' => 'العمولة',
    ],

    // Columns
    'columns' => [
        'owner_name' => 'اسم المالك',
        'business_name' => 'اسم العمل',
        'commercial_registration' => 'السجل التجاري',
        'business_phone' => 'هاتف العمل',
        'is_verified' => 'موثق',
        'is_active' => 'نشط',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Export Headers
    'export' => [
        'id' => 'المعرف',
        'owner_name' => 'اسم المالك',
        'business_name' => 'اسم العمل',
        'business_name_ar' => 'اسم العمل (عربي)',
        'commercial_registration' => 'السجل التجاري',
        'tax_number' => 'الرقم الضريبي',
        'business_phone' => 'هاتف العمل',
        'business_email' => 'بريد العمل الإلكتروني',
        'bank_name' => 'اسم البنك',
        'iban' => 'رقم الآيبان',
        'commission_type' => 'نوع العمولة',
        'commission_value' => 'قيمة العمولة',
        'verified' => 'موثق',
        'active' => 'نشط',
        'verified_at' => 'تاريخ التحقق',
        'total_halls' => 'إجمالي القاعات',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Info List
    'infolist' => [
        'business_information' => 'معلومات العمل',
        'owner' => 'المالك',
        'contact_information' => 'معلومات الاتصال',
        'bank_details' => 'تفاصيل البنك',
        'verification_status' => 'حالة التحقق',
        'verified' => 'موثق',
        'pending' => 'قيد الانتظار',
        'verified_by' => 'تم التحقق بواسطة',
        'statistics' => 'الإحصائيات',
        'total_halls' => 'إجمالي القاعات',
        'active_halls' => 'القاعات النشطة',
        'total_bookings' => 'إجمالي الحجوزات',
        'total_revenue' => 'إجمالي الإيرادات',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'تم التصدير بنجاح',
        'export_success_body' => 'تم تصدير مالكي القاعات بنجاح.',
        'export_error' => 'فشل التصدير',
        'bulk_verify_success' => 'تم التحقق بالجملة',
        'bulk_verify_success_body' => 'تم التحقق من :count مالك.',
        'notification_sent' => 'تم إرسال الإشعارات',
        'notification_sent_body' => 'تم إرسال :count إشعار.',
        'report_generated' => 'تم إنشاء التقرير بنجاح',
        'report_generated_body' => 'تم إنشاء تقرير جميع مالكي القاعات.',
        'report_failed' => 'فشل إنشاء التقرير',
        'owner_verified' => 'تم التحقق من المالك',
        'owner_rejected' => 'تم رفض المالك',
        'owner_updated' => 'تم تحديث المالك',
        'owner_deleted' => 'تم حذف المالك',
        'update_error' => 'فشلت العملية',
    ],

    // Filters
    'filters' => [
        'verified' => 'موثق',
        'active' => 'نشط',
    ],

    // Common
    'yes' => 'نعم',
    'no' => 'لا',
    'note' => 'اتركه فارغاً لاستخدام إعدادات العمولة العامة',
    'n_a' => 'غير متوفر',
    'not_verified' => 'غير موثق',
];
