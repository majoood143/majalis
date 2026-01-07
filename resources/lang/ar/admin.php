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


    // Resource Labels
    'hall' => 'القاعة',
    'halls' => 'القاعات',

    // Tab Labels
    'basic_info' => 'المعلومات الأساسية',
    'location' => 'الموقع',
    'capacity_pricing' => 'السعة والتسعير',
    'contact' => 'الاتصال',
    'features_media' => 'الميزات والوسائط',
    'settings' => 'الإعدادات',

    // Basic Info Tab
    'city' => 'المدينة',
    'owner' => 'المالك',
    'name_english' => 'الاسم (الإنجليزية)',
    'name_arabic' => 'الاسم (العربية)',
    'url_slug' => 'رابط الموقع',
    'area' => 'المساحة',
    'description_english' => 'الوصف (الإنجليزية)',
    'description_arabic' => 'الوصف (العربية)',

    // Advance Payment Tab
    'advance_payment' => 'الدفعة المقدمة',
    'advance_payment_settings' => 'إعدادات الدفعة المقدمة',
    'advance_payment_explanation' => 'قم بتكوين متطلبات الدفعة المقدمة لهذه القاعة. سيدفع العملاء هذا المبلغ مقدمًا عند الحجز.',
    'allows_advance_payment' => 'السماح بالدفعة المقدمة',
    'allows_advance_payment_help' => 'تمكين لطلب دفعة مقدمة للحجوزات',
    'advance_payment_type' => 'نوع الدفعة المقدمة',
    'advance_payment_type_help' => 'اختر كيفية حساب الدفعة المقدمة',
    'advance_type_fixed' => 'مبلغ ثابت',
    'advance_type_percentage' => 'نسبة من الإجمالي',
    'advance_payment_amount' => 'مبلغ الدفعة المقدمة',
    'advance_payment_amount_help' => 'المبلغ الثابت للدفع مقدمًا (ريال عماني)',
    'advance_payment_amount_placeholder' => 'أدخل المبلغ الثابت',
    'advance_payment_percentage' => 'نسبة الدفعة المقدمة',
    'advance_payment_percentage_help' => 'نسبة سعر الحجز الإجمالي للدفع مقدمًا',
    'advance_payment_percentage_placeholder' => 'أدخل النسبة',
    'minimum_advance_payment' => 'الحد الأدنى للدفعة المقدمة',
    'minimum_advance_payment_help' => 'تأكد من أن الدفعة المقدمة لا تقل عن هذا المبلغ (ريال عماني)',
    'minimum_advance_payment_placeholder' => 'أدخل الحد الأدنى للمبلغ',
    'advance_payment_preview' => 'معاينة الدفعة المقدمة',
    'advance_payment_preview_help' => 'معاينة كيفية عمل الدفعة المقدمة مع التسعير التجريبي',
    'preview_for_price' => 'معاينة لحجز بقيمة :price ريال عماني',
    'customer_pays_advance' => 'يدفع العميل (مقدمًا)',
    'balance_due_before_event' => 'الرصيد المستحق (قبل الحدث)',
    'advance_includes_services' => 'حساب الدفعة المقدمة يشمل الخدمات. الرصيد يشمل الرسوم الإضافية.',

    // Location Tab
    'full_address' => 'العنوان الكامل',
    'address_english' => 'العنوان (الإنجليزية)',
    'address_arabic' => 'العنوان (العربية)',
    'pick_location_on_map' => 'اختر الموقع على الخريطة',
    'hall_location' => 'موقع القاعة',
    'latitude' => 'خط العرض',
    'longitude' => 'خط الطول',
    'google_maps_url' => 'رابط خرائط جوجل',

    // Map Helper Texts
    'map_helper_click' => 'انقر على الخريطة لتعيين موقع القاعة، أو اسحب العلامة للتعديل.',
    'coordinate_helper' => 'يتم ملؤه تلقائيًا من الخريطة. يمكن أيضًا الإدخال يدويًا.',

    // Capacity & Pricing Tab
    'minimum_capacity' => 'الحد الأدنى للسعة',
    'maximum_capacity' => 'الحد الأقصى للسعة',
    'base_price_per_slot' => 'السعر الأساسي لكل فترة',
    'slot_specific_pricing' => 'التسعير الخاص بالفترة',
    'time_slot' => 'الفترة الزمنية',
    'price_omr' => 'السعر (ريال عماني)',

    // Contact Tab
    'phone_number' => 'رقم الهاتف',
    'whatsapp' => 'واتساب',
    'email_address' => 'عنوان البريد الإلكتروني',

    // Features & Media Tab
    'hall_features' => 'ميزات القاعة',
    'featured_image' => 'الصورة الرئيسية',
    'gallery_images' => 'صور المعرض',
    'video_url' => 'رابط الفيديو',

    // Settings Tab
    'active' => 'نشط',
    'featured' => 'مميز',
    'requires_approval' => 'يتطلب الموافقة',
    'cancellation_window' => 'فترة الإلغاء',
    'cancellation_fee' => 'رسوم الإلغاء',

    // Table Columns
    'image' => 'الصورة',
    'name' => 'الاسم',
    'capacity' => 'السعة',
    'price' => 'السعر',
    'bookings' => 'الحجوزات',
    'rating' => 'التقييم',

    // Units
    'sqm' => 'متر مربع',
    'guests' => 'ضيف',
    'hours' => 'ساعة',

    // Helper Texts
    'auto_generate_slug' => 'اتركه فارغًا للتوليد التلقائي من الاسم الإنجليزي',
    'recommended_image_size' => 'مستحسن: 1920x1080 بكسل',
    'max_images' => '10 صور كحد أقصى',
    'inactive_halls_hidden' => 'القاعات غير النشطة مخفية عن العملاء',
    'featured_halls_highlighted' => 'تظهر القاعات المميزة في الأقسام المميزة',
    'allow_cancellation_help' => 'الحد الأدنى من الساعات قبل الحجوزات للسماح بالإلغاء',
    'cancellation_fee_help' => 'نسبة الرسوم المفروضة عند الإلغاء',

    // Placeholders
    'enter_hall_name_english' => 'أدخل اسم القاعة بالإنجليزية',
    'enter_hall_name_arabic' => 'أدخل اسم القاعة بالعربية',
    'enter_full_address' => 'أدخل عنوان الشارع الكامل',
    'enter_address_english' => 'العنوان بالإنجليزية',
    'enter_address_arabic' => 'العنوان بالعربية',
    'enter_capacity_example' => 'مثال: 50',
    'enter_price_example' => 'مثال: 150.000',
    'phone_placeholder' => '+968 XXXX XXXX',
    'whatsapp_placeholder' => '+968 XXXX XXXX',
    'email_placeholder' => 'contact@hallname.com',
    'video_placeholder' => 'https://youtube.com/watch?v=...',

    // Table Filters
    'city_filter' => 'المدينة',
    'owner_filter' => 'المالك',
    'featured_filter' => 'مميز',
    'active_filter' => 'نشط',
    'min_capacity_filter' => 'الحد الأدنى للسعة',
    'max_capacity_filter' => 'الحد الأقصى للسعة',
    'featured_only' => 'المميزة فقط',
    'not_featured' => 'غير مميز',
    'active_only' => 'النشطة فقط',
    'inactive_only' => 'غير النشطة فقط',

    // Table Empty States
    'no_halls_found' => 'لم يتم العثور على قاعات',
    'create_first_hall' => 'أنشئ قاعتك الأولى للبدء.',

    // Messages
    'hall_created' => 'تم إنشاء القاعة بنجاح',
    'hall_updated' => 'تم تحديث القاعة بنجاح',
    'hall_deleted' => 'تم حذف القاعة بنجاح',

    // Additional
    'add_price_override' => 'إضافة تجاوز سعر',
    'override_prices_help' => 'تجاوز الأسعار لـ: الصباح، بعد الظهر، المساء، طوال اليوم',
    'select_features_help' => 'حدد جميع الميزات المتاحة في هذه القاعة',
    'disabled' => 'معطل',
    'optional_google_maps' => 'اختياري: الصق رابط خرائط جوجل لهذا الموقع',
    'youtube_vimeo_link' => 'رابط يوتيوب أو فيميو',
    'require_admin_approval' => 'يتطلب موافقة المسؤول لكل حجز',

    // Actions
    'actions' => [
        'export' => 'تصدير القاعات',
        'export_modal_heading' => 'تصدير بيانات القاعات',
        'export_modal_description' => 'تصدير جميع بيانات القاعات إلى ملف CSV.',
        'bulk_price_update' => 'تحديث الأسعار بالجملة',
        'bulk_price_modal_heading' => 'تحديث الأسعار بالجملة',
        'bulk_price_modal_description' => 'تحديث أسعار عدة قاعات دفعة واحدة.',
        'generate_slugs' => 'إنشاء الروابط الناقصة',
        'generate_slugs_modal_heading' => 'إنشاء الروابط الناقصة',
        'generate_slugs_modal_description' => 'إنشاء روابط URL للقاعات التي لا تملك واحدة.',
        'bulk_feature' => 'إدارة المميز بالجملة',
        'sync_availability' => 'مزامنة التوفر',
        'sync_availability_modal_heading' => 'إنشاء سجلات التوفر',
        'sync_availability_modal_description' => 'إنشاء فترات توفر لجميع القاعات للـ 3 أشهر القادمة.',
        'bulk_activation' => 'التفعيل بالجملة',
        'download' => 'تحميل الملف',
    ],

    // Fields
    'fields' => [
        'city' => 'المدينة',
        'city_filter' => 'تصفية حسب المدينة (اختياري)',
        'update_type' => 'نوع التحديث',
        'percentage' => 'النسبة المئوية (%)',
        'amount' => 'المبلغ (ريال عماني)',
        'action' => 'الإجراء',
        'status' => 'الحالة',
    ],

    // Options
    'options' => [
        'percentage_increase' => 'زيادة نسبية',
        'percentage_decrease' => 'تخفيض نسبي',
        'fixed_increase' => 'زيادة مبلغ ثابت',
        'fixed_decrease' => 'تخفيض مبلغ ثابت',
        'mark_featured' => 'وضع كمميز',
        'unmark_featured' => 'إزالة حالة المميز',
        'activate' => 'تفعيل',
        'deactivate' => 'تعطيل',
    ],

    // Tabs
    'tabs' => [
        'all' => 'جميع القاعات',
        'active' => 'نشطة',
        'inactive' => 'غير نشطة',
        'featured' => 'مميزة',
        'pending_approval' => 'بانتظار الموافقة',
        'high_capacity' => 'سعة عالية (500+)',
        'premium_price' => 'مميزة (1000+ ريال عماني)',
        'highly_rated' => 'تقييم عالي (4.5+)',
        'with_video' => 'تحتوي على فيديو',
        'incomplete' => 'ملف غير مكتمل',
        'no_bookings' => 'بدون حجوزات',
    ],

    // Export Headers
    'export' => [
        'name_en' => 'الاسم (الإنجليزية)',
        'name_ar' => 'الاسم (العربية)',
        'slug' => 'الرابط',
        'city' => 'المدينة',
        'owner' => 'المالك',
        'address' => 'العنوان',
        'latitude' => 'خط العرض',
        'longitude' => 'خط الطول',
        'capacity_min' => 'الحد الأدنى للسعة',
        'capacity_max' => 'الحد الأقصى للسعة',
        'base_price' => 'السعر الأساسي',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',
        'total_bookings' => 'إجمالي الحجوزات',
        'average_rating' => 'متوسط التقييم',
        'featured' => 'مميز',
        'active' => 'نشط',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'تم التصدير بنجاح',
        'export_success_body' => 'تم تصدير بيانات القاعات بنجاح.',
        'export_error' => 'فشل التصدير',
        'prices_updated' => 'تم تحديث الأسعار',
        'prices_updated_body' => 'تم تحديث :count قاعة بنجاح.',
        'slugs_generated' => 'تم إنشاء الروابط',
        'slugs_generated_body' => 'تم إنشاء :count رابط.',
        'feature_updated' => 'تم تحديث حالة التميز',
        'feature_updated_body' => 'تم تحديث :count قاعة بنجاح.',
        'activation_updated' => 'تم تحديث الحالة',
        'activation_updated_body' => 'تم تحديث :count قاعة بنجاح.',
        'availability_synced' => 'تمت مزامنة التوفر',
        'availability_synced_body' => 'تم إنشاء :count فترة توفر.',
        'update_error' => 'فشلت العملية',
    ],

    // Common
    'yes' => 'نعم',
    'no' => 'لا',

    // Stats
    'stats' => [
        'total_halls' => 'إجمالي القاعات',
        'total_halls_desc' => 'جميع القاعات في النظام',
        'active_halls' => 'القاعات النشطة',
        'active_halls_desc' => 'نشطة حالياً',
        'featured_halls' => 'القاعات المميزة',
        'featured_halls_desc' => 'مميزة',
        'pending_halls' => 'القاعات المعلقة',
        'pending_halls_desc' => 'بانتظار الموافقة',
        'average_price' => 'متوسط السعر',
        'average_price_desc' => 'لكل فترة',
    ],
];
