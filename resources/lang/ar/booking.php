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

        'booking_information' => 'معلومات الحجز',
        'hall_date_information' => 'معلومات القاعة والتاريخ',
        'customer_details' => 'تفاصيل العميل',
        'pricing_breakdown' => 'تفاصيل السعر',
        'advance_payment_details' => 'تفاصيل الدفع المقدم',
        'extra_services' => 'خدمات إضافية',
        'description' => 'اختر خدمات إضافية لهذا الحجز',
        'payment_type_helper' => 'تم تعيينه بواسطة إعدادات القاعة',
        'timestamps' => 'الطوابع الزمنية',
        'cancellation_details' => 'تفاصيل الإلغاء',
        'admin_notes' => 'ملاحظات المسؤول',

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
            'select_hall_first' => 'اختر القاعة والتاريخ والفترة الزمنية لرؤية الأسعار',
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

        'edit' => 'تعديل',
        'confirm' => 'تأكيد الحجز',
        'confirm_modal_heading' => 'تأكيد الحجز',
        'confirm_modal_description' => 'هل أنت متأكد من تأكيد هذا الحجز؟ سيتم إرسال إشعار للعميل.',
        'cancel' => 'إلغاء الحجز',
        'cancel_modal_heading' => 'إلغاء الحجز',
        'cancel_modal_description' => 'هل أنت متأكد من إلغاء هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
        'complete' => 'إكمال الحجز',
        'complete_modal_heading' => 'إكمال الحجز',
        'complete_modal_description' => 'هل تريد وضع علامة إكمال على هذا الحجز؟ سيتم إنهاء الحجز.',
        'download_invoice' => 'تحميل الفاتورة',
        'generate_invoice' => 'إنشاء فاتورة',
        'send_reminder' => 'إرسال تذكير',
        'send_reminder_modal_heading' => 'إرسال تذكير',
        'send_reminder_modal_description' => 'إرسال إشعار تذكير للعميل بخصوص حجزه القادم؟',
        'mark_balance_paid' => 'تسوية الرصيد',
        'create_modal_heading' => 'تأكيد إنشاء الحجز',
        'create_modal_description' => 'هل أنت متأكد من إنشاء هذا الحجز؟ يرجى التحقق من صحة جميع التفاصيل.',
        'create_modal_submit_label' => 'نعم، إنشاء الحجز',
        'send_email' => 'إرسال البريد الإلكتروني',
        'send_reminder' => 'إرسال تذكير',
        'contact_customer' => 'الاتصال بالعميل عبر واتساب',
        'add_note' => 'إضافة ملاحظة',
        'duplicate' => 'تكرار الحجز',
        'request_review' => 'طلب مراجعة',
        'view_new_booking' => 'عرض الحجز الجديد',
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

        'slot_already_booked_title' => 'الفترة محجوزة بالفعل',
        'slot_already_booked_body' => 'هذه الفترة محجوزة بالفعل (الحجز رقم :booking_number). يرجى اختيار تاريخ أو فترة زمنية مختلفة.',
        'guest_count_below_min_title' => 'عدد الضيوف أقل من الحد الأدنى',
        'guest_count_below_min_body' => 'الحد الأدنى للسعة هو :capacity_min ضيف. تم تعديل عدد الضيوف.',
        'guest_count_exceeds_max_title' => 'عدد الضيوف يتجاوز الحد الأقصى',
        'guest_count_exceeds_max_body' => 'الحد الأقصى للسعة هو :capacity_max ضيف.',
        'slot_just_booked_title' => 'الفترة محجوزة بالفعل',
        'slot_just_booked_body' => 'هذه الفترة تم حجزها للتو من قبل مستخدم آخر. يرجى اختيار فترة زمنية مختلفة.',
        'advance_payment_booking_title' => 'حجز بدفع مقدم',
        'advance_payment_booking_body' => 'هذا الحجز يتطلب دفع مقدم. يجب على العميل دفع :advance_amount ريال عماني مقدماً. رصيد :balance_due ريال عماني مستحق قبل الحدث.',
        'booking_created_title' => 'تم إنشاء الحجز بنجاح',
        'booking_summary_title' => '📋 ملخص الحجز',
        'booking_summary_body' => "**الحجز:** :booking_number\n**المبلغ الإجمالي:** :total_amount ريال عماني\n**نوع الدفع:** دفع مقدم\n**المبلغ المقدم المطلوب:** :advance_amount ريال عماني\n**الرصيد المستحق:** :balance_due ريال عماني\n\nيجب على العميل دفع المبلغ المقدم قبل تأكيد الحدث.",
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
        'no_notes_provided' => 'لا توجد ملاحظات',
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
        'event_details_placeholder' => 'وصف المناسبة...',
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


    // Form
    'form' => [
        'cancellation_reason' => 'سبب الإلغاء',
        'cancellation_reason_placeholder' => 'أدخل سبب الإلغاء...',
        'balance_payment_method' => 'طريقة الدفع',
        'balance_payment_reference' => 'مرجع الدفع',
        'balance_payment_reference_placeholder' => 'رقم المعاملة أو رقم الإيصال',
        'payment_date' => 'تاريخ الدفع',
    ],

    // Notifications
    'notifications' => [
        'booking_confirmed_title' => 'تم تأكيد الحجز بنجاح',
        'booking_cancelled_title' => 'تم إلغاء الحجز بنجاح',
        'booking_completed_title' => 'تم إكمال الحجز بنجاح',
        'invoice_not_available_title' => 'الفاتورة غير متوفرة',
        'invoice_generated_title' => 'تم إنشاء الفاتورة بنجاح',
        'invoice_generated_body' => 'تم حفظ الفاتورة باسم: :filename',
        'invoice_generation_failed_title' => 'فشل إنشاء الفاتورة',
        'reminder_sent_title' => 'تم إرسال التذكير بنجاح',
        'balance_marked_paid_title' => 'تم تسوية الرصيد',
        'balance_marked_paid_body' => 'تم تسجيل دفعة الرصيد بنجاح.',
        'balance_already_paid_title' => 'الرصيد مدفوع بالفعل',
        'balance_already_paid_body' => 'تم دفع الرصيد لهذا الحجز بالفعل.',
        'booking_confirmed' => 'تم تأكيد الحجز بنجاح',
        'booking_cancelled' => 'تم إلغاء الحجز بنجاح',
        'booking_completed' => 'تم إكمال الحجز بنجاح',
        'email_sent_title' => 'تم إرسال البريد الإلكتروني',
        'email_sent_body' => 'تم إرسال تأكيد الحجز إلى :email',
        'email_failed_title' => 'فشل إرسال البريد الإلكتروني',
        'email_failed_body' => 'تعذر إرسال البريد الإلكتروني: :error',
    ],



    // Labels
    'labels' => [
        'booking_number' => 'رقم الحجز',
        'status' => 'الحالة',
        'payment_status' => 'حالة الدفع',
        'hall' => 'القاعة',
        'location' => 'الموقع',
        'booking_date' => 'تاريخ الحجز',
        'time_slot' => 'الفترة الزمنية',
        'number_of_guests' => 'عدد الضيوف',
        'guests_suffix' => ' ضيف',
        'event_type' => 'نوع الحدث',
        'customer_name' => 'اسم العميل',
        'customer_email' => 'البريد الإلكتروني للعميل',
        'customer_phone' => 'هاتف العميل',
        'customer_notes' => 'ملاحظات العميل',
        'hall_price' => 'سعر القاعة',
        'services_price' => 'سعر الخدمات',
        'subtotal' => 'المجموع الفرعي',
        //'commission_amount' => 'مبلغ العمولة',
        'platform_fee' => 'رسوم المنصة',
        'total_amount' => 'المبلغ الإجمالي',
        'owner_payout' => 'دفع المالك',
        'payment_type' => 'نوع الدفع',
        'advance_amount' => 'المبلغ المقدم',
        'balance_due' => 'الرصيد المستحق',
        'balance_payment_status' => 'حالة دفع الرصيد',
        'balance_paid_at' => 'تاريخ دفع الرصيد',
        'balance_payment_method' => 'طريقة الدفع',
        'balance_payment_reference' => 'مرجع الدفع',
        'service_name' => 'الخدمة',
        'unit_price' => 'سعر الوحدة',
        'quantity' => 'الكمية',
        'total_price' => 'المجموع',
        'created_at' => 'تاريخ الإنشاء',
        'confirmed_at' => 'تاريخ التأكيد',
        'completed_at' => 'تاريخ الإكمال',
        'cancelled_at' => 'تاريخ الإلغاء',
        'cancellation_reason' => 'سبب الإلغاء',
        'refund_amount' => 'مبلغ الاسترداد',
        'admin_notes' => 'ملاحظات المسؤول',
        'registered_user' => 'مستخدم مسجل',
        'guest_user' => 'ضيف',
        'customer_notes_placeholder' => 'أدخل أي ملاحظات خاصة أو طلبات خاصة'
    ],

    // Placeholders
    'placeholders' => [
        'no_notes' => 'لا توجد ملاحظات',
        'balance_not_paid' => 'الرصيد لم يدفع بعد',
        'no_admin_notes' => 'لا توجد ملاحظات للمسؤول',
        'guest_booking' => 'حجز ضيف (بدون حساب مستخدم)',
        'no_notes' => ' No notes provided ',
    ],

    // Descriptions
    'descriptions' => [
        'advance_payment_pending' => '⚠️ هذا الحجز يتطلب دفع مقدم. يجب على العميل دفع الرصيد المتبقي قبل الحدث.',
        'advance_payment_paid' => '✅ هذا الحجز يتطلب دفع مقدم. تم دفع الرصيد.',
        'full_payment' => 'هذا حجز بدفع كامل. يدفع العميل المبلغ بالكامل.',
    ],

    // Statuses
    'statuses' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغى',
        'balance_paid' => 'الرصيد مدفوع',
        'balance_pending' => 'الرصيد قيد الانتظار',
    ],

    // Payment Statuses
    'payment_statuses' => [
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'refunded' => 'مسترجع',
        'partially_paid' => 'مدفوع جزئياً',
    ],

    // Payment Types
    'payment_types' => [
        'full' => 'دفع كامل',
        'advance' => 'دفع مقدم',
    ],

    // Time Slots
    'time_slots' => [
        'morning' => 'صباحاً',
        'afternoon' => 'ظهراً',
        'evening' => 'مساءً',
        'night' => 'ليلاً',
        'full_day' => 'يوم كامل',
    ],

    // Event Types
    'event_types' => [
        'wedding' => 'زفاف',
        'birthday' => 'عيد ميلاد',
        'corporate' => 'شركة',
        'graduation' => 'تخرج',
        'engagement' => 'خطوبة',
        'other' => 'آخر',
    ],

    // Payment Methods
    'payment_methods' => [
        'bank_transfer' => 'تحويل بنكي',
        'cash' => 'نقداً',
        'card' => 'بطاقة',
    ],

    // Messages
    'messages' => [
        'reference_copied' => 'تم نسخ المرجع!',

    ],

    'exceptions' => [
        'slot_already_booked' => 'الفترة محجوزة بالفعل',
    ],

    'invoice' => [
        // Header
        'title' => 'فاتورة',
        'number' => 'رقم الفاتورة',
        'date' => 'التاريخ',
        'print' => 'طباعة',
        'close' => 'إغلاق',

        // Company
        'company_tagline' => 'منصة حجز القاعات المميزة',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',

        // Bill To Section
        'bill_to' => 'فاتورة إلى',
        'customer_name' => 'الاسم',
        'account' => 'الحساب',

        // Booking Details
        'booking_details' => 'تفاصيل الحجز',
        'booking_date' => 'تاريخ المناسبة',
        'time_slot' => 'الفترة الزمنية',
        'guests' => 'الضيوف',
        'persons' => 'شخص',
        'event_type' => 'نوع المناسبة',
        'capacity' => 'السعة',

        // Table Headers
        'description' => 'الوصف',
        'quantity' => 'الكمية',
        'unit_price' => 'سعر الوحدة',
        'total' => 'المجموع',

        // Line Items
        'hall_rental' => 'إيجار القاعة',

        // Totals
        'hall_price' => 'سعر القاعة',
        'services_total' => 'مجموع الخدمات',
        'subtotal' => 'المجموع الفرعي',
        'platform_fee' => 'رسوم المنصة',
        'grand_total' => 'المجموع الكلي',
        'currency' => 'ر.ع',

        // Advance Payment
        'advance_payment_details' => 'تفاصيل الدفع المقدم',
        'advance_paid' => 'المبلغ المدفوع مقدماً',
        'balance_due' => 'المبلغ المتبقي',
        'balance_status' => 'الحالة',
        'paid_on' => 'تم الدفع في',
        'pending_payment' => 'في انتظار الدفع',

        // Footer
        'customer_notes' => 'ملاحظات العميل',
        'terms_title' => 'الشروط والأحكام',
        'terms_1' => 'يجب الدفع عند تأكيد الحجز.',
        'terms_2' => 'تطبق سياسة الإلغاء وفقاً لشروط القاعة.',
        'terms_3' => 'يرجى الحضور قبل 30 دقيقة من موعد المناسبة.',
        'thank_you' => 'شكراً لاختياركم لنا!',
    ],
    'modals' => [
        'send_invoice_email' => 'إرسال الفاتورة عبر البريد الإلكتروني',
        'send_reminder' => 'إرسال تذكير بالحجز',
        'admin_notes' => 'ملاحظات المشرف',
        'duplicate_booking' => 'تكرار الحجز',
        'duplicate_booking_description' => 'إنشاء حجز جديد بنفس التفاصيل لتاريخ مختلف.',
        'request_review' => 'طلب مراجعة العميل',
        'request_review_description' => 'إرسال طلب مراجعة عبر البريد الإلكتروني للعميل.',
        'complete_booking_description' => 'تحديد هذا الحجز كمكتمل. لا يمكن التراجع عن هذا الإجراء.',
        'confirm_booking_description' => 'تأكيد هذا الحجز وإعلام العميل.',
        'cancel_booking_description' => 'إلغاء هذا الحجز. لا يمكن التراجع عن هذا الإجراء.',
        'generate_invoice_description' => 'إنشاء فاتورة PDF لهذا الحجز.',
        'send_reminder_description' => 'إرسال إشعار تذكير للعميل بخصوص حجزه القادم.',
        'mark_balance_paid_description' => 'تسجيل دفعة الرصيد لهذا الحجز.',
        'mark_balance_paid_confirm' => 'هل أنت متأكد من تسجيل دفعة الرصيد لهذا الحجز؟',
        'duplicate_booking_confirm' => 'هل أنت متأكد من تكرار هذا الحجز؟',
        'confirm_booking' => 'هل أنت متأكد من تأكيد هذا الحجز؟',
        'cancel_booking_confirm' => 'هل أنت متأكد من إلغاء هذا الحجز؟',
        'complete_booking' => 'هل أنت متأكد من إكمال هذا الحجز؟',
        'generate_invoice_confirm' => 'هل أنت متأكد من إنشاء فاتورة لهذا الحجز؟',
        'send_reminder_confirm' => 'هل أنت متأكد من إرسال تذكير لهذا الحجز؟',
    ],

    'email' => [
        // ... existing keys ...

        // Invoice Email Template Keys
        'invoice_greeting' => 'عزيزي :name،',
        'invoice_intro' => 'يرجى الاطلاع على الفاتورة المرفقة لحجزك الأخير لدينا.',
        'invoice_pdf_attached' => 'تم إرفاق نسخة PDF من فاتورتك بهذه الرسالة لسجلاتك.',
        'invoice_questions' => 'إذا كان لديك أي استفسارات حول هذه الفاتورة، يرجى التواصل معنا.',
        'invoice_footer' => 'هذه رسالة آلية. يرجى عدم الرد على هذه الرسالة مباشرة.',

        // Financial Summary Labels
        'booking_summary' => 'ملخص الحجز',
        'financial_summary' => 'الملخص المالي',
        'hall_price' => 'سعر القاعة',
        'services_price' => 'الخدمات الإضافية',
        'subtotal' => 'المجموع الفرعي',
        'platform_fee' => 'رسوم المنصة',
        'total_amount' => 'المبلغ الإجمالي',

        // Payment Information Labels
        'payment_info' => 'معلومات الدفع',
        'payment_type' => 'نوع الدفع',
        'advance_paid' => 'الدفعة المقدمة',
        'balance_due' => 'الرصيد المتبقي',
        'balance_paid' => 'تم دفع الرصيد',

        // Status Labels
        'booking_status' => 'حالة الحجز',
        'payment_status' => 'حالة الدفع',

        // Extra Services
        'extra_services' => 'الخدمات الإضافية',

        // Guests suffix
        'persons' => 'شخص',
        'event_type' => 'نوع المناسبة',
    ],
    // Booking summary
    'summary' => 'ملخص الحجز',
    'hall' => 'القاعة',
    'number' => 'رقم الحجز',
    'date' => 'التاريخ',
    'time' => 'الوقت',
    'guests' => 'عدد الضيوف',
    'customer' => 'العميل',

    // Time slots
    'morning' => 'صباحي',
    'afternoon' => 'بعد الظهر',
    'evening' => 'مسائي',
    'full_day' => 'يوم كامل',
    // Header
    'my_bookings' => 'حجوزاتي',
    'booking_number' => 'رقم الحجز :number',
    'booking_details' => 'تفاصيل الحجز',
    'booking_date' => ':date • :time',

    // Status
    'status_confirmed' => 'مؤكد',
    'status_pending' => 'قيد الانتظار',
    'status_completed' => 'مكتمل',
    'status_cancelled' => 'ملغي',

    // Hall Information
    'hall_information' => 'معلومات القاعة',
    'hall_name_not_available' => 'اسم القاعة غير متاح',
    'city_not_available' => 'المدينة غير متاحة',
    'address_not_available' => 'العنوان غير متاح',
    'view_hall_details' => 'عرض تفاصيل القاعة ←',

    // Booking Information
    'booking_information' => 'معلومات الحجز',
    'booking_number_label' => 'رقم الحجز',
    'event_date' => 'تاريخ المناسبة',
    'time_slot' => 'الفترة الزمنية',
    'number_of_guests' => 'عدد الضيوف',
    'event_type' => 'نوع المناسبة',
    'booked_on' => 'تاريخ الحجز',
    'special_notes' => 'ملاحظات خاصة',

    // Customer Information
    'customer_information' => 'معلومات العميل',
    'name' => 'الاسم',
    '_email' => 'البريد الإلكتروني',
    'phone' => 'رقم الهاتف',

    // Extra Services
    'extra_services' => 'خدمات إضافية',
    'currency' => 'ريال عماني',

    // Payment Summary
    'payment_summary' => 'ملخص الدفع',
    'hall_price' => 'سعر القاعة',
    'extra_services_price' => 'خدمات إضافية',
    'subtotal' => 'المجموع الفرعي',
    'platform_fee' => 'رسوم المنصة',
    'total_amount' => 'المبلغ الإجمالي',
    'payment_status' => 'حالة الدفع',
    'payment_status_paid' => 'مدفوع',
    'payment_status_pending' => 'قيد الانتظار',
    'payment_status_failed' => 'فشل',
    'complete_payment' => 'إتمام الدفع',

    // Actions
    '_actions' => 'الإجراءات',
    'cancel_booking' => 'إلغاء الحجز',
    'view_hall_details_btn' => 'عرض تفاصيل القاعة',
    'back_to_all_bookings' => 'العودة إلى جميع الحجوزات',

    // Cancel Modal
    'cancel_booking_title' => 'إلغاء الحجز',
    'cancel_booking_confirmation' => 'هل أنت متأكد من رغبتك في إلغاء هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
    'confirm_cancel' => 'نعم، إلغاء',
    'keep_booking' => 'لا، الاحتفاظ به',

    // Bookings List Page
    'view_and_manage' => 'عرض وإدارة جميع حجوزات القاعات الخاصة بك',
    'book_new_hall' => 'حجز قاعة جديدة',
    //'status' => 'الحالة',
    'all_statuses' => 'جميع الحالات',
    'from_date' => 'من تاريخ',
    'to_date' => 'إلى تاريخ',
    'apply_filters' => 'تطبيق التصفية',
    'unnamed_hall' => 'قاعة بدون اسم',
    'unknown_city' => 'مدينة غير معروفة',
    'hall_image' => 'صورة القاعة',
    'booking_date' => 'تاريخ الحجز',
    'guests' => 'عدد الضيوف',
    'total' => 'الإجمالي',
    'view_details' => 'عرض التفاصيل',

    // Empty State
    'no_bookings_found' => 'لا توجد حجوزات',
    'no_bookings_message' => 'لم تقم بأي حجوزات بعد. ابدأ باستكشاف قاعاتنا!',
    'browse_halls' => 'تصفح القاعات',
];
