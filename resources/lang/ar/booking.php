<?php

declare(strict_types=1);

/**
 * Arabic Translation File for Booking Resource (ملف الترجمة العربية لمورد الحجز)
 *
 * This file contains all Arabic translation strings for the BookingResource
 * in the Majalis Hall Booking Management System.
 *
 * يحتوي هذا الملف على جميع سلاسل الترجمة العربية لمورد الحجز
 * في نظام إدارة حجز القاعات مجالس
 *
 * @package    Majalis
 * @subpackage Translations
 * @author     Majalis Development Team
 * @version    1.0.0
 * @since      2025-01-01
 *
 * Usage:
 * - In Filament Resource: __('booking.key.subkey')
 * - In Blade: {{ __('booking.key.subkey') }}
 * - With parameters: __('booking.notifications.slot_booked', ['number' => $bookingNumber])
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Resource Labels (تسميات المورد)
    |--------------------------------------------------------------------------
    |
    | Labels for resource navigation and general identification.
    | تسميات التنقل والتعريف العام للمورد
    |
    */
    'resource' => [
        'label' => 'حجز',
        'plural_label' => 'الحجوزات',
        'navigation_label' => 'الحجوزات',
        'navigation_group' => 'إدارة الحجوزات',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Sections (أقسام النموذج)
    |--------------------------------------------------------------------------
    |
    | Section titles and descriptions for the booking form.
    | عناوين وأوصاف أقسام نموذج الحجز
    |
    */
    'sections' => [
        'hall_selection' => [
            'title' => 'اختيار القاعة',
            'description' => 'تصفية واختيار القاعة للحجز',
        ],
        'booking_details' => [
            'title' => 'تفاصيل الحجز',
            'description' => 'أدخل معلومات الحجز',
        ],
        'customer_details' => [
            'title' => 'بيانات العميل',
            'description' => 'معلومات الاتصال بالعميل',
        ],
        'extra_services' => [
            'title' => 'الخدمات الإضافية',
            'description' => 'اختر خدمات إضافية لهذا الحجز',
        ],
        'pricing' => [
            'title' => 'ملخص الأسعار',
            'description' => 'تفصيل تكلفة الحجز',
        ],
        'pricing_breakdown' => [
            'title' => 'تفصيل الأسعار',
            'description' => 'تحليل التكلفة التفصيلي',
        ],
        'status_payment' => [
            'title' => 'الحالة والدفع',
            'description' => 'حالة الحجز والدفع',
        ],
        'timestamps' => [
            'title' => 'التواريخ والأوقات',
            'description' => 'سجل تتبع العمليات',
        ],
        'cancellation_details' => [
            'title' => 'تفاصيل الإلغاء',
            'description' => 'معلومات حول إلغاء الحجز',
        ],
        'admin_notes' => [
            'title' => 'ملاحظات المسؤول',
            'description' => 'ملاحظات داخلية للمسؤولين',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Fields (حقول النموذج)
    |--------------------------------------------------------------------------
    |
    | Labels, placeholders, and helper texts for form fields.
    | التسميات والنصوص التوضيحية والنصوص المساعدة لحقول النموذج
    |
    */
    'fields' => [
        // Hall Selection (اختيار القاعة)
        'region_id' => [
            'label' => 'المحافظة',
            'placeholder' => 'اختر المحافظة',
            'helper' => 'اختر المحافظة لتصفية المدن',
        ],
        'city_id' => [
            'label' => 'المدينة',
            'placeholder' => 'اختر المدينة',
            'helper' => 'اختر المدينة لتصفية القاعات',
        ],
        'hall_id' => [
            'label' => 'القاعة',
            'placeholder' => 'اختر القاعة',
            'helper' => 'اختر القاعة للمتابعة',
        ],

        // Booking Details (تفاصيل الحجز)
        'booking_number' => [
            'label' => 'رقم الحجز',
            'helper' => 'معرف فريد يتم إنشاؤه تلقائياً',
        ],
        'user_id' => [
            'label' => 'العميل',
            'placeholder' => 'اختر العميل',
            'helper' => 'اختر العميل الذي يقوم بهذا الحجز',
        ],
        'booking_date' => [
            'label' => 'تاريخ المناسبة',
            'placeholder' => 'اختر التاريخ',
            'helper' => 'اختر التاريخ لرؤية الفترات الزمنية المتاحة',
            'helper_select_hall' => 'اختر القاعة أولاً',
        ],
        'time_slot' => [
            'label' => 'الفترة الزمنية',
            'placeholder' => 'اختر الفترة الزمنية',
            'helper' => 'اختر القاعة والتاريخ أولاً',
            'helper_available' => ':count فترة/فترات متاحة',
            'helper_all_booked' => 'جميع الفترات محجوزة لهذا التاريخ',
        ],
        'number_of_guests' => [
            'label' => 'عدد الضيوف',
            'placeholder' => 'أدخل عدد الضيوف',
            'helper' => 'السعة: :min - :max ضيف',
            'helper_select_hall' => 'اختر القاعة أولاً',
        ],
        'event_type' => [
            'label' => 'نوع المناسبة',
            'placeholder' => 'اختر نوع المناسبة',
            'helper' => 'نوع المناسبة المقامة',
        ],

        // Customer Details (بيانات العميل)
        'customer_name' => [
            'label' => 'اسم العميل',
            'placeholder' => 'أدخل اسم العميل',
        ],
        'customer_email' => [
            'label' => 'البريد الإلكتروني',
            'placeholder' => 'أدخل البريد الإلكتروني',
        ],
        'customer_phone' => [
            'label' => 'رقم الهاتف',
            'placeholder' => 'أدخل رقم الهاتف',
        ],
        'customer_notes' => [
            'label' => 'ملاحظات العميل',
            'placeholder' => 'أدخل أي طلبات خاصة أو ملاحظات',
        ],

        // Extra Services (الخدمات الإضافية)
        'service_id' => [
            'label' => 'الخدمة',
            'placeholder' => 'اختر خدمة',
        ],
        'service_name' => [
            'label' => 'اسم الخدمة',
        ],
        'unit_price' => [
            'label' => 'سعر الوحدة',
        ],
        'quantity' => [
            'label' => 'الكمية',
        ],
        'total_price' => [
            'label' => 'الإجمالي',
        ],

        // Pricing (الأسعار)
        'hall_price' => [
            'label' => 'سعر القاعة',
            'helper' => 'اختر القاعة والتاريخ والفترة الزمنية لرؤية السعر',
            'helper_custom' => 'سعر مخصص لهذا التاريخ/الفترة',
            'helper_default' => 'السعر الافتراضي للقاعة لفترة :slot',
        ],
        'services_price' => [
            'label' => 'سعر الخدمات',
        ],
        'subtotal' => [
            'label' => 'المجموع الفرعي',
        ],
        'commission_rate' => [
            'label' => 'نسبة العمولة',
        ],
        'commission_amount' => [
            'label' => 'مبلغ العمولة',
        ],
        'platform_fee' => [
            'label' => 'رسوم المنصة',
        ],
        'total_amount' => [
            'label' => 'المبلغ الإجمالي',
        ],
        'owner_payout' => [
            'label' => 'مستحقات المالك',
        ],

        // Status & Payment (الحالة والدفع)
        'status' => [
            'label' => 'حالة الحجز',
            'placeholder' => 'اختر الحالة',
        ],
        'payment_status' => [
            'label' => 'حالة الدفع',
            'placeholder' => 'اختر حالة الدفع',
        ],
        'payment_method' => [
            'label' => 'طريقة الدفع',
            'placeholder' => 'اختر طريقة الدفع',
        ],
        'payment_reference' => [
            'label' => 'مرجع الدفع',
            'placeholder' => 'أدخل مرجع الدفع',
        ],

        // Cancellation (الإلغاء)
        'cancellation_reason' => [
            'label' => 'سبب الإلغاء',
            'placeholder' => 'أدخل سبب الإلغاء',
        ],
        'refund_amount' => [
            'label' => 'مبلغ الاسترداد',
        ],

        // Admin (المسؤول)
        'admin_notes' => [
            'label' => 'ملاحظات المسؤول',
            'placeholder' => 'أدخل ملاحظات داخلية',
        ],

        // Timestamps (التواريخ والأوقات)
        'created_at' => [
            'label' => 'تاريخ الإنشاء',
        ],
        'confirmed_at' => [
            'label' => 'تاريخ التأكيد',
        ],
        'completed_at' => [
            'label' => 'تاريخ الإكمال',
        ],
        'cancelled_at' => [
            'label' => 'تاريخ الإلغاء',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Columns (أعمدة الجدول)
    |--------------------------------------------------------------------------
    |
    | Labels for table columns in the list view.
    | تسميات أعمدة الجدول في عرض القائمة
    |
    */
    'table' => [
        'columns' => [
            'booking_number' => 'رقم الحجز',
            'hall' => 'القاعة',
            'customer_name' => 'العميل',
            'booking_date' => 'تاريخ المناسبة',
            'time_slot' => 'الفترة',
            'number_of_guests' => 'الضيوف',
            'total_amount' => 'الإجمالي',
            'status' => 'الحالة',
            'payment_status' => 'الدفع',
            'created_at' => 'الإنشاء',
        ],
        'filters' => [
            'status' => 'تصفية حسب الحالة',
            'payment_status' => 'تصفية حسب الدفع',
            'date_range' => 'نطاق التاريخ',
            'hall' => 'تصفية حسب القاعة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tabs (علامات التبويب)
    |--------------------------------------------------------------------------
    |
    | Labels for list view tabs.
    | تسميات علامات التبويب في عرض القائمة
    |
    */
    'tabs' => [
        'all' => 'الكل',
        'pending' => 'معلق',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'today' => 'اليوم',
        'upcoming' => 'القادمة',
        'past' => 'السابقة',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuses (الحالات)
    |--------------------------------------------------------------------------
    |
    | Booking and payment status labels.
    | تسميات حالات الحجز والدفع
    |
    */
    'statuses' => [
        'booking' => [
            'pending' => 'معلق',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
        ],
        'payment' => [
            'pending' => 'معلق',
            'paid' => 'مدفوع',
            'partial' => 'جزئي',
            'refunded' => 'مسترد',
            'failed' => 'فشل',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Slots (الفترات الزمنية)
    |--------------------------------------------------------------------------
    |
    | Labels for available time slots.
    | تسميات الفترات الزمنية المتاحة
    |
    */
    'time_slots' => [
        'morning' => 'صباحية',
        'afternoon' => 'ظهرية',
        'evening' => 'مسائية',
        'full_day' => 'يوم كامل',
        'morning_afternoon' => 'صباحية وظهرية',
        'afternoon_evening' => 'ظهرية ومسائية',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Types (أنواع المناسبات)
    |--------------------------------------------------------------------------
    |
    | Labels for event types.
    | تسميات أنواع المناسبات
    |
    */
    'event_types' => [
        'wedding' => 'زفاف',
        'engagement' => 'خطوبة',
        'birthday' => 'عيد ميلاد',
        'corporate' => 'مناسبة شركات',
        'conference' => 'مؤتمر',
        'seminar' => 'ندوة',
        'workshop' => 'ورشة عمل',
        'exhibition' => 'معرض',
        'graduation' => 'تخرج',
        'anniversary' => 'ذكرى سنوية',
        'memorial' => 'عزاء',
        'religious' => 'مناسبة دينية',
        'social' => 'تجمع اجتماعي',
        'other' => 'أخرى',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods (طرق الدفع)
    |--------------------------------------------------------------------------
    |
    | Labels for payment methods.
    | تسميات طرق الدفع
    |
    */
    'payment_methods' => [
        'thawani' => 'ثواني',
        'bank_transfer' => 'تحويل بنكي',
        'cash' => 'نقداً',
        'card' => 'بطاقة ائتمان/خصم',
        'cheque' => 'شيك',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions (الإجراءات)
    |--------------------------------------------------------------------------
    |
    | Labels for action buttons and their confirmations.
    | تسميات أزرار الإجراءات والتأكيدات
    |
    */
    'actions' => [
        'create' => 'إنشاء حجز',
        'edit' => 'تعديل الحجز',
        'view' => 'عرض الحجز',
        'delete' => 'حذف الحجز',
        'confirm' => [
            'label' => 'تأكيد',
            'modal_heading' => 'تأكيد الحجز',
            'modal_description' => 'هل أنت متأكد من تأكيد هذا الحجز؟',
            'modal_submit' => 'نعم، تأكيد',
        ],
        'cancel' => [
            'label' => 'إلغاء',
            'modal_heading' => 'إلغاء الحجز',
            'modal_description' => 'هل أنت متأكد من إلغاء هذا الحجز؟',
            'modal_submit' => 'نعم، إلغاء',
        ],
        'complete' => [
            'label' => 'إكمال',
            'modal_heading' => 'إكمال الحجز',
            'modal_description' => 'هل أنت متأكد من تحديد هذا الحجز كمكتمل؟',
            'modal_submit' => 'نعم، إكمال',
        ],
        'generate_invoice' => [
            'label' => 'إنشاء الفاتورة',
            'modal_heading' => 'إنشاء الفاتورة',
            'modal_description' => 'سيتم إنشاء فاتورة PDF لهذا الحجز.',
            'modal_submit' => 'إنشاء',
        ],
        'download_invoice' => [
            'label' => 'تحميل الفاتورة',
        ],
        'send_reminder' => [
            'label' => 'إرسال تذكير',
            'modal_heading' => 'إرسال تذكير',
            'modal_description' => 'إرسال إشعار تذكير للعميل؟',
            'modal_submit' => 'إرسال',
        ],
        'create_confirm' => [
            'modal_heading' => 'تأكيد إنشاء الحجز',
            'modal_description' => 'هل أنت متأكد من إنشاء هذا الحجز؟ يرجى التحقق من صحة جميع التفاصيل.',
            'modal_submit' => 'نعم، إنشاء الحجز',
        ],
        'add_service' => 'إضافة خدمة',
        'remove_service' => 'إزالة خدمة',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications (الإشعارات)
    |--------------------------------------------------------------------------
    |
    | Notification titles and messages.
    | عناوين ورسائل الإشعارات
    |
    */
    'notifications' => [
        'created' => [
            'title' => 'تم إنشاء الحجز',
            'body' => 'تم إنشاء الحجز بنجاح.',
        ],
        'updated' => [
            'title' => 'تم تحديث الحجز',
            'body' => 'تم تحديث الحجز بنجاح.',
        ],
        'confirmed' => [
            'title' => 'تم تأكيد الحجز',
            'body' => 'تم تأكيد الحجز بنجاح.',
        ],
        'cancelled' => [
            'title' => 'تم إلغاء الحجز',
            'body' => 'تم إلغاء الحجز بنجاح.',
        ],
        'completed' => [
            'title' => 'تم إكمال الحجز',
            'body' => 'تم تحديد الحجز كمكتمل.',
        ],
        'slot_already_booked' => [
            'title' => 'الفترة محجوزة مسبقاً',
            'body' => 'هذه الفترة الزمنية محجوزة مسبقاً (حجز رقم :number). يرجى اختيار تاريخ أو فترة زمنية مختلفة.',
        ],
        'slot_just_booked' => [
            'title' => 'الفترة محجوزة مسبقاً',
            'body' => 'تم حجز هذه الفترة الزمنية للتو من قبل مستخدم آخر. يرجى اختيار فترة زمنية مختلفة.',
        ],
        'no_available_slots' => [
            'title' => 'لا توجد فترات متاحة',
            'body' => 'جميع الفترات الزمنية محجوزة لهذا التاريخ. يرجى اختيار تاريخ آخر.',
        ],
        'guests_below_minimum' => [
            'title' => 'عدد الضيوف أقل من الحد الأدنى',
            'body' => 'الحد الأدنى للسعة هو :min ضيف. تم تعديل عدد الضيوف.',
        ],
        'guests_exceeds_maximum' => [
            'title' => 'عدد الضيوف يتجاوز الحد الأقصى',
            'body' => 'الحد الأقصى للسعة هو :max ضيف.',
        ],
        'invoice_generated' => [
            'title' => 'تم إنشاء الفاتورة',
            'body' => 'تم حفظ الفاتورة باسم: :filename',
        ],
        'invoice_error' => [
            'title' => 'فشل إنشاء الفاتورة',
            'body' => 'فشل في إنشاء الفاتورة: :error',
        ],
        'invoice_not_available' => [
            'title' => 'الفاتورة غير متوفرة',
            'body' => 'لم يتم إنشاء الفاتورة بعد.',
        ],
        'reminder_sent' => [
            'title' => 'تم إرسال التذكير',
            'body' => 'تم إرسال إشعار التذكير للعميل.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Texts & Messages (النصوص المساعدة والرسائل)
    |--------------------------------------------------------------------------
    |
    | General helper texts and informational messages.
    | النصوص المساعدة العامة والرسائل المعلوماتية
    |
    */
    'messages' => [
        'select_hall_first' => 'اختر القاعة أولاً',
        'select_date_first' => 'اختر التاريخ أولاً',
        'select_time_slot' => 'اختر الفترة الزمنية',
        'no_services_available' => 'لا توجد خدمات إضافية متاحة لهذه القاعة',
        'no_notes_provided' => 'لا توجد ملاحظات',
        'no_admin_notes' => 'لا توجد ملاحظات للمسؤول',
        'fully_booked' => 'محجوز بالكامل',
        'slots_available' => ':count فترة/فترات متاحة',
        'custom_price_applied' => 'سعر مخصص لهذا التاريخ/الفترة',
        'default_price' => 'السعر الافتراضي للقاعة لفترة :slot',
    ],

    /*
    |--------------------------------------------------------------------------
    | Infolist Labels (تسميات صفحة العرض)
    |--------------------------------------------------------------------------
    |
    | Labels specific to the view/infolist page.
    | تسميات خاصة بصفحة العرض/قائمة المعلومات
    |
    */
    'infolist' => [
        'booking_info' => 'معلومات الحجز',
        'event_details' => 'تفاصيل المناسبة',
        'hall_info' => 'معلومات القاعة',
        'financial_summary' => 'الملخص المالي',
        'service_details' => 'تفاصيل الخدمات',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages (رسائل التحقق)
    |--------------------------------------------------------------------------
    |
    | Custom validation error messages.
    | رسائل أخطاء التحقق المخصصة
    |
    */
    'validation' => [
        'hall_required' => 'يرجى اختيار قاعة.',
        'date_required' => 'يرجى اختيار تاريخ الحجز.',
        'time_slot_required' => 'يرجى اختيار الفترة الزمنية.',
        'customer_required' => 'يرجى اختيار العميل.',
        'guests_required' => 'يرجى إدخال عدد الضيوف.',
        'guests_min' => 'يجب أن يكون عدد الضيوف :min على الأقل.',
        'guests_max' => 'لا يمكن أن يتجاوز عدد الضيوف :max.',
        'slot_not_available' => 'الفترة الزمنية المختارة غير متاحة.',
        'date_past' => 'لا يمكن أن يكون تاريخ الحجز في الماضي.',
    ],
];
