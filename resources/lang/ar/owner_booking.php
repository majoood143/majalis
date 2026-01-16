<?php

return [
    'booking' => 'الحجز',
    'bookings' => 'الحجوزات',

    'navigation' => [
        'group' => 'الحجوزات',
        'badge_tooltip' => 'حجوزات قيد الانتظار تحتاج إلى الاهتمام',
    ],

    'general' => [
        'na' => 'غير متوفر',
    ],

    'time_slots' => [
        'morning' => 'صباح',
        'afternoon' => 'ظهر',
        'evening' => 'مساء',
        'full_day' => 'يوم كامل',
    ],

    'status' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',

        'pending_info' => 'هذا الحجز في انتظار التأكيد. يرجى المراجعة والموافقة أو الرفض.',
        'confirmed_info' => 'تم تأكيد هذا الحجز. تم إخطار العميل.',
        'completed_info' => 'تم اكتمال هذا الحدث بنجاح.',
        'cancelled_info' => 'تم إلغاء هذا الحجز.',
        'cancelled_reason' => 'السبب:',
    ],

    'payment' => [
        'pending' => 'قيد الانتظار',
        'partial' => 'جزئي',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'refunded' => 'تم الاسترجاع',
    ],

    'payment_methods' => [
        'cash' => 'نقداً',
        'bank_transfer' => 'تحويل بنكي',
        'card' => 'بطاقة (POS)',
    ],

    'form' => [
        'sections' => [
            'booking_information' => 'معلومات الحجز',
            'booking_information_description' => 'تفاصيل الحجز الأساسية',
            'customer_information' => 'معلومات العميل',
            'customer_information_description' => 'تفاصيل الاتصال للعميل',
            'payment_information' => 'معلومات الدفع',
            'payment_information_description' => 'التفاصيل المالية لهذا الحجز',
            'booking_status' => 'حالة الحجز',
        ],

        'fields' => [
            'booking_number' => 'رقم الحجز',
            'hall' => 'القاعة',
            'event_date' => 'تاريخ الحدث',
            'time_slot' => 'الفترة الزمنية',
            'event_type' => 'نوع الحدث',
            'number_of_guests' => 'عدد الضيوف',
            'customer_name' => 'اسم العميل',
            'email' => 'البريد الإلكتروني',
            'phone' => 'الهاتف',
            'customer_notes' => 'ملاحظات العميل',
            'hall_price' => 'سعر القاعة',
            'services_price' => 'سعر الخدمات',
            'total_amount' => 'المبلغ الإجمالي',
            'your_earnings' => 'أرباحك',
            'your_earnings_help' => 'المبلغ بعد عمولة المنصة',
            'advance_paid' => 'المدفوع مسبقاً',
            'balance_due' => 'المتبقي',
            'current_status' => 'الحالة الحالية',
        ],
    ],

    'table' => [
        'columns' => [
            'booking_number' => 'رقم الحجز #',
            'hall' => 'القاعة',
            'customer' => 'العميل',
            'event_date' => 'تاريخ الحدث',
            'time' => 'الوقت',
            'status' => 'الحالة',
            'your_earnings' => 'أرباحك',
            'payment' => 'الدفع',
            'balance' => 'المتبقي',
            'guests' => 'الضيوف',
            'booked_on' => 'تم الحجز في',
        ],

        'copy_messages' => [
            'booking_number' => 'تم نسخ رقم الحجز',
        ],

        'empty_state' => [
            'heading' => 'لا توجد حجوزات بعد',
            'description' => 'عندما يحجز العملاء قاعاتك، ستظهر هنا.',
        ],
    ],

    'filters' => [
        'hall' => 'القاعة',
        'booking_status' => 'حالة الحجز',
        'payment_status' => 'حالة الدفع',
        'from_date' => 'من تاريخ',
        'until_date' => 'حتى تاريخ',
        'from' => 'من',
        'until' => 'حتى',
        'upcoming_events' => 'الأحداث القادمة',
        'all_bookings' => 'جميع الحجوزات',
        'upcoming_only' => 'القادمة فقط',
        'past_only' => 'السابقة فقط',
        'needs_action' => 'تحتاج إلى إجراء',
    ],

    'actions' => [
        'approve' => [
            'label' => 'الموافقة',
            'modal_heading' => 'الموافقة على الحجز',
            'modal_description' => 'هل أنت متأكد أنك تريد الموافقة على هذا الحجز؟ سيتم إخطار العميل.',
            'modal_submit_label' => 'نعم، الموافقة',
        ],

        'reject' => [
            'label' => 'الرفض',
            'modal_heading' => 'رفض الحجز',
            'modal_description' => 'هل أنت متأكد أنك تريد رفض هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
            'reason_label' => 'سبب الرفض',
            'reason_placeholder' => 'يرجى تقديم سبب لرفض هذا الحجز...',
            'cancellation_reason_prefix' => 'مرفوض من قبل مالك القاعة: ',
        ],

        'mark_balance' => [
            'label' => 'تسجيل المبلغ المتبقي',
            'modal_heading' => 'تسديد المبلغ المتبقي',
            'modal_description' => 'سجل أن المبلغ المتبقي قد تم استلامه من العميل.',
            'balance_info' => 'المبلغ المتبقي',
            'payment_method_label' => 'طريقة الدفع',
            'reference_label' => 'رقم المرجع/الإيصال',
            'reference_placeholder' => 'رقم المرجع اختياري',
            'notes_label' => 'ملاحظات',
            'notes_placeholder' => 'أي ملاحظات إضافية...',
            'admin_note' => 'تم استلام مبلغ :amount ريال عماني عن طريق :method في :date',
            'notes_prefix' => 'ملاحظات: ',
        ],

        'contact' => [
            'label' => 'اتصال',
            'call' => 'الاتصال بالعميل',
            'email' => 'إرسال بريد إلكتروني للعميل',
            'whatsapp' => 'واتساب',
        ],

        'bulk' => [
            'export' => 'تصدير المحدد',
        ],
    ],

    'notifications' => [
        'approve' => [
            'title' => 'تمت الموافقة على الحجز',
            'body' => 'تمت الموافقة على الحجز رقم :number بنجاح.',
        ],

        'reject' => [
            'title' => 'تم رفض الحجز',
            'body' => 'تم رفض الحجز رقم :number.',
        ],

        'mark_balance' => [
            'title' => 'تم تسجيل المبلغ المتبقي',
            'body' => 'تم تسجيل المبلغ المتبقي للحجز رقم :number.',
        ],

        'export' => [
            'title' => 'بدء التصدير',
            'body' => 'يتم إعداد التصدير الخاص بك...',
        ],
    ],

    'infolist' => [
        'copy_messages' => [
            'copied' => 'تم النسخ!',
        ],

        'placeholders' => [
            'not_specified' => 'غير محدد',
            'no_notes' => 'لا توجد ملاحظات',
            'not_yet_received' => 'لم يتم الاستلام بعد',
            'not_confirmed' => 'غير مؤكد',
            'not_completed' => 'غير مكتمل',
            'not_cancelled' => 'غير ملغي',
        ],

        'sections' => [
            'header' => [
                'booking_number' => 'رقم الحجز',
                'status' => 'الحالة',
                'payment' => 'الدفع',
                'booked_on' => 'تم الحجز في',
            ],

            'event_details' => [
                'event_details' => 'تفاصيل الحدث',
                'hall' => 'القاعة',
                'event_date' => 'تاريخ الحدث',
                'time_slot' => 'الفترة الزمنية',
                'event_type' => 'نوع الحدث',
                'expected_guests' => 'الضيوف المتوقعين',
                'guests_suffix' => 'ضيف',
            ],

            'customer_information' => [
                'customer_information' => 'معلومات العميل',
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'phone' => 'الهاتف',
                'customer_notes' => 'ملاحظات العميل',
            ],

            'financial_summary' => [
                'financial_summary' => 'ملخص مالي',
                'hall_price' => 'سعر القاعة',
                'services' => 'الخدمات',
                'total_amount' => 'المبلغ الإجمالي',
                'your_earnings' => 'أرباحك',
                'advance_payment_details' => 'تفاصيل الدفع المسبق',
                'advance_paid' => 'المدفوع مسبقاً',
                'balance_due' => 'المبلغ المتبقي',
                'balance_received' => 'تم استلام المبلغ المتبقي',
                'payment_method' => 'طريقة الدفع',
            ],

            'extra_services' => [
                'extra_services' => 'خدمات إضافية',
                'service' => 'الخدمة',
                'qty' => 'الكمية',
                'unit_price' => 'سعر الوحدة',
                'total' => 'الإجمالي',
            ],

            'booking_timeline' => [
                'booking_timeline' => 'الجدول الزمني للحجز',
                'booked' => 'تم الحجز',
                'confirmed' => 'تم التأكيد',
                'completed' => 'تم الإكمال',
                'cancelled' => 'تم الإلغاء',
                'cancellation_reason' => 'سبب الإلغاء',
            ],
        ],
    ],

    'stats' => [
        'pending_approval' => 'قيد انتظار الموافقة',
        'pending_bookings_description' => ':count حجز تحتاج إلى مراجعتك',
        'all_reviewed' => 'تمت مراجعة جميع الحجوزات',

        'upcoming_events' => 'الأحداث القادمة',
        'today_events_description' => ':count حدث(أحداث) اليوم',
        'upcoming_bookings_description' => 'حجوزات قادمة مؤكدة',

        'this_month_earnings' => 'أرباح هذا الشهر',
        'revenue_increase' => 'زيادة بنسبة :percent% عن الشهر الماضي',
        'revenue_decrease' => 'انخفاض بنسبة :percent% عن الشهر الماضي',

        'balance_to_collect' => 'رصيد للتحصيل',
        'balance_from_advance' => 'من حجوزات الدفع المسبق',
        'no_pending_balances' => 'لا توجد أرصدة معلقة',


    ],

    'pages' => [
        'list' => [
            'export_label' => 'تصدير',
            'export_notification' => 'وظيفة التصدير قريباً',

            'tabs' => [
                'all' => 'جميع الحجوزات',
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'upcoming' => 'قادمة',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
            ],
        ],
    ],
];
