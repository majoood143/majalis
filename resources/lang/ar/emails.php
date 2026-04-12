<?php

return [
    'ticket' => [
        // Subject lines
        'submitted_admin_subject'    => 'تم تقديم تذكرة دعم جديدة - #:ticket_number',
        'submitted_customer_subject' => 'تم استقبال تذكرتك - #:ticket_number',

        // Admin email
        'submitted_admin_subtitle'   => 'تم تقديم تذكرة دعم جديدة',
        'submitted_admin_title'      => 'تذكرة دعم جديدة',
        'new_ticket_submitted'       => 'قدم العميل طلب دعم جديد',
        'admin_footer_note'          => 'يرجى مراجعة هذه التذكرة والرد عليها في أقرب وقت ممكن.',

        // Customer email
        'submitted_customer_subtitle'    => 'شكراً على تقديم طلبك',
        'submitted_customer_title'       => 'تم استقبال التذكرة',
        'submitted_customer_intro'       => 'لقد استقبلنا طلب الدعم الخاص بك وسيراجعه فريقنا قريباً.',
        'submitted_customer_message'     => 'يمكنك تتبع حالة طلبك في أي وقت بتسجيل الدخول إلى حسابك. نهدف للرد على جميع الطلبات خلال 24 ساعة.',
        'submitted_customer_footer'      => 'إذا لم تقدم هذا الطلب، يرجى تجاهل هذا البريد.',
        'greeting'                       => 'مرحباً :name،',

        // Common fields
        'reference_label'      => 'رقم المرجعية',
        'customer_information' => 'معلومات العميل',
        'ticket_details'       => 'تفاصيل التذكرة',
        'description'          => 'الوصف',
        'name'                 => 'الاسم',
        'email'                => 'عنوان البريد الإلكتروني',
        'submission_type'      => 'نوع التقديم',
        'guest_submission'     => 'تقديم من ضيف',
        'type'                 => 'النوع',
        'priority'             => 'الأولوية',
        'subject'              => 'الموضوع',
        'status'               => 'الحالة',

        // Buttons
        'view_ticket_btn'         => 'عرض تذكرتك',
        'view_and_respond_btn'    => 'عرض والرد',
    ],

    'booking' => [
        // Already present
        'regards' => 'مع تحياتنا،',
        'team'    => 'فريق :app',

        // Common fields shared across booking emails
        'greeting'        => 'مرحباً :name،',
        'booking_number'  => 'رقم الحجز',
        'hall'            => 'القاعة',
        'date'            => 'التاريخ',
        'time_slot'       => 'الفترة الزمنية',
        'guests'          => 'عدد الضيوف',
        'persons'         => 'أشخاص',
        'event_type'      => 'نوع المناسبة',
        'location'        => 'الموقع',
        'view_map'        => 'عرض على الخريطة',
        'your_booking'    => 'حجزك',
        'details_title'   => 'تفاصيل الحجز',
        'original_amount' => 'المبلغ الأصلي',
        'services_title'  => 'الخدمات الإضافية',
        'payment_summary' => 'ملخص الدفع',
        'hall_price'      => 'سعر القاعة',
        'services_price'  => 'الخدمات الإضافية',
        'discount'        => 'الخصم',
        'total_amount'    => 'المبلغ الإجمالي',
        'view_details'    => 'عرض تفاصيل الحجز',
        'download_confirmation' => 'تحميل التأكيد',

        // Payment status blocks
        'payment_complete'     => 'تم الدفع',
        'amount_paid'          => 'لقد دفعت :amount ريال عماني لهذا الحجز.',
        'payment_pending'      => 'الدفع معلق',
        'payment_pending_desc' => 'يرجى إتمام الدفع بمبلغ :amount ريال عماني لتأكيد حجزك.',

        // Advance/partial payment
        'advance_payment_received' => 'تم استلام الدفعة المقدمة',
        'advance_paid_desc'        => 'المبلغ المدفوع مقدماً: :advance ريال عماني — المتبقي: :balance ريال عماني',

        // Countdown (used with trans_choice)
        'days_until' => '{1} يوم حتى حجزك|[2,10] أيام حتى حجزك|[11,*] يوماً حتى حجزك',

        // Important notes
        'important_notes'       => 'ملاحظات مهمة',
        'note_arrive_early'     => 'يرجى الحضور قبل 15 دقيقة من موعدك المحدد.',
        'note_bring_id'         => 'يرجى إحضار هوية سارية للتحقق من هويتك.',
        'note_contact_changes'  => 'لأي تعديلات أو إلغاء، يرجى التواصل معنا قبل 24 ساعة على الأقل.',

        // ── Confirmed ──
        'confirmed' => [
            'subtitle'  => 'تم تأكيد حجزك',
            'title'     => 'تم تأكيد الحجز!',
            'intro'     => 'تم تأكيد حجزك وكل شيء جاهز لمناسبتك. إليك تفاصيل الحجز.',
            'questions' => 'إذا كان لديك أي استفسار، يرجى التواصل مع فريق الدعم.',
        ],

        // ── Cancelled (customer) ──
        'cancelled' => [
            'subtitle'          => 'تم إلغاء حجزك',
            'title'             => 'تم إلغاء الحجز',
            'intro'             => 'نأسف لإبلاغك بأن حجزك قد تم إلغاؤه.',
            'details_title'     => 'تفاصيل الحجز الملغى',
            'reason_title'      => 'سبب الإلغاء',
            'refund_title'      => 'معلومات الاسترداد',
            'refund_processing' => 'سيتم معالجة استرداد المبلغ خلال 5–7 أيام عمل.',
            'no_refund_title'   => 'معلومات الاسترداد',
            'no_refund_desc'    => 'وفقاً لسياسة الإلغاء، هذا الحجز غير مؤهل للاسترداد.',
            'what_next'         => 'ماذا بعد؟',
            'what_next_desc'    => 'يسعدنا مساعدتك في إيجاد قاعة أخرى. تصفح القاعات المتاحة واحجز مجدداً.',
            'browse_halls'      => 'تصفح القاعات',
            'questions'         => 'إذا كان لديك أي استفسار حول هذا الإلغاء، يرجى التواصل معنا.',
        ],

        // ── Completed ──
        'completed' => [
            'subtitle'         => 'اكتملت مناسبتك',
            'title'            => 'شكراً لاستخدامك مجالس!',
            'intro'            => 'نأمل أنك قضيت وقتاً رائعاً في :hall. شكراً لاختيارك لنا!',
            'event_summary'    => 'ملخص المناسبة',
            'review_title'     => 'شارك تجربتك',
            'review_desc'      => 'تعليقاتك تساعدنا على التحسين وتساعد العملاء الآخرين على اتخاذ قرارات أفضل.',
            'leave_review'     => 'اترك تقييماً',
            'why_review'       => 'لماذا تقييمك مهم',
            'why_review_1'     => 'ساعد العملاء الآخرين في اختيار القاعة المناسبة لمناسبتهم.',
            'why_review_2'     => 'مكّن أصحاب القاعات من تحسين خدماتهم.',
            'why_review_3'     => 'ساعدنا في الحفاظ على جودة منصتنا.',
            'book_again_title' => 'هل أنت مستعد للحجز مجدداً؟',
            'book_again_desc'  => 'هل كانت تجربتك رائعة؟ نفس القاعة متاحة للحجوزات المستقبلية.',
            'book_again_btn'   => 'احجز مجدداً',
            'thank_you'        => 'شكراً لك على ثقتك بنا. نتطلع لخدمتك مجدداً.',
        ],

        // ── Created ──
        'created' => [
            'subtitle'          => 'تم استلام طلب حجزك',
            'intro'             => 'شكراً لك! لقد استلمنا طلب حجزك وجاري معالجته.',
            'next_steps_title'  => 'الخطوات التالية',
            'next_steps_desc'   => 'تم إنشاء حجزك. ستتلقى تأكيداً بمجرد الموافقة عليه.',
            'awaiting_approval' => 'حجزك بانتظار موافقة مالك القاعة. سيتم إبلاغك بمجرد الموافقة.',
            'questions'         => 'إذا كان لديك أي استفسار، يرجى التواصل معنا على',
        ],
    ],

    // ── Payment link email ──
    'payment_link' => [
        'title'            => 'أكمل دفع حجزك',
        'intro'            => 'حجزك رقم :number محجوز. يرجى إتمام الدفع لتأكيده.',
        'advance_note'     => 'دفعة مقدمة — المبلغ المتبقي: :balance ريال عماني',
        'pay_now'          => 'ادفع الآن',
        'link_expiry_note' => 'رابط الدفع هذا ستنتهي صلاحيته. إذا لم تتمكن من النقر على الزر، انسخ الرابط وألصقه في متصفحك.',
        'footer_note'      => 'إذا لم تقم بهذا الحجز أو لديك أي استفسار، يرجى التواصل معنا.',
    ],

    // ── Status badges ──
    'status' => [
        'confirmed' => 'مؤكد',
        'cancelled'  => 'ملغى',
        'completed'  => 'مكتمل',
        'pending'    => 'قيد الانتظار',
        'paid'       => 'مدفوع',
        'failed'     => 'فشل',
    ],

    // ── Payment emails ──
    'payment' => [
        'status'         => 'حالة الدفع',
        'transaction_id' => 'رقم المعاملة',
        'date'           => 'تاريخ الدفع',
        'method'         => 'طريقة الدفع',
        'amount'         => 'المبلغ',

        'failed' => [
            'subtitle'        => 'لم تتم معالجة دفعتك',
            'title'           => 'فشل الدفع',
            'intro'           => 'للأسف، لم نتمكن من معالجة دفعتك. يرجى المحاولة مجدداً.',
            'amount_due'      => 'المبلغ المستحق',
            'error_title'     => 'تفاصيل الخطأ',
            'booking_details' => 'تفاصيل الحجز',
            'common_reasons'  => 'أسباب شائعة لفشل الدفع',
            'reason_1'        => 'رصيد غير كافٍ في حسابك.',
            'reason_2'        => 'تفاصيل البطاقة غير صحيحة.',
            'reason_3'        => 'البطاقة منتهية الصلاحية أو محظورة للمعاملات الإلكترونية.',
            'reason_4'        => 'مشكلة تقنية في بنكك أو بوابة الدفع.',
            'what_to_do'      => 'ماذا تفعل الآن',
            'what_to_do_desc' => 'يرجى التحقق من تفاصيل بطاقتك والمحاولة مجدداً. إذا استمرت المشكلة، تواصل مع بنكك أو فريق الدعم.',
            'time_warning'    => 'يُرجى العلم أن موعد حجزك محفوظ مؤقتاً. أكمل الدفع في أقرب وقت لتأكيد حجزك.',
            'retry_payment'   => 'إعادة محاولة الدفع',
            'contact_support' => 'التواصل مع الدعم',
            'support_note'    => 'إذا استمررت في مواجهة مشاكل، يرجى التواصل مع فريق الدعم للمساعدة.',
        ],

        'success' => [
            'subtitle'         => 'تمت عملية الدفع بنجاح',
            'title'            => 'تم الدفع بنجاح!',
            'intro'            => 'تمت معالجة دفعتك بنجاح. شكراً لك!',
            'amount_paid'      => 'المبلغ المدفوع',
            'payment_details'  => 'تفاصيل الدفع',
            'booking_summary'  => 'ملخص الحجز',
            'confirmed'        => 'تم تأكيد الحجز',
            'confirmed_desc'   => 'تم تأكيد حجزك. ستتلقى بريداً إلكترونياً لتأكيد الحجز قريباً.',
            'receipt_note'     => 'تم إرفاق إيصال لهذه الدفعة بهذا البريد الإلكتروني.',
            'view_booking'     => 'عرض الحجز',
            'download_receipt' => 'تحميل الإيصال',
            'questions'        => 'إذا كان لديك أي استفسار حول هذه الدفعة، يرجى التواصل مع فريق الدعم.',
        ],
    ],

    'owner' => [
        'greeting' => 'مرحباً :name،',

        'verified' => [
            'subject'         => 'تم التحقق من حسابك',
            'subtitle'        => 'اكتمل التحقق من حسابك',
            'title'           => 'تم توثيق الحساب!',
            'intro'           => 'يسعدنا إعلامك بأنه تم التحقق من حسابك كمالك قاعة بنجاح.',
            'badge'           => 'مالك قاعة موثّق',
            'what_you_can_do' => 'ما يمكنك فعله الآن',
            'step_1_title'    => 'أضف قاعتك',
            'step_1_desc'     => 'أدرج قاعتك مع التفاصيل الكاملة والصور والأسعار.',
            'step_2_title'    => 'حدّد مواعيد التوفر',
            'step_2_desc'     => 'أضف التواريخ والأوقات المتاحة لقاعتك.',
            'step_3_title'    => 'استقبل الحجوزات',
            'step_3_desc'     => 'ابدأ باستقبال طلبات الحجز من العملاء.',
            'step_4_title'    => 'أدر وأكسب',
            'step_4_desc'     => 'تابع حجوزاتك وأرباحك من لوحة التحكم.',
            'tips_title'      => 'نصائح للنجاح',
            'tip_1'           => 'أكمل ملف القاعة بصور عالية الجودة لجذب المزيد من العملاء.',
            'tip_2'           => 'حافظ على تحديث تقويم المواعيد المتاحة.',
            'tip_3'           => 'استجب لطلبات الحجز بسرعة لتحسين ترتيبك.',
            'need_help'       => 'هل تحتاج مساعدة؟',
            'support_desc'    => 'فريق الدعم لدينا جاهز لمساعدتك. تواصل معنا على',
            'go_to_dashboard' => 'اذهب إلى لوحة التحكم',
            'welcome_message' => 'مرحباً بك في مجالس — نتطلع إلى النمو معاً!',
        ],

        'rejected' => [
            'subject'      => 'تحديث بشأن طلب التحقق من حسابك',
            'subtitle'     => 'طلب التحقق يحتاج إلى مراجعة',
            'title'        => 'لم يتم قبول التحقق',
            'intro'        => 'يؤسفنا إعلامك بأنه لم يتمكن من الموافقة على التحقق من حسابك كمالك قاعة في الوقت الحالي.',
            'reason_title' => 'السبب',
            'what_next'    => 'ماذا تفعل الآن؟',
            'step_1_title' => 'راجع السبب',
            'step_1_desc'  => 'يرجى قراءة السبب أعلاه بعناية وتحديث معلوماتك أو وثائقك وفقاً لذلك.',
            'step_2_title' => 'تواصل مع الدعم',
            'step_2_desc'  => 'إذا كانت لديك أسئلة أو اعتقدت أن هذا القرار جاء خطأً، تواصل مع فريق الدعم.',
            'need_help'    => 'هل تحتاج مساعدة؟',
            'support_desc' => 'فريق الدعم لدينا جاهز لمساعدتك. تواصل معنا على',
        ],

        // ── New booking notification (to owner) ──
        'new_booking' => [
            'subtitle'            => 'لديك حجز جديد',
            'title'               => 'تم استلام حجز جديد!',
            'intro'               => 'لقد استلمت حجزاً جديداً في :hall.',
            'your_earnings'       => 'أرباحك',
            'booking_details'     => 'تفاصيل الحجز',
            'customer_info'       => 'معلومات العميل',
            'customer_name'       => 'اسم العميل',
            'customer_email'      => 'البريد الإلكتروني',
            'customer_phone'      => 'رقم الهاتف',
            'special_notes'       => 'ملاحظات خاصة',
            'financial_summary'   => 'الملخص المالي',
            'platform_commission' => 'عمولة المنصة',
            'your_payout'         => 'مستحقاتك',
            'action_required'     => 'إجراء مطلوب',
            'action_desc'         => 'تتطلب هذه القاعة الموافقة. يرجى مراجعة هذا الحجز والموافقة عليه أو رفضه خلال 24 ساعة.',
            'view_booking'        => 'عرض الحجز',
            'approve_booking'     => 'موافقة / رفض الحجز',
            'manage_note'         => 'يمكنك إدارة جميع حجوزاتك من لوحة التحكم.',
        ],

        // ── Booking cancelled notification (to owner) ──
        'cancelled' => [
            'subtitle'            => 'تم إلغاء أحد الحجوزات',
            'title'               => 'تم إلغاء الحجز',
            'intro'               => 'تم إلغاء حجز في :hall.',
            'booking_details'     => 'تفاصيل الحجز',
            'customer'            => 'العميل',
            'reason_title'        => 'سبب الإلغاء',
            'financial_impact'    => 'الأثر المالي',
            'original_booking'    => 'قيمة الحجز الأصلية',
            'lost_earnings'       => 'الأرباح الضائعة',
            'slot_available'      => 'الفترة الزمنية متاحة الآن',
            'slot_available_desc' => 'فترة :slot بتاريخ :date أصبحت متاحة لحجوزات جديدة.',
            'view_bookings'       => 'عرض حجوزاتي',
            'support_note'        => 'إذا كان لديك أي استفسار، يرجى التواصل مع فريق الدعم.',
        ],
    ],
];
