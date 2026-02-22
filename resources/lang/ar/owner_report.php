<?php

declare(strict_types=1);

/**
 * Arabic Owner Panel Translations (RTL)
 *
 * Contains all translation keys used in the Owner Panel for:
 * - Earnings Resource
 * - Payouts Resource
 * - Financial Reports Page
 *
 * @package Lang\Ar
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard - لوحة التحكم
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'subheading' => 'نظرة عامة على أدائك لـ :date',
        'refresh' => 'تحديث',
        'export' => 'تصدير',
        'view_reports' => 'عرض التقارير',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'select_hall' => 'اختر القاعة',
        'all_halls' => 'جميع القاعات',
        'export_format' => 'صيغة التصدير',
        'report_type' => 'نوع التقرير',
        'export_confirm' => 'تصدير بيانات لوحة التحكم',
        'export_description' => 'حدد نطاق التاريخ والصيغة لتصدير بيانات لوحة التحكم.',
        'export_error' => 'فشل التصدير',
        'good_morning' => 'صباح الخير',
        'good_afternoon' => 'مساء الخير',
        'good_evening' => 'مساء الخير',
    ],

    // Earnings Resource
    'earnings' => [
        'label' => 'ربح',
        'plural' => 'الأرباح',
        'navigation_group' => 'المالية',

        // Table Columns
        'booking_number' => 'رقم الحجز',
        'hall' => 'القاعة',
        'date' => 'التاريخ',
        'slot' => 'الفترة',
        'customer' => 'العميل',
        'hall_price' => 'سعر القاعة',
        'services_price' => 'الخدمات',
        'total_amount' => 'الإجمالي',
        'commission_amount' => 'العمولة',
        'owner_payout' => 'أرباحك',
        'status' => 'الحالة',

        // Filters
        'filter_date_range' => 'نطاق التاريخ',
        'filter_from' => 'من',
        'filter_until' => 'إلى',
        'filter_hall' => 'القاعة',
        'filter_status' => 'الحالة',
        'filter_slot' => 'الفترة الزمنية',
        'filter_this_month' => 'هذا الشهر',
        'filter_last_month' => 'الشهر الماضي',

        // Tabs
        'tab_all' => 'جميع الأرباح',
        'tab_this_month' => 'هذا الشهر',
        'tab_last_month' => 'الشهر الماضي',
        'tab_this_year' => 'هذه السنة',

        // Page Titles
        'title' => 'أرباحي',
        'heading' => 'نظرة عامة على الأرباح',
        'list_title' => 'أرباحي',
        'view_title' => 'تفاصيل الأرباح',
        'subheading' => 'الإجمالي: :total ر.ع | هذا الشهر: :month ر.ع',

        // Actions
        'view_details' => 'عرض التفاصيل',
        'generate_report' => 'إنشاء تقرير',
        'export_excel' => 'تصدير إلى Excel',
        'download_invoice' => 'تحميل الفاتورة',
        'generate_statement' => 'إنشاء كشف حساب',
        'back_to_list' => 'العودة للقائمة',

        // Report Modal
        'report_period' => 'فترة التقرير',
        'report_title' => 'إنشاء تقرير الأرباح',
        'report_start_date' => 'تاريخ البداية',
        'report_end_date' => 'تاريخ النهاية',
        'report_hall' => 'تصفية حسب القاعة',
        'report_all_halls' => 'جميع القاعات',
        'report_include_details' => 'تضمين تفاصيل الحجوزات',
        'report_include_breakdown' => 'تضمين التفاصيل المالية',
        'report_generating' => 'جاري إنشاء التقرير...',
        'report_generated' => 'تم إنشاء التقرير بنجاح',
        'report_generated_desc' => 'يتضمن التقرير :bookings حجز بإجمالي :earnings ر.ع صافي أرباح.',
        'report_failed' => 'فشل إنشاء التقرير',
        'include_in_report' => 'تضمين في التقرير',
        'summary' => 'الملخص',
        'booking_details' => 'تفاصيل الحجوزات',
        'hall_breakdown' => 'تفاصيل القاعات',
        'chart' => 'الرسم البياني',

        // Export Settings
        'export_settings' => 'إعدادات التصدير',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'select_hall' => 'اختر القاعة',
        'all_halls' => 'جميع القاعات',
        'include_columns' => 'تضمين الأعمدة',

        // Export Column Labels
        'columns' => [
            'booking_number' => 'رقم الحجز',
            'hall' => 'اسم القاعة',
            'customer' => 'اسم العميل',
            'date' => 'تاريخ الحجز',
            'slot' => 'الفترة الزمنية',
            'hall_price' => 'سعر القاعة',
            'services_price' => 'سعر الخدمات',
            'gross_amount' => 'المبلغ الإجمالي',
            'commission' => 'العمولة',
            'net_earnings' => 'صافي الأرباح',
        ],

        // Export Messages
        'export_success' => 'تم التصدير بنجاح',
        'export_success_desc' => 'تم تصدير :count حجز بإجمالي :total ر.ع صافي أرباح.',
        'export_failed' => 'فشل التصدير',
        'no_data' => 'لا توجد بيانات للتصدير',
        'no_data_desc' => 'لم يتم العثور على أرباح للفترة المحددة.',
        'totals' => 'الإجمالي',
        'bookings' => 'حجز',

        // Infolist Sections
        'section_booking_info' => 'معلومات الحجز',
        'section_financial' => 'التفاصيل المالية',
        'section_services' => 'الخدمات الإضافية',
        'section_payment' => 'تفاصيل الدفع',

        // Widget
        'widget_title' => 'ملخص الأرباح',
        'stat_total_earnings' => 'إجمالي الأرباح',
        'stat_this_month' => 'هذا الشهر',
        'stat_this_week' => 'هذا الأسبوع',
        'stat_avg_booking' => 'متوسط/حجز',
        'stat_gross_revenue' => 'إجمالي الإيرادات',
        'stat_total_commission' => 'إجمالي العمولة',
        'stat_mom_change' => ':change% من الشهر الماضي',

        // Messages
        'no_earnings' => 'لا توجد أرباح.',
        'empty_state_heading' => 'لا توجد أرباح بعد',
        'empty_state_description' => 'ستظهر أرباحك من الحجوزات المكتملة هنا.',
    ],

    // Payouts Resource
    'payouts' => [
        'label' => 'مدفوعات',
        'plural' => 'المدفوعات',
        'navigation_group' => 'المالية',

        // Table Columns
        'payout_number' => 'رقم المدفوعات',
        'period' => 'الفترة',
        'bookings_count' => 'الحجوزات',
        'gross_revenue' => 'إجمالي الإيرادات',
        'commission_amount' => 'العمولة',
        'commission_rate' => 'النسبة',
        'net_payout' => 'صافي المدفوعات',
        'status' => 'الحالة',
        'payment_method' => 'الطريقة',
        'completed_at' => 'تاريخ الإكمال',
        'transaction_reference' => 'المرجع',

        // Filters
        'filter_status' => 'الحالة',
        'filter_period' => 'الفترة',
        'filter_from' => 'من',
        'filter_until' => 'إلى',
        'filter_completed' => 'المكتملة فقط',
        'filter_this_year' => 'هذه السنة',

        // Tabs
        'tab_all' => 'جميع المدفوعات',
        'tab_pending' => 'قيد الانتظار',
        'tab_completed' => 'مكتملة',
        'tab_this_year' => 'هذه السنة',

        // Page Titles
        'list_title' => 'مدفوعاتي',
        'view_title' => 'تفاصيل المدفوعات',
        'subheading' => 'إجمالي المستلم: :received ر.ع | قيد الانتظار: :pending ر.ع',

        // Actions
        'download_receipt' => 'تحميل الإيصال',
        'contact_support' => 'تواصل مع الدعم',
        'report_issue' => 'الإبلاغ عن مشكلة',
        'back_to_list' => 'العودة للقائمة',

        // Infolist Sections
        'section_summary' => 'ملخص المدفوعات',
        'section_financial' => 'التفاصيل المالية',
        'section_payment' => 'تفاصيل الدفع',
        'section_failure' => 'تفاصيل الفشل',
        'section_notes' => 'ملاحظات',
        'section_timestamps' => 'التواريخ',

        // Infolist Fields
        'period_start' => 'بداية الفترة',
        'period_end' => 'نهاية الفترة',
        'adjustments' => 'التعديلات',
        'bank_details' => 'تفاصيل البنك',
        'processed_by' => 'معالج بواسطة',
        'processed_at' => 'تاريخ المعالجة',
        'failed_at' => 'تاريخ الفشل',
        'failure_reason' => 'سبب الفشل',
        'notes' => 'ملاحظات',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',

        // Widget
        'widget_title' => 'ملخص المدفوعات',
        'stat_total_received' => 'إجمالي المستلم',
        'stat_pending' => 'قيد الانتظار',
        'stat_processing' => 'قيد المعالجة',
        'stat_avg_payout' => 'متوسط المدفوعات',
        'stat_this_year' => 'هذه السنة',
        'stat_last_payout' => 'آخر مدفوعات',

        // Messages
        'no_payouts' => 'لا توجد مدفوعات.',
        'empty_state_heading' => 'لا توجد مدفوعات بعد',
        'empty_state_description' => 'ستظهر مدفوعاتك هنا بعد معالجتها من قبل الإدارة.',
        'support_ticket_created' => 'تم إنشاء تذكرة الدعم بنجاح.',
    ],

    // Financial Reports Page
    'reports' => [
        'title' => 'التقارير المالية',
        'navigation_group' => 'المالية',

        // Report Types
        'type_monthly' => 'تقرير شهري',
        'type_yearly' => 'تقرير سنوي',
        'type_hall' => 'أداء القاعات',
        'type_comparison' => 'مقارنة الأشهر',

        // Form Fields
        'report_type' => 'نوع التقرير',
        'select_year' => 'اختر السنة',
        'select_month' => 'اختر الشهر',
        'select_hall' => 'اختر القاعة',
        'all_halls' => 'جميع القاعات',

        // Actions
        'export_pdf' => 'تصدير PDF',
        'refresh' => 'تحديث',

        // Section Titles
        'summary' => 'الملخص',
        'daily_breakdown' => 'التفاصيل اليومية',
        'monthly_breakdown' => 'التفاصيل الشهرية',
        'hall_breakdown' => 'تفاصيل القاعات',
        'slot_breakdown' => 'تحليل الفترات الزمنية',
        'comparison' => 'المقارنة',

        // Stats
        'total_bookings' => 'إجمالي الحجوزات',
        'gross_revenue' => 'إجمالي الإيرادات',
        'hall_revenue' => 'إيرادات القاعات',
        'services_revenue' => 'إيرادات الخدمات',
        'total_commission' => 'إجمالي العمولة',
        'net_earnings' => 'صافي الأرباح',
        'avg_per_booking' => 'متوسط لكل حجز',
        'best_month' => 'أفضل شهر',
        'avg_monthly' => 'المتوسط الشهري',
        'year_total' => 'إجمالي السنة',

        // Comparison
        'current_month' => 'الشهر الحالي',
        'previous_month' => 'الشهر السابق',
        'change' => 'التغيير',
        'increase' => 'زيادة',
        'decrease' => 'نقصان',
        'no_change' => 'لا تغيير',

        // Messages
        'report_generated' => 'تم إنشاء التقرير بنجاح!',
        'export_success' => 'تم تصدير PDF بنجاح.',
        'no_data' => 'لا توجد بيانات للفترة المحددة.',
    ],

    // Common
    'months' => [
        '1' => 'يناير',
        '2' => 'فبراير',
        '3' => 'مارس',
        '4' => 'أبريل',
        '5' => 'مايو',
        '6' => 'يونيو',
        '7' => 'يوليو',
        '8' => 'أغسطس',
        '9' => 'سبتمبر',
        '10' => 'أكتوبر',
        '11' => 'نوفمبر',
        '12' => 'ديسمبر',
    ],

    'slots' => [
        'morning' => 'صباحي',
        'afternoon' => 'ظهري',
        'evening' => 'مسائي',
        'full_day' => 'يوم كامل',
    ],

    'status' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
    ],

    'payment' => [
        'paid' => 'مدفوع',
        'pending' => 'قيد الانتظار',
        'partial' => 'جزئي',
        'refunded' => 'مسترد',
    ],

    'actions' => [
        'view' => 'عرض',
        'export' => 'تصدير',
        'download' => 'تحميل',
        'generate' => 'إنشاء',
        'refresh' => 'تحديث',
        'back' => 'رجوع',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics - التقارير والتحليلات
    |--------------------------------------------------------------------------
    */
    'reports' => [
        // Page titles
        'title' => 'التقارير والتحليلات',
        'heading' => 'تقاريري',
        'subheading' => 'تتبع أرباحك وحجوزاتك وأداء قاعاتك',
        'nav_label' => 'التقارير',

        // Tabs
        'tabs' => [
            'overview' => 'نظرة عامة',
            'earnings' => 'الأرباح',
            'bookings' => 'الحجوزات',
            'halls' => 'القاعات',
        ],

        // Filters
        'filters' => [
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'hall' => 'القاعة',
            'all_halls' => 'جميع القاعات',
            'preset' => 'اختيار سريع',
            'custom' => 'نطاق مخصص',
        ],

        // Presets
        'presets' => [
            'today' => 'اليوم',
            'yesterday' => 'أمس',
            'this_week' => 'هذا الأسبوع',
            'last_week' => 'الأسبوع الماضي',
            'this_month' => 'هذا الشهر',
            'last_month' => 'الشهر الماضي',
            'this_quarter' => 'هذا الربع',
            'this_year' => 'هذا العام',
        ],

        // Actions
        'actions' => [
            'refresh' => 'تحديث',
            'export_csv' => 'تصدير CSV',
            'export_pdf' => 'تصدير PDF',
            'print' => 'طباعة',
        ],

        // Export
        'export' => [
            'type' => 'نوع التقرير',
            'summary' => 'تقرير ملخص',
            'bookings' => 'تقرير الحجوزات',
            'revenue' => 'تقرير الإيرادات',
            'halls' => 'أداء القاعات',
        ],

        // Stats
        'stats' => [
            'total_earnings' => 'صافي الأرباح',
            'total_revenue' => 'إجمالي الإيرادات',
            'total_bookings' => 'إجمالي الحجوزات',
            'pending_payout' => 'المدفوعات المعلقة',
            'confirmed' => 'مؤكدة',
            'completed' => 'مكتملة',
            'pending' => 'معلقة',
            'cancelled' => 'ملغاة',
            'total_guests' => 'إجمالي الضيوف',
            'avg_booking' => 'متوسط الحجز',
            'total_halls' => 'إجمالي القاعات',
            'active_halls' => 'القاعات النشطة',
            'avg_guests_per_booking' => 'متوسط الضيوف/حجز',
            'avg_booking_value' => 'متوسط قيمة الحجز',
            'avg_bookings_per_hall' => 'متوسط الحجوزات/قاعة',
        ],

        // Status
        'status' => [
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'pending' => 'معلق',
            'cancelled' => 'ملغي',
        ],

        // Charts
        'charts' => [
            'earnings_trend' => 'اتجاه الأرباح',
            'booking_status' => 'حالة الحجوزات',
            'time_slots' => 'توزيع الفترات الزمنية',
            'revenue' => 'الإيرادات',
            'earnings' => 'صافي الأرباح',
            'bookings' => 'الحجوزات',
        ],

        // Sections
        'sections' => [
            'earnings_summary' => 'ملخص الأرباح',
            'booking_summary' => 'ملخص الحجوزات',
            'hall_performance' => 'أداء القاعات',
            'monthly_comparison' => 'المقارنة الشهرية',
            'guest_stats' => 'إحصائيات الضيوف',
        ],

        // Fields
        'fields' => [
            'total_revenue' => 'إجمالي الإيرادات',
            'platform_fee' => 'رسوم المنصة',
            'net_earnings' => 'صافي الأرباح',
            'paid_out' => 'تم الدفع',
            'pending_payout' => 'مدفوعات معلقة',
        ],

        // Table headers
        'table' => [
            'hall' => 'القاعة',
            'bookings' => 'الحجوزات',
            'revenue' => 'الإيرادات',
            'avg_booking' => 'متوسط الحجز',
            'total' => 'الإجمالي',
            'earnings' => 'الأرباح',
        ],

        // Comparison
        'comparison' => [
            'earnings_change' => 'تغير الأرباح',
            'bookings_change' => 'تغير الحجوزات',
            'vs_last_month' => 'مقارنة بالشهر الماضي',
        ],

        // Notifications
        'notifications' => [
            'no_data' => 'لا توجد بيانات متاحة للتصدير',
        ],

        // No data
        'no_data' => 'لا توجد بيانات متاحة للفترة المحددة',

        // PDF
        'pdf' => [
            'title' => 'تقرير الأرباح',
            'earnings_report' => 'تقرير الأرباح',
            'subtitle' => 'تقرير مفصل للأرباح والأداء',
            'period' => 'الفترة',
            'generated' => 'تم الإنشاء',
            'owner_details' => 'تفاصيل المالك',
            'owner_name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'business' => 'النشاط التجاري',
            'phone' => 'الهاتف',
            'net_earnings' => 'صافي الأرباح',
            'financial_overview' => 'نظرة مالية عامة',
            'gross_revenue' => 'إجمالي الإيرادات',
            'platform_fee' => 'رسوم المنصة',
            'pending_payout' => 'مدفوعات معلقة',
            'monthly_comparison' => 'المقارنة الشهرية',
            'earnings_change' => 'تغير الأرباح',
            'bookings_change' => 'تغير الحجوزات',
            'booking_stats' => 'إحصائيات الحجوزات',
            'total_bookings' => 'إجمالي الحجوزات',
            'confirmed' => 'مؤكدة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            'additional_stats' => 'إحصائيات إضافية',
            'total_guests' => 'إجمالي الضيوف',
            'avg_booking_value' => 'متوسط قيمة الحجز',
            'total_paid_out' => 'إجمالي المدفوعات',
            'hall_performance' => 'أداء القاعات',
            'hall_name' => 'اسم القاعة',
            'bookings' => 'الحجوزات',
            'revenue' => 'الإيرادات',
            'avg_booking' => 'متوسط الحجز',
            'total' => 'الإجمالي',
            'hall_summary' => 'ملخص القاعات',
            'total_halls' => 'إجمالي القاعات',
            'active_halls' => 'القاعات النشطة',
            'avg_bookings_per_hall' => 'متوسط الحجوزات/قاعة',
            'avg_earnings_per_hall' => 'متوسط الأرباح/قاعة',
            'footer' => 'تم إنشاء هذا التقرير بواسطة :app',
            'thank_you' => 'شكراً لكونك شريكاً قيماً!',
        ],
    ],
];
