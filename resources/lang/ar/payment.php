<?php

return [

    'title' => 'المدفوعات',
    'title_singular' => 'الدفع',
    'breadcrumb' => 'المدفوعات',
    'singleton' => 'الدفع',
    'plural' => 'المدفوعات',
    'navigation_label' => 'المدفوعات',

    // Sections
    'sections' => [
        'payment_information' => 'معلومات الدفع',
        'refund_information' => 'معلومات الاسترداد',
        'failure_information' => 'معلومات الفشل',
        'gateway_response' => 'رد بوابة الدفع',
        'timestamps' => 'الطوابع الزمنية',
        'refund_details' => 'تفاصيل الاسترداد',
        'export_options' => 'خيارات التصدير',
        'report_period' => 'فترة التقرير',
        'email_details' => 'تفاصيل البريد الإلكتروني',
    ],

    // Fields
    'fields' => [
        'payment_reference' => 'مرجع الدفع',
        'booking' => 'الحجز',
        'transaction_id' => 'معرف المعاملة',
        'amount' => 'المبلغ',
        'currency' => 'العملة',
        'status' => 'الحالة',
        'payment_method' => 'طريقة الدفع',
        'refund_amount' => 'مبلغ الاسترداد',
        'refund_reason' => 'سبب الاسترداد',
        'failure_reason' => 'سبب الفشل',
        'gateway_response' => 'رد بوابة الدفع',
        'paid_at' => 'تم الدفع في',
        'failed_at' => 'فشل في',
        'refunded_at' => 'تم الاسترداد في',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'refund_type' => 'نوع الاسترداد',
        'refund_amount_input' => 'مبلغ الاسترداد',
        'refund_reason_select' => 'سبب الاسترداد',
        'additional_notes' => 'ملاحظات إضافية',
        'notify_customer' => 'إشعار العميل',
        'status_filter' => 'تصفية حسب الحالة',
        'format' => 'التنسيق',
        'include_booking_details' => 'تضمين تفاصيل الحجز',
        'metrics' => 'تضمين المقاييس',
        'reconcile_date' => 'مطابقة للتاريخ',
        'auto_update' => 'تحديث التطابقات تلقائياً',
        'age' => 'فشل خلال',
        'retry_reason' => 'سبب إعادة المحاولة (اختياري)',
        'pending_for' => 'معلق لأكثر من',
        'customer_email' => 'البريد الإلكتروني للعميل',
        'send_admin_copy' => 'إرسال نسخة للمسؤول',
        'custom_message' => 'رسالة مخصصة (اختياري)',
        'payment_date' => 'تاريخ الدفع',
    ],

    // Status
    'status' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'canceled' => 'ملغي',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'refunded' => 'تم الاسترداد',
        'partially_refunded' => 'تم الاسترداد جزئياً',
        'refund_in_progress' => 'جاري الاسترداد',
        'retrying' => 'إعادة المحاولة',
        'reconciliation_pending' => 'مطابقة معلقة',

    ],

    // Actions
    'actions' => [
        'create' => 'إنشاء دفعة',
        'export' => 'تصدير المدفوعات',
        'financial_report' => 'تقرير مالي',
        'reconcile' => 'مطابقة المدفوعات',
        'retry_failed' => 'إعادة محاولة الفاشلة',
        'send_reminders' => 'إرسال تذكيرات',
        'refund' => 'معالجة الاسترداد',
        'process_refund' => 'معالجة الاسترداد',
        'download' => 'تحميل الملف',
        'view' => 'عرض',
        'edit' => 'تعديل',
        'receipt' => 'الإيصال',
        'download_receipt' => 'تحميل الإيصال',
        'print_receipt' => 'طباعة الإيصال',
        'email_receipt' => 'إرسال الإيصال بالبريد',
        'send_email' => 'إرسال البريد',
    ],

    // Options
    'options' => [
        'all_statuses' => 'جميع الحالات',
        'paid_only' => 'المدفوعة فقط',
        'pending_only' => 'المعلقة فقط',
        'failed_only' => 'الفاشلة فقط',
        'refunded_only' => 'المستردة فقط',
        'partially_refunded' => 'المستردة جزئياً',
        'csv_format' => 'CSV (متوافق مع Excel)',
        'json_format' => 'JSON',
        'full_refund' => 'استرداد كامل (:amount ريال عماني)',
        'partial_refund' => 'استرداد جزئي (تحديد المبلغ)',
        'last_24_hours' => 'آخر 24 ساعة',
        'last_3_days' => 'آخر 3 أيام',
        'last_7_days' => 'آخر 7 أيام',
        'last_30_days' => 'آخر 30 يوم',
        'more_than_1_day' => 'أكثر من يوم واحد',
        'more_than_3_days' => 'أكثر من 3 أيام',
        'more_than_7_days' => 'أكثر من 7 أيام',
    ],

    // Refund Reasons
    'refund_reasons' => [
        'customer_request' => 'طلب العميل',
        'event_cancelled' => 'تم إلغاء الحدث',
        'hall_unavailable' => 'القاعة غير متوفرة',
        'duplicate_payment' => 'دفعة مكررة',
        'service_not_provided' => 'الخدمة غير مقدمة',
        'quality_issues' => 'مشاكل الجودة',
        'other' => 'أخرى',
    ],

    // Metrics
    'metrics' => [
        'revenue' => 'إجمالي الإيرادات',
        'refunds' => 'ملخص المبالغ المستردة',
        'failed' => 'تحليل المدفوعات الفاشلة',
        'payment_methods' => 'تفصيل طرق الدفع',
        'daily_trends' => 'اتجاهات المعاملات اليومية',
    ],

    // Tabs
    'tabs' => [
        'all' => 'جميع المدفوعات',
        'paid' => 'مدفوعة',
        'pending' => 'معلقة',
        'failed' => 'فاشلة',
        'refunded' => 'مستردة',
        'today' => 'اليوم',
        'this_week' => 'هذا الأسبوع',
        'this_month' => 'هذا الشهر',
        'high_value' => 'عالية القيمة (1000+ ريال عماني)',
    ],

    // Columns
    'columns' => [
        'payment_reference' => 'مرجع الدفع',
        'booking_number' => 'رقم الحجز',
        'transaction_id' => 'معرف المعاملة',
        'amount' => 'المبلغ',
        'status' => 'الحالة',
        'payment_method' => 'طريقة الدفع',
        'paid_at' => 'تم الدفع في',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Filters
    'filters' => [
        'status' => 'الحالة',
        'paid_at' => 'تاريخ الدفع',
    ],

    // Export Headers
    'export' => [
        'payment_reference' => 'مرجع الدفع',
        'booking_number' => 'رقم الحجز',
        'transaction_id' => 'معرف المعاملة',
        'amount' => 'المبلغ (ريال عماني)',
        'currency' => 'العملة',
        'status' => 'الحالة',
        'payment_method' => 'طريقة الدفع',
        'refund_amount' => 'مبلغ الاسترداد (ريال عماني)',
        'paid_at' => 'تم الدفع في',
        'failed_at' => 'فشل في',
        'refunded_at' => 'تم الاسترداد في',
        'created_at' => 'تاريخ الإنشاء',
        'customer_name' => 'اسم العميل',
        'customer_email' => 'بريد العميل الإلكتروني',
        'hall_name' => 'اسم القاعة',
        'booking_date' => 'تاريخ الحجز',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'تم التصدير بنجاح',
        'export_success_body' => 'تم تصدير :count دفعة بنجاح.',
        'export_failed' => 'فشل التصدير',
        'export_failed_body' => 'فشل تصدير المدفوعات: :error',
        'json_export_success' => 'اكتمل تصدير JSON',
        'reconciliation_completed' => 'اكتملت المطابقة',
        'reconciliation_completed_body' => 'تمت مطابقة :count دفعة. تم العثور على :mismatches عدم تطابق.',
        'reconciliation_failed' => 'فشلت المطابقة',
        'report_generated' => 'تم إنشاء التقرير المالي',
        'report_failed' => 'فشل إنشاء التقرير',
        'retry_completed' => 'اكتملت إعادة المحاولة',
        'retry_completed_body' => 'تمت إعادة :count دفعة للمحاولة.',
        'retry_failed' => 'فشلت إعادة المحاولة',
        'reminders_sent' => 'تم إرسال التذكيرات',
        'reminders_sent_body' => 'تم إرسال :count تذكير بالبريد الإلكتروني بنجاح.',
        'reminders_failed' => 'فشل الإرسال',
        'refund_success' => 'تم معالجة الاسترداد بنجاح',
        'refund_success_body' => 'تم معالجة استرداد :amount ريال عماني بنجاح.',
        'refund_failed' => 'فشل الاسترداد',
        'refund_failed_body' => 'فشل معالجة الاسترداد: :error',
        'error_prefix' => 'خطأ: ',
        'email_sent' => 'تم إرسال الإيصال بنجاح',
        'email_sent_body' => 'تم إرسال إيصال الدفع إلى :email',
        'email_failed' => 'فشل إرسال الإيصال',
        'email_failed_body' => 'خطأ: :error',
        'download_failed' => 'فشل التحميل',
        'download_failed_body' => 'خطأ في إنشاء الإيصال: :error',
        'print_failed' => 'فشلت الطباعة',
        'print_failed_body' => 'خطأ في إعداد الإيصال للطباعة: :error',
    ],

    // Modals
    'modals' => [
        'refund' => [
            'heading' => 'معالجة الاسترداد',
            'description' => 'سيتم معالجة الاسترداد من خلال بوابة دفع Thawani.',
        ],
        'reconcile' => [
            'heading' => 'مطابقة سجلات الدفع',
            'description' => 'مطابقة سجلات الدفع مع معاملات البوابة. قد يستغرق هذا بضع لحظات.',
        ],
        'retry' => [
            'heading' => 'إعادة محاولة المدفوعات الفاشلة',
            'description' => 'محاولة إعادة معالجة جميع المدفوعات الفاشلة. سيتم إعادة محاولة حالات الفشل الحديثة فقط.',
        ],
        'reminders' => [
            'heading' => 'إرسال تذكيرات الدفع',
            'description' => 'إرسال تذكيرات بالبريد الإلكتروني للعملاء الذين لديهم مدفوعات معلقة.',
        ],
        'email_receipt' => [
            'heading' => 'إرسال الإيصال للعميل',
            'description' => 'إرسال إيصال الدفع مع مرفق PDF إلى البريد الإلكتروني للعميل.',
        ],
    ],

    // Placeholders
    'placeholders' => [
        'original_amount' => 'المبلغ الأصلي',
        'already_refunded' => 'تم استرداده بالفعل',
        'refundable_amount' => 'متاح للاسترداد',
        'additional_notes' => 'أدخل أي تفاصيل إضافية حول هذا الاسترداد...',
        'custom_message' => 'شكراً لحجزكم معنا...',
    ],

    // Descriptions
    'descriptions' => [
        'refund_process' => 'معالجة استرداد كامل أو جزئي لهذه الدفعة.',
        'email_receipt' => 'سيتم إرسال الإيصال كمرفق PDF إلى عنوان البريد الإلكتروني المحدد.',
    ],

    // Helpers
    'helpers' => [
        'max_refund' => 'الحد الأقصى: :amount ريال عماني',
        'notify_customer' => 'سيستلم العميل بريداً إلكترونياً حول هذا الاسترداد',
        'include_booking_details' => 'تضمين معلومات الحجز ذات الصلة في التصدير',
        'auto_update' => 'تحديث حالات الدفع تلقائياً بناءً على بيانات البوابة',
        'email_receipt' => 'أدخل عنوان البريد الإلكتروني الذي سيتم إرسال الإيصال إليه.',
        'send_admin_copy' => 'إرسال نسخة أيضاً إلى بريد المسؤول.',
        'custom_message' => 'أضف رسالة شخصية اختيارية إلى البريد الإلكتروني.',
    ],

    'errors' => [
        // ... existing errors ...
        'invalid_email' => 'الرجاء إدخال عنوان بريد إلكتروني صالح.',
    ],
    'receipt' => [
        'title' => 'إيصال الدفع',
        'tagline' => 'نظام إدارة حجز القاعات',
        'amount_paid' => 'المبلغ المدفوع',
        'refund_amount' => 'المبلغ المسترد',
        'payment_details' => 'تفاصيل الدفع',
        'booking_details' => 'تفاصيل الحجز',
        'customer_info' => 'معلومات العميل',
        'hall' => 'القاعة',
        'event_date' => 'تاريخ الحدث',
        'time_slot' => 'الفترة الزمنية',
        'customer_name' => 'اسم العميل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'thank_you' => 'شكراً لدفعكم!',
        'thank_you_sub' => 'نقدر تعاملكم معنا ونتطلع لخدمتكم.',
        'computer_generated' => 'هذا إيصال إلكتروني ولا يحتاج إلى توقيع.',
        'generated_on' => 'تم الإنشاء في',
        'sultanate_oman' => 'سلطنة عمان',
    ],

    // Report Messages
    'report_period' => '📅 الفترة: :from إلى :to',
    'report_revenue' => '💰 إجمالي الإيرادات: :amount ريال عماني (:count دفعة)',
    'report_refunds' => '↩️ إجمالي المبالغ المستردة: :amount ريال عماني (:count استرداد)',
    'report_failed' => '❌ المدفوعات الفاشلة: :count (:amount ريال عماني)',
    'report_net_revenue' => '📊 صافي الإيرادات: :amount ريال عماني',

    // Common
    'n_a' => 'غير متوفر',
    'processed_by' => 'تمت المعالجة بواسطة',
    // Payment page
    'method' => 'طريقة الدفع',
    'select_option' => 'اختر طريقة الدفع',
    'full' => 'الدفع الكامل',
    'full_description' => 'ادفع المبلغ كاملاً الآن',
    'advance' => 'دفعة مقدمة',
    'advance_description' => 'ادفع :percentage% الآن، والباقي قبل الحدث',
    'balance' => 'المبلغ المتبقي',
    'total_amount' => 'إجمالي المبلغ',
    'secure' => 'دفع آمن',
    'redirect_message' => 'سيتم توجيهك إلى بوابة الدفع الآمنة ثواني لإتمام عملية الدفع.',
    'terms_agreement' => 'أوافق على أن يتم تأكيد حجزي بعد الدفع بنجاح.',
    'view_terms' => 'عرض الشروط والأحكام',
    'pay_now' => 'ادفع الآن',
    'redirecting' => 'جاري التحويل إلى بوابة الدفع...',
    'terms_required' => 'يرجى الموافقة على الشروط والأحكام',

    // Price breakdown
    'hall_rental' => 'تأجير القاعة',
    'services' => 'الخدمات',
    'platform_fee' => 'رسوم المنصة',
    'total' => 'الإجمالي',
    'cancelled' => 'تم إلغاء الدفع. يمكنك المحاولة مرة أخرى عندما تكون جاهزاً.',
];
