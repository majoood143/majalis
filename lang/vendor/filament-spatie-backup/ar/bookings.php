<?php

return [
    // Resource Labels
    'resource' => [
        'label' => 'حجز',
        'plural_label' => 'الحجوزات',
        'navigation_label' => 'الحجوزات',
        'navigation_group' => 'إدارة الحجوزات',
        'navigation_icon' => 'heroicon-o-calendar-days',
    ],

    // Table Columns
    'columns' => [
        'booking_number' => 'رقم الحجز',
        'customer' => 'العميل',
        'customer_name' => 'اسم العميل',
        'customer_phone' => 'الهاتف',
        'hall' => 'القاعة',
        'hall_name' => 'اسم القاعة',
        'event_date' => 'تاريخ المناسبة',
        'event_time' => 'وقت المناسبة',
        'status' => 'الحالة',
        'total_amount' => 'المبلغ الإجمالي',
        'paid_amount' => 'المبلغ المدفوع',
        'remaining_amount' => 'المبلغ المتبقي',
        'guests_count' => 'عدد الضيوف',
        'event_type' => 'نوع المناسبة',
        'notes' => 'ملاحظات',
        'created_at' => 'تاريخ الحجز',
        'updated_at' => 'آخر تحديث',
        'payment_status' => 'حالة الدفع',
        'commission' => 'العمولة',
        'services' => 'الخدمات الإضافية',
    ],

    // Form Fields
    'fields' => [
        'booking_details' => 'تفاصيل الحجز',
        'customer_details' => 'بيانات العميل',
        'hall_details' => 'تفاصيل القاعة',
        'event_details' => 'تفاصيل المناسبة',
        'payment_details' => 'تفاصيل الدفع',
        'additional_info' => 'معلومات إضافية',

        'booking_number' => 'رقم الحجز',
        'customer_name' => 'اسم العميل',
        'customer_email' => 'البريد الإلكتروني',
        'customer_phone' => 'رقم الهاتف',
        'select_hall' => 'اختر القاعة',
        'event_date' => 'تاريخ المناسبة',
        'start_time' => 'وقت البداية',
        'end_time' => 'وقت النهاية',
        'event_type' => 'نوع المناسبة',
        'guests_count' => 'عدد الضيوف',
        'total_amount' => 'المبلغ الإجمالي',
        'paid_amount' => 'المبلغ المدفوع',
        'payment_method' => 'طريقة الدفع',
        'notes' => 'ملاحظات',
        'internal_notes' => 'ملاحظات داخلية',
        'special_requests' => 'طلبات خاصة',
        'services' => 'الخدمات الإضافية',
        'service_quantity' => 'الكمية',
        'service_price' => 'السعر',
        'service_total' => 'المجموع',
    ],

    // Status Options
    'statuses' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'cancelled' => 'ملغي',
        'completed' => 'مكتمل',
        'no_show' => 'لم يحضر',
    ],

    // Payment Status
    'payment_statuses' => [
        'pending' => 'قيد الانتظار',
        'partial' => 'دفع جزئي',
        'paid' => 'مدفوع',
        'refunded' => 'مسترد',
    ],

    // Event Types
    'event_types' => [
        'wedding' => 'زفاف',
        'engagement' => 'خطوبة',
        'birthday' => 'عيد ميلاد',
        'corporate' => 'فعالية شركة',
        'conference' => 'مؤتمر',
        'other' => 'أخرى',
    ],

    // Actions
    'actions' => [
        'create' => 'إضافة حجز',
        'edit' => 'تعديل الحجز',
        'delete' => 'حذف الحجز',
        'view' => 'عرض التفاصيل',
        'cancel' => 'إلغاء الحجز',
        'confirm' => 'تأكيد الحجز',
        'print' => 'طباعة الفاتورة',
        'send_email' => 'إرسال بريد إلكتروني',
        'add_payment' => 'إضافة دفعة',
        'export' => 'تصدير الحجوزات',
        'filter' => 'تصفية',
        'search' => 'البحث عن حجز...',
    ],

    // Filters
    'filters' => [
        'status' => 'الحالة',
        'date_range' => 'نطاق التاريخ',
        'hall' => 'القاعة',
        'payment_status' => 'حالة الدفع',
        'event_type' => 'نوع المناسبة',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
    ],

    // Messages
    'messages' => [
        'created' => 'تم إنشاء الحجز بنجاح',
        'updated' => 'تم تحديث الحجز بنجاح',
        'deleted' => 'تم حذف الحجز بنجاح',
        'confirmed' => 'تم تأكيد الحجز بنجاح',
        'cancelled' => 'تم إلغاء الحجز بنجاح',
        'payment_added' => 'تم إضافة الدفعة بنجاح',
        'email_sent' => 'تم إرسال البريد الإلكتروني بنجاح',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا الحجز؟',
        'confirm_cancel' => 'هل أنت متأكد من إلغاء هذا الحجز؟',
        'no_availability' => 'القاعة غير متاحة في التاريخ المحدد',
    ],

    // Validation Messages
    'validation' => [
        'customer_required' => 'بيانات العميل مطلوبة',
        'hall_required' => 'الرجاء اختيار قاعة',
        'date_required' => 'تاريخ المناسبة مطلوب',
        'time_conflict' => 'يوجد تعارض في الوقت مع حجز آخر',
        'past_date' => 'لا يمكن أن يكون تاريخ المناسبة في الماضي',
        'invalid_phone' => 'الرجاء إدخال رقم هاتف صحيح',
    ],

    // Help Text
    'help' => [
        'booking_number' => 'رقم مرجعي فريد يتم إنشاؤه تلقائياً',
        'event_time' => 'حدد وقت البداية والنهاية للمناسبة',
        'payment_note' => 'المبلغ المتبقي: :amount ريال عماني',
        'commission_info' => 'سيتم حساب العمولة تلقائياً',
    ],

    // Placeholders
    'placeholders' => [
        'search' => 'البحث برقم الحجز، اسم العميل...',
        'select_hall' => 'اختر قاعة',
        'select_status' => 'اختر الحالة',
        'notes' => 'أضف أي ملاحظات أو متطلبات خاصة',
    ],
];
