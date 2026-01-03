<?php

declare(strict_types=1);

/**
 * Arabic PDF Translations (RTL)
 *
 * Contains all translation keys used in PDF templates for:
 * - Owner Earnings Report
 * - Booking Statement
 * - Financial Report
 *
 * @package Lang\Ar
 */
return [
    // Company Information
    'company' => [
        'name' => 'مجالس',
        'tagline' => 'منصة حجز القاعات',
    ],

    // Footer
    'footer' => [
        'generated' => 'تم الإنشاء',
        'system' => 'النظام',
        'page' => 'صفحة',
        'confidential' => 'سري - للاستخدام الخاص بالمالك فقط',
        'document_id' => 'رقم المستند',
    ],

    // Earnings Report
    'earnings_report' => [
        'title' => 'تقرير الأرباح',
        'period' => 'الفترة',
        'owner_name' => 'اسم المالك',
        'report_date' => 'تاريخ التقرير',
        'email' => 'البريد الإلكتروني',
        'report_number' => 'رقم التقرير',

        // Summary
        'total_earnings' => 'صافي الأرباح',
        'gross_revenue' => 'إجمالي الإيرادات',
        'commission' => 'العمولة',
        'total_bookings' => 'إجمالي الحجوزات',

        // Financial Breakdown
        'financial_breakdown' => 'التفاصيل المالية',
        'hall_rental_income' => 'دخل تأجير القاعة',
        'services_income' => 'دخل الخدمات',
        'gross_total' => 'الإجمالي الكلي',
        'platform_commission' => 'عمولة المنصة',
        'net_earnings' => 'صافي الأرباح',

        // Details Table
        'earnings_details' => 'تفاصيل الأرباح',
        'col_booking' => 'رقم الحجز',
        'col_date' => 'التاريخ',
        'col_hall' => 'القاعة',
        'col_slot' => 'الفترة',
        'col_hall_price' => 'سعر القاعة',
        'col_services' => 'الخدمات',
        'col_commission' => 'العمولة',
        'col_net' => 'الصافي',
        'no_bookings' => 'لا توجد حجوزات في هذه الفترة.',
        'totals' => 'المجموع',

        // Hall Performance
        'hall_performance' => 'أداء القاعات',
        'col_bookings_count' => 'الحجوزات',
        'col_hall_revenue' => 'إيرادات القاعة',
        'col_services_revenue' => 'إيرادات الخدمات',
        'col_total_revenue' => 'إجمالي الإيرادات',
        'col_net_earnings' => 'صافي الأرباح',

        // Notes
        'notes' => 'ملاحظات',
    ],

    // Booking Statement
    'booking_statement' => [
        'title' => 'كشف الحجز',

        // Booking Info
        'booking_info' => 'معلومات الحجز',
        'booking_number' => 'رقم الحجز',
        'booking_date' => 'تاريخ الحجز',
        'time_slot' => 'الفترة الزمنية',
        'booking_status' => 'حالة الحجز',
        'payment_status' => 'حالة الدفع',

        // Customer Info
        'customer_info' => 'معلومات العميل',
        'customer_name' => 'اسم العميل',
        'customer_phone' => 'رقم الهاتف',
        'customer_email' => 'البريد الإلكتروني',
        'guests_count' => 'عدد الضيوف',
        'guests' => 'ضيف',
        'event_type' => 'نوع المناسبة',

        // Hall Details
        'hall_details' => 'تفاصيل القاعة',
        'location' => 'الموقع',
        'capacity' => 'السعة',
        'persons' => 'شخص',
        'hall_type' => 'نوع القاعة',

        // Extra Services
        'extra_services' => 'الخدمات الإضافية',
        'service_name' => 'اسم الخدمة',
        'quantity' => 'الكمية',
        'unit_price' => 'سعر الوحدة',
        'total' => 'الإجمالي',

        // Financial Summary
        'financial_summary' => 'الملخص المالي',
        'hall_rental' => 'إيجار القاعة',
        'services_total' => 'إجمالي الخدمات',
        'gross_total' => 'الإجمالي الكلي',
        'platform_commission' => 'عمولة المنصة',
        'your_earnings' => 'أرباحك',

        // Payment Details
        'payment_details' => 'تفاصيل الدفع',
        'payment_method' => 'طريقة الدفع',
        'paid_amount' => 'المبلغ المدفوع',
        'payment_date' => 'تاريخ الدفع',

        // Notes
        'customer_notes' => 'ملاحظات العميل',

        // Timestamps
        'created_at' => 'تاريخ إنشاء الحجز',
        'updated_at' => 'آخر تحديث',
        'statement_generated' => 'تاريخ إنشاء الكشف',
    ],

    // Financial Report
    'financial_report' => [
        'title' => 'التقرير المالي',
        'generated' => 'تم الإنشاء',
        'report_id' => 'رقم التقرير',

        // Report Types
        'types' => [
            'monthly' => 'تقرير شهري',
            'yearly' => 'تقرير سنوي',
            'hall' => 'تقرير أداء القاعات',
            'comparison' => 'تقرير مقارنة',
        ],

        // Period Labels
        'period_monthly' => 'التقرير الشهري لـ :month :year',
        'period_yearly' => 'التقرير السنوي لـ :year',
        'period_comparison' => 'المقارنة: :current مقابل :previous :year',
        'period_custom' => 'فترة مخصصة: :start - :end',

        // Owner Info
        'owner' => 'المالك',
        'total_halls' => 'إجمالي القاعات',
        'active_halls' => 'القاعات النشطة',
        'member_since' => 'عضو منذ',

        // Summary Cards
        'net_earnings' => 'صافي الأرباح',
        'gross_revenue' => 'إجمالي الإيرادات',
        'commission' => 'العمولة',
        'total_bookings' => 'إجمالي الحجوزات',
        'avg_per_booking' => 'متوسط/حجز',
        'occupancy_rate' => 'نسبة الإشغال',

        // Section Headers
        'daily_breakdown' => 'التفاصيل اليومية',
        'monthly_breakdown' => 'التفاصيل الشهرية',
        'financial_breakdown' => 'التفاصيل المالية',
        'slot_breakdown' => 'تحليل الفترات الزمنية',
        'hall_breakdown' => 'تفاصيل القاعات',
        'hall_performance' => 'أداء القاعات',
        'hall_comparison' => 'مقارنة القاعات',
        'month_comparison' => 'مقارنة شهرية',
        'year_stats' => 'إحصائيات السنة',
        'payout_summary' => 'ملخص المدفوعات',

        // Table Headers
        'date' => 'التاريخ',
        'month' => 'الشهر',
        'bookings' => 'الحجوزات',
        'gross' => 'الإجمالي',
        'net' => 'الصافي',
        'hall_rev' => 'إيراد القاعة',
        'services_rev' => 'إيراد الخدمات',
        'hall' => 'القاعة',
        'slot' => 'الفترة',
        'count' => 'العدد',
        'revenue' => 'الإيرادات',
        'total' => 'المجموع',
        'share' => 'الحصة',
        'contribution' => 'المساهمة',
        'metric' => 'المقياس',
        'change' => 'التغيير',

        // Breakdown Labels
        'hall_revenue' => 'إيرادات القاعة',
        'services_revenue' => 'إيرادات الخدمات',
        'gross_total' => 'الإجمالي الكلي',
        'platform_commission' => 'عمولة المنصة',
        'net_total' => 'الصافي الإجمالي',

        // Yearly Stats
        'year_total' => 'إجمالي السنة',
        'best_month' => 'أفضل شهر',
        'best_month_earnings' => 'أرباح أفضل شهر',
        'avg_monthly' => 'المتوسط الشهري',
        'total_year_earnings' => 'إجمالي أرباح السنة',

        // Payout
        'total_received' => 'إجمالي المستلم',
        'pending_payout' => 'المدفوعات المعلقة',
        'payout_count' => 'عدد المدفوعات',

        // Analysis
        'analysis' => 'التحليل',
        'analysis_positive' => 'زادت أرباحك بنسبة :change% مقارنة بالشهر السابق. استمر في الأداء الممتاز!',
        'analysis_negative' => 'انخفضت أرباحك بنسبة :change% مقارنة بالشهر السابق. ننصح بمراجعة التسعير أو استراتيجية التسويق.',
        'analysis_neutral' => 'ظلت أرباحك مستقرة مقارنة بالشهر السابق.',
    ],
];
