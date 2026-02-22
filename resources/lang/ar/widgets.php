<?php

return [
    'hall-stats' => [
        'total_halls' => 'إجمالي القاعات',
        'total_halls_desc' => 'جميع القاعات في النظام',
        'active_halls' => 'القاعات النشطة',
        'active_halls_desc' => 'القاعات النشطة حالياً',
        'featured_halls' => 'القاعات المميزة',
        'featured_halls_desc' => 'القاعات المميزة',
        'pending_halls' => 'في انتظار الموافقة',
        'pending_halls_desc' => 'القاعات في انتظار الموافقة',
        'average_price' => 'متوسط السعر',
        'average_price_desc' => 'متوسط السعر لكل فترة',
    ],

    'hall-stats-overview' => [
        'total_bookings' => 'إجمالي الحجوزات',
        'percent_increase_month' => 'زيادة :percent% هذا الشهر',
        'percent_decrease_month' => 'انخفاض :percent% هذا الشهر',
        'total_revenue' => 'إجمالي الإيرادات',
        'percent_increase_vs_last_month' => 'زيادة :percent% عن الشهر الماضي',
        'percent_decrease_vs_last_month' => 'انخفاض :percent% عن الشهر الماضي',
        'average_rating' => 'متوسط التقييم',
        'based_on_reviews' => 'بناءً على :count تقييم',
        'no_reviews_yet' => 'لا توجد تقييمات بعد',
        'occupancy_rate' => 'نسبة الإشغال',
        'slots_this_month' => ':booked من :total فترة هذا الشهر',
        'pending_bookings' => 'الحجوزات المعلقة',
        'upcoming_count' => ':count قادمة',
        'completed_bookings' => 'الحجوزات المكتملة',
        'this_month_count' => ':count هذا الشهر',
    ],

    'hall-revenue-chart' => [
        'heading' => 'تحليل الإيرادات',
        'description' => 'توزيع الإيرادات حسب الشهر',
        'filters' => [
            '3' => 'آخر 3 أشهر',
            '6' => 'آخر 6 أشهر',
            '12' => 'آخر 12 شهر',
        ],
        'datasets' => [
            'gross_revenue' => 'إجمالي الإيرادات (ريال)',
            'platform_commission' => 'عمولة المنصة (ريال)',
            'owner_payout' => 'صافي المالك (ريال)',
        ],
        'axes' => [
            'y_title' => 'المبلغ (ريال)',
            'x_title' => 'الشهر',
        ],
    ],

    'hall-recent-bookings' => [
        'heading' => 'أحدث الحجوزات',
        'description' => 'آخر نشاط للحجوزات في هذه القاعة',
        'columns' => [
            'booking_number' => 'رقم الحجز',
            'customer' => 'العميل',
            'time_slot' => 'الفترة',
            'status' => 'الحالة',
            'amount' => 'المبلغ',
            'booked' => 'تاريخ الحجز',
        ],
        'filters' => [
            'status' => 'الحالة',
            'payment' => 'الدفع',
            'upcoming' => 'القادمة فقط',
        ],
        'messages' => [
            'copy_success' => 'تم نسخ رقم الحجز',
        ],
        'empty_state' => [
            'heading' => 'لا توجد حجوزات بعد',
            'description' => 'لا توجد سجلات حجوزات لهذه القاعة.',
        ],
    ],

    'hall-booking-trend' => [
        'heading' => 'اتجاهات الحجوزات',
        'description' => 'نشاط الحجوزات عبر الوقت',
        'filters' => [
            '30' => 'آخر 30 يوم',
            '60' => 'آخر 60 يوم',
            '90' => 'آخر 90 يوم',
            '180' => 'آخر 6 أشهر',
        ],
        'datasets' => [
            'confirmed_completed' => 'مؤكدة/مكتملة',
            'pending' => 'معلقة',
            'cancelled' => 'ملغاة',
        ],
        'axes' => [
            'y_title' => 'عدد الحجوزات',
            'x_title' => 'التاريخ',
        ],
    ],

    'hall-booking-status' => [
        'heading' => 'توزيع الحجوزات',
        'description' => 'الحجوزات حسب الحالة',
        'filters' => [
            'all' => 'كل الفترات',
            'month' => 'هذا الشهر',
            'quarter' => 'هذا الربع',
            'year' => 'هذه السنة',
        ],
    ],

    'owner-stats-overview' => [
        'my_halls' => 'قاعاتي',
        'active_halls_desc' => ':count قاعات نشطة',
        'total_earnings' => 'إجمالي الأرباح',
        'all_time_earnings' => 'إجمالي الأرباح',
        'monthly_earnings' => 'الأرباح الشهرية',
        'this_month' => 'هذا الشهر',
        'total_bookings' => 'إجمالي الحجوزات',
        'upcoming_desc' => ':count قادمة',
        'pending_bookings' => 'الحجوزات المعلقة',
        'requires_action' => 'تتطلب إجراء',
    ],
];
