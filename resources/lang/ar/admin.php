<?php

declare(strict_types=1);

/**
 * ترجمة إدارة الدفع للمسؤولين - العربية
 *
 * يحتوي على جميع مفاتيح الترجمة لواجهة إدارة مدفوعات المسؤولين.
 *
 * @package Lang\Ar
 */
return [
    /*
    |--------------------------------------------------------------------------
    | إدارة المدفوعات
    |--------------------------------------------------------------------------
    */
    'payout' => [
        // عناوين الصفحات
        'title' => 'إدارة المدفوعات',
        'create_title' => 'إنشاء دفعة',
        'edit_title' => 'تعديل دفعة',
        'view_title' => 'تفاصيل الدفعة',

        // الأقسام
        'sections' => [
            'main' => 'معلومات الدفعة',
            'main_desc' => 'التفاصيل الأساسية للدفعة واختيار المالك',
            'financial' => 'التفاصيل المالية',
            'financial_desc' => 'الإيرادات والعمولة ومبالغ الدفع',
            'payment' => 'تفاصيل الدفع',
            'payment_desc' => 'طريقة الدفع ومعلومات المعاملة',
            'notes' => 'ملاحظات وتعليقات',
            'owner_info' => 'معلومات المالك',
            'timestamps' => 'الجدول الزمني',
        ],

        // تسميات الحقول
        'fields' => [
            'payout_number' => 'رقم الدفعة',
            'owner' => 'مالك القاعة',
            'owner_name' => 'اسم المالك',
            'owner_email' => 'البريد الإلكتروني للمالك',
            'business_name' => 'اسم النشاط التجاري',
            'bank_name' => 'اسم البنك',
            'period' => 'الفترة',
            'period_start' => 'بداية الفترة',
            'period_end' => 'نهاية الفترة',
            'status' => 'الحالة',
            'gross_revenue' => 'إجمالي الإيرادات',
            'gross' => 'الإجمالي',
            'commission' => 'العمولة',
            'commission_rate' => 'معدل العمولة',
            'adjustments' => 'تعديلات',
            'net_payout' => 'صافي المدفوع',
            'net' => 'الصافي',
            'bookings_count' => 'عدد الحجوزات',
            'bookings' => 'الحجوزات',
            'payment_method' => 'طريقة الدفع',
            'transaction_reference' => 'رقم المرجع للمعاملة',
            'bank_details' => 'تفاصيل البنك',
            'notes' => 'ملاحظات',
            'failure_reason' => 'سبب الفشل',
            'hold_reason' => 'سبب التعليق',
            'cancel_reason' => 'سبب الإلغاء',
            'processed_at' => 'تمت المعالجة في',
            'completed_at' => 'تم الإكمال في',
            'failed_at' => 'فشلت في',
            'processed_by' => 'تمت المعالجة بواسطة',
            'created_at' => 'تاريخ الإنشاء',
        ],

        // طرق الدفع
        'methods' => [
            'bank_transfer' => 'تحويل بنكي',
            'cash' => 'نقداً',
            'cheque' => 'شيك',
            'other' => 'أخرى',
        ],

        // تفاصيل البنك
        'bank' => [
            'field' => 'الحقل',
            'value' => 'القيمة',
            'add' => 'إضافة تفصيل بنكي',
        ],

        // الإجراءات
        'actions' => [
            'create' => 'دفعة جديدة',
            'edit' => 'تعديل',
            'view' => 'عرض',
            'delete' => 'حذف',
            'process' => 'بدء المعالجة',
            'complete' => 'تعيين كمكتمل',
            'fail' => 'تعيين كفاشل',
            'hold' => 'وضع قيد الانتظار',
            'cancel' => 'إلغاء الدفعة',
            'generate' => 'توليد مدفوعات',
            'calculate' => 'احتساب من الحجوزات',
            'export' => 'تصدير',
            'print' => 'طباعة الإيصال',
        ],

        // الإجراءات الجماعية
        'bulk' => [
            'process' => 'معالجة المحدد',
            'cancel' => 'إلغاء المحدد',
        ],

        // الفلاتر
        'filters' => [
            'status' => 'الحالة',
            'owner' => 'المالك',
            'period' => 'الفترة',
            'from' => 'من تاريخ',
            'to' => 'إلى تاريخ',
            'pending_only' => 'المعلقة فقط',
        ],

        // علامات التبويب
        'tabs' => [
            'all' => 'الكل',
            'pending' => 'معلقة',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتملة',
            'on_hold' => 'قيد الانتظار',
            'failed' => 'فاشلة',
        ],

        // عناوين ووصف النوافذ المنبثقة
        'modal' => [
            'process_title' => 'بدء معالجة الدفعة',
            'process_desc' => 'سيؤدي هذا إلى تعيين الدفعة على أنها قيد المعالجة. المتابعة؟',
            'process_desc_amount' => 'معالجة دفعة بقيمة :amount ريال عماني إلى :owner؟',
            'process_confirm' => 'بدء المعالجة',
            'complete_title' => 'إكمال الدفعة',
            'complete_desc' => 'تأكيد دفع مبلغ :amount ريال عماني إلى المالك.',
            'fail_title' => 'تعيين الدفعة كفاشلة',
            'hold_title' => 'وضع الدفعة قيد الانتظار',
            'cancel_title' => 'إلغاء الدفعة',
            'cancel_desc' => 'لا يمكن التراجع عن هذا الإجراء. هل أنت متأكد؟',
            'generate_title' => 'توليد المدفوعات',
            'generate_desc' => 'إنشاء سجلات المدفوعات للملاك بناءً على الحجوزات المكتملة.',
            'generate_confirm' => 'توليد المدفوعات',
            'regenerate_receipt_title' => 'إعادة توليد الإيصال؟',
            'regenerate_receipt_desc' => 'سيؤدي هذا إلى حذف الإيصال الحالي وإنشاء واحد جديد. المتابعة؟',
        ],

        // الإشعارات
        'notifications' => [
            'created' => 'تم إنشاء الدفعة',
            'created_body' => 'تم إنشاء الدفعة :number لـ :owner (:amount ريال عماني)',
            'updated' => 'تم تحديث الدفعة',
            'processing' => 'بدأت معالجة الدفعة',
            'processing_body' => 'الدفعة :number قيد المعالجة الآن.',
            'process_failed' => 'فشل بدء المعالجة',
            'completed' => 'تم إكمال الدفعة',
            'completed_body' => 'تم دفع :amount ريال عماني إلى :owner',
            'failed' => 'تم تعيين الدفعة كفاشلة',
            'failed_body' => 'تم تعيين الدفعة على أنها فاشلة.',
            'on_hold' => 'الدفعة قيد الانتظار',
            'on_hold_body' => 'تم وضع الدفعة قيد الانتظار.',
            'cancelled' => 'تم إلغاء الدفعة',
            'cancelled_body' => 'تم إلغاء الدفعة.',
            'generated' => 'تم توليد :count دفعة(مدفوعات)',
            'no_payouts' => 'لم يتم توليد مدفوعات',
            'no_payouts_body' => 'لم يتم العثور على حجوزات مؤهلة للفترة المحددة.',
            'bulk_processed' => 'تم نقل :count دفعة(مدفوعات) إلى قيد المعالجة',
            'bulk_cancelled' => 'تم إلغاء :count دفعة(مدفوعات)',
            'missing_data' => 'معلومات ناقصة',
            'missing_data_body' => 'يرجى اختيار مالك وفترة أولاً.',
            'calculated' => 'تم احتساب القيم',
            'calculated_body' => 'تم العثور على :count حجز(حجوزات) مؤهلة.',
            'no_bookings' => 'لم يتم العثور على حجوزات',
            'no_bookings_body' => 'لم يتم العثور على حجوزات مدفوعة لهذا المالك في الفترة المحددة.',
            'export_started' => 'بدأ التصدير',
            'receipt_generated' => 'تم توليد الإيصال',
            'receipt_generated_body' => 'تم إنشاء ملف PDF لإيصال الدفعة وحفظه.',
            'receipt_failed' => 'تحذير من فشل توليد الإيصال',
            'receipt_failed_body' => 'تم إكمال الدفعة لكن فشل توليد الإيصال. يمكنك إعادة توليده لاحقاً.',
            'receipt_regenerated' => 'تمت إعادة توليد الإيصال',
            'receipt_regenerated_body' => 'تمت إعادة توليد إيصال الدفعة بنجاح.',
            'receipt_not_found' => 'لم يتم العثور على الإيصال',
            'receipt_not_found_body' => 'تعذر العثور على ملف الإيصال. حاول إعادة توليده.',
        ],

        // لوحة الإحصائيات
        'stats' => [
            'heading' => 'نظرة عامة على المدفوعات',
            'description' => 'ملخص مدفوعات الملاك',
            'pending' => 'مدفوعات معلقة',
            'pending_count' => ':count في انتظار المعالجة',
            'processing' => 'قيد المعالجة',
            'processing_count' => ':count قيد المعالجة',
            'completed_month' => 'المكتملة هذا الشهر',
            'total_paid' => 'إجمالي المدفوع',
            'all_time' => 'جميع الأوقات',
            'on_hold' => 'قيد الانتظار',
            'requires_attention' => 'تحتاج إلى اهتمام',
            'failed' => 'فاشلة',
            'needs_review' => 'تحتاج إلى مراجعة',
            'increase' => 'زيادة بنسبة :percent%',
            'decrease' => 'انخفاض بنسبة :percent%',
        ],

        // التصدير
        'export' => [
            'format' => 'تنسيق التصدير',
        ],

        // النصوص التوضيحية والأماكن الفارغة
        'auto_generated' => 'تم توليده تلقائياً',
        'auto_generated_help' => 'سيتم توليد رقم الدفعة تلقائياً',
        'transaction_placeholder' => 'مثال: TXN-2025-00001',
        'adjustments_help' => 'موجب للإضافات، سالب للخصومات',
        'failure_placeholder' => 'اشرح سبب الفشل...',
        'hold_placeholder' => 'سبب وضع الدفعة قيد الانتظار (اختياري)',
        'no_notes' => 'لا توجد ملاحظات',
        'all_owners' => 'جميع الملاك',
        'all_statuses' => 'جميع الحالات',
        'generate_owner_help' => 'اتركه فارغاً لتوليد مدفوعات لجميع الملاك',
    ],

    /*
    |--------------------------------------------------------------------------
    | تسميات حالات الدفع (Enum)
    |--------------------------------------------------------------------------
    */
    'enums' => [
        'payout_status' => [
            'pending' => 'معلقة',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتملة',
            'failed' => 'فاشلة',
            'cancelled' => 'ملغاة',
            'on_hold' => 'قيد الانتظار',
        ],
    ],
    'reports' => [
        // عناوين الصفحات
        'title' => 'التقارير والتحليلات',
        'heading' => 'تقارير المنصة',
        'subheading' => 'تحليلات ورؤى شاملة لأداء المنصة',

        // علامات التبويب
        'tabs' => [
            'overview' => 'نظرة عامة',
            'revenue' => 'الإيرادات',
            'bookings' => 'الحجوزات',
            'performance' => 'الأداء',
            'commission' => 'العمولة',
        ],

        // الفلاتر
        'filters' => [
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'preset' => 'اختيار سريع',
            'custom' => 'نطاق مخصص',
        ],

        // النطاقات الزمنية المحددة مسبقاً
        'presets' => [
            'today' => 'اليوم',
            'yesterday' => 'أمس',
            'this_week' => 'هذا الأسبوع',
            'last_week' => 'الأسبوع الماضي',
            'this_month' => 'هذا الشهر',
            'last_month' => 'الشهر الماضي',
            'this_quarter' => 'هذا الربع',
            'this_year' => 'هذه السنة',
        ],

        // الإجراءات
        'actions' => [
            'refresh' => 'تحديث',
            'export_csv' => 'تصدير CSV',
            'export_pdf' => 'تصدير PDF',
            'print' => 'طباعة',
            'download_receipt' => 'تنزيل الإيصال',
            'regenerate_receipt' => 'إعادة توليد الإيصال',
        ],

        // التصدير
        'export' => [
            'type' => 'نوع التقرير',
            'summary' => 'تقرير ملخص',
            'bookings' => 'تقرير الحجوزات',
            'revenue' => 'تقرير الإيرادات',
            'halls' => 'أداء القاعات',
        ],

        // الإحصائيات
        'stats' => [
            'total_revenue' => 'إجمالي الإيرادات',
            'platform_commission' => 'عمولة المنصة',
            'total_bookings' => 'إجمالي الحجوزات',
            'pending_payouts' => 'مدفوعات معلقة',
            'payouts' => 'مدفوعات',
            'confirmed' => 'مؤكدة',
            'completed' => 'مكتملة',
            'pending' => 'معلقة',
            'cancelled' => 'ملغاة',
            'active_halls' => 'قاعات نشطة',
            'verified_owners' => 'ملاك موثقون',
        ],

        // الحالة
        'status' => [
            'confirmed' => 'مؤكدة',
            'completed' => 'مكتملة',
            'pending' => 'معلقة',
            'cancelled' => 'ملغاة',
        ],

        // الرسوم البيانية
        'charts' => [
            'revenue_trend' => 'اتجاه الإيرادات',
            'booking_status' => 'توزيع حالة الحجز',
            'time_slots' => 'توزيع الفترات الزمنية',
            'revenue' => 'الإيرادات',
            'commission' => 'العمولة',
            'bookings' => 'الحجوزات',
        ],

        // الأقسام
        'sections' => [
            'revenue_summary' => 'ملخص الإيرادات',
            'booking_summary' => 'ملخص الحجوزات',
            'top_halls' => 'أفضل القاعات أداءً',
            'top_owners' => 'أفضل الملاك أداءً',
            'commission_summary' => 'ملخص العمولة',
            'by_type' => 'حسب نوع العمولة',
        ],

        // الحقول
        'fields' => [
            'gross_revenue' => 'إجمالي الإيرادات',
            'platform_commission' => 'عمولة المنصة',
            'owner_payouts' => 'مدفوعات الملاك',
            'refunds' => 'المبالغ المعادة',
            'total_commission' => 'إجمالي العمولة',
            'total_revenue' => 'إجمالي الإيرادات',
            'avg_rate' => 'المعدل المتوسط',
        ],

        // عناوين الجداول
        'table' => [
            'hall' => 'القاعة',
            'owner' => 'المالك',
            'bookings' => 'الحجوزات',
            'revenue' => 'الإيرادات',
            'halls' => 'القاعات',
        ],

        // الإشعارات
        'notifications' => [
            'no_data' => 'لا توجد بيانات متاحة للتصدير',
        ],

        // لا توجد بيانات
        'no_data' => 'لا توجد بيانات متاحة للفترة المحددة',

        // PDF
        'pdf' => [
            'title' => 'تقرير المنصة',
            'platform_report' => 'تقرير تحليلات المنصة',
            'period' => 'الفترة',
            'generated' => 'تم التوليد',
            'by' => 'بواسطة',
            'overview' => 'نظرة عامة',
            'total_revenue' => 'إجمالي الإيرادات',
            'platform_commission' => 'عمولة المنصة',
            'owner_payouts' => 'مدفوعات الملاك',
            'pending_payouts' => 'مدفوعات معلقة',
            'booking_stats' => 'إحصائيات الحجوزات',
            'total_bookings' => 'إجمالي الحجوزات',
            'confirmed' => 'مؤكدة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            'commission_summary' => 'ملخص العمولة',
            'gross_revenue' => 'إجمالي الإيرادات',
            'total_commission' => 'إجمالي العمولة',
            'avg_commission_rate' => 'متوسط معدل العمولة',
            'bookings_processed' => 'الحجوزات المعالجة',
            'top_halls' => 'أفضل القاعات أداءً',
            'hall_name' => 'اسم القاعة',
            'bookings' => 'الحجوزات',
            'revenue' => 'الإيرادات',
            'avg_booking' => 'متوسط الحجز',
            'top_owners' => 'أفضل الملاك أداءً',
            'owner_name' => 'اسم المالك',
            'business' => 'النشاط التجاري',
            'halls' => 'القاعات',
            'commission' => 'العمولة',
            'platform_stats' => 'إحصائيات المنصة',
            'active_halls' => 'قاعات نشطة',
            'verified_owners' => 'ملاك موثقون',
            'total_customers' => 'إجمالي العملاء',
            'pending_payout_count' => 'مدفوعات معلقة',
            'footer' => 'تم توليد هذا التقرير بواسطة :app',
            'confidential' => 'سري - للاستخدام الداخلي فقط',
        ],
    ],
];
