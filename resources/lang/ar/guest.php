<?php

declare(strict_types=1);

/**
 * Guest Booking Language File (Arabic)
 *
 * Contains all translation strings for the guest booking feature.
 *
 * @package Lang\Ar
 * @version 1.0.0
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Page Titles
    |--------------------------------------------------------------------------
    */

    'page_title_book' => 'الحجز كزائر',
    'page_title_verify' => 'تأكيد البريد الإلكتروني',
    'page_title_form' => 'إكمال الحجز',
    'page_title_payment' => 'الدفع',
    'page_title_success' => 'تم تأكيد الحجز',
    'page_title_details' => 'تفاصيل الحجز',

    /*
    |--------------------------------------------------------------------------
    | Step Labels
    |--------------------------------------------------------------------------
    */

    'step_1_guest_info' => 'معلوماتك',
    'step_2_verify' => 'تأكيد البريد',
    'step_3_booking' => 'تفاصيل الحجز',
    'step_4_payment' => 'الدفع',

    /*
    |--------------------------------------------------------------------------
    | Form Labels
    |--------------------------------------------------------------------------
    */

    'label_name' => 'الاسم الكامل',
    'label_email' => 'البريد الإلكتروني',
    'label_phone' => 'رقم الهاتف',
    'label_otp' => 'رمز التحقق',
    'label_password' => 'كلمة المرور',
    'label_password_confirm' => 'تأكيد كلمة المرور',

    /*
    |--------------------------------------------------------------------------
    | Placeholders
    |--------------------------------------------------------------------------
    */

    'placeholder_name' => 'أدخل اسمك الكامل',
    'placeholder_email' => 'أدخل بريدك الإلكتروني',
    'placeholder_phone' => 'مثال: 9xxxxxxx',
    'placeholder_otp' => 'أدخل الرمز المكون من 6 أرقام',

    /*
    |--------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------
    */

    'btn_continue' => 'متابعة',
    'btn_verify' => 'تأكيد',
    'btn_resend_otp' => 'إعادة إرسال الرمز',
    'btn_submit_booking' => 'المتابعة للدفع',
    'btn_pay_now' => 'ادفع الآن',
    'btn_create_account' => 'إنشاء حساب',
    'btn_skip_account' => 'لا شكراً',
    'btn_view_booking' => 'عرض الحجز',
    'btn_download_pdf' => 'تحميل PDF',
    'btn_login_instead' => 'تسجيل الدخول',
    'btn_continue_as_guest' => 'المتابعة كزائر',

    /*
    |--------------------------------------------------------------------------
    | Success Messages
    |--------------------------------------------------------------------------
    */

    'otp_sent' => 'تم إرسال رمز التحقق إلى :email',
    'otp_resent' => 'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني.',
    'otp_verified' => 'تم التحقق من البريد الإلكتروني بنجاح! يرجى إكمال الحجز.',
    'booking_created' => 'تم إنشاء حجزك. يرجى إكمال الدفع.',
    'payment_successful' => 'تم الدفع بنجاح! تم تأكيد حجزك.',
    'account_created' => 'تم إنشاء الحساب بنجاح! تم ربط :count حجز/حجوزات بحسابك.',

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    */

    'session_expired' => 'انتهت صلاحية جلستك. يرجى البدء من جديد.',
    'session_invalid' => 'جلسة غير صالحة. يرجى بدء عملية الحجز مرة أخرى.',
    'verification_required' => 'يرجى التحقق من بريدك الإلكتروني قبل المتابعة.',
    'otp_required' => 'يرجى إدخال رمز التحقق.',
    'otp_invalid_length' => 'رمز التحقق يجب أن يكون 6 أرقام.',
    'otp_digits_only' => 'رمز التحقق يجب أن يحتوي على أرقام فقط.',
    'otp_incorrect' => 'رمز التحقق غير صحيح. المحاولات المتبقية: :remaining',
    'otp_expired' => 'انتهت صلاحية رمز التحقق. يرجى طلب رمز جديد.',
    'otp_locked' => 'تجاوزت عدد المحاولات المسموحة. يرجى طلب رمز تحقق جديد.',
    'otp_resend_wait' => 'يرجى الانتظار :seconds ثانية قبل طلب رمز جديد.',
    'otp_resend_failed' => 'فشل إرسال رمز التحقق. يرجى المحاولة مرة أخرى.',
    'initiation_failed' => 'فشل بدء الحجز. يرجى المحاولة مرة أخرى.',
    'booking_not_found' => 'الحجز غير موجود أو انتهت صلاحية رابط الوصول.',
    'max_pending_bookings' => 'لقد وصلت إلى الحد الأقصى وهو :count حجوزات معلقة. يرجى إكمال أو إلغاء الحجوزات الحالية أولاً.',
    'too_many_sessions' => 'لديك جلسات حجز معلقة كثيرة. يرجى الإكمال أو الانتظار حتى انتهاء الجلسات الحالية.',
    'account_already_exists' => 'يوجد حساب بهذا البريد الإلكتروني بالفعل. يرجى تسجيل الدخول.',
    'account_creation_failed' => 'فشل إنشاء الحساب. يرجى المحاولة مرة أخرى.',

    /*
    |--------------------------------------------------------------------------
    | Info Messages
    |--------------------------------------------------------------------------
    */

    'email_registered_prompt' => 'هذا البريد الإلكتروني مسجل بالفعل. هل تريد تسجيل الدخول أو المتابعة كزائر؟',
    'otp_info' => 'أرسلنا رمز تحقق مكون من 6 أرقام إلى بريدك الإلكتروني. يرجى إدخاله أدناه.',
    'otp_expires_info' => 'هذا الرمز صالح لمدة 10 دقائق.',
    'booking_access_info' => 'احفظ هذا الرابط للوصول إلى تفاصيل حجزك في أي وقت.',
    'create_account_prompt' => 'هل تريد إنشاء حساب؟ سيمكنك ذلك من إدارة جميع حجوزاتك بسهولة.',
    'guest_booking_note' => 'أنت تحجز كزائر. يمكنك إنشاء حساب بعد إكمال الحجز.',

    /*
    |--------------------------------------------------------------------------
    | Email: OTP
    |--------------------------------------------------------------------------
    */

    'otp_email_subject' => 'رمز التحقق من الحجز - :app',
    'otp_email_greeting' => 'مرحباً :name،',
    'otp_email_intro' => 'أنت تحجز :hall. يرجى استخدام الرمز التالي للتحقق من بريدك الإلكتروني:',
    'otp_email_code_label' => 'رمز التحقق الخاص بك هو:',
    'otp_email_expires' => 'هذا الرمز صالح لمدة :minutes دقائق.',
    'otp_email_warning' => 'إذا لم تطلب هذا الرمز، يرجى تجاهل هذا البريد.',
    'otp_email_salutation' => 'مع أطيب التحيات،|فريق :app',

    /*
    |--------------------------------------------------------------------------
    | Email: Booking Confirmation
    |--------------------------------------------------------------------------
    */

    'confirmation_email_subject' => 'تأكيد الحجز - :booking_number',
    'confirmation_email_greeting' => 'مرحباً :name،',
    'confirmation_email_intro' => 'تم تأكيد حجزك! إليك التفاصيل:',
    'view_booking_details' => 'عرض تفاصيل الحجز',
    'confirmation_email_access_info' => 'يمكنك استخدام الرابط أعلاه لعرض تفاصيل حجزك في أي وقت.',
    'confirmation_email_create_account_hint' => 'نصيحة: أنشئ حسابًا لإدارة جميع حجوزاتك بسهولة في مكان واحد!',
    'confirmation_email_salutation' => 'شكراً لاختيارك :app!',

    /*
    |--------------------------------------------------------------------------
    | Booking Choice Modal
    |--------------------------------------------------------------------------
    */

    'modal_title' => 'كيف تريد الحجز؟',
    'modal_login_option' => 'تسجيل الدخول',
    'modal_login_description' => 'الوصول إلى حسابك لإدارة الحجوزات بسهولة',
    'modal_register_option' => 'إنشاء حساب',
    'modal_register_description' => 'سجل للحصول على إدارة أسهل للحجوزات',
    'modal_guest_option' => 'المتابعة كزائر',
    'modal_guest_description' => 'احجز بدون إنشاء حساب',

    /*
    |--------------------------------------------------------------------------
    | Account Creation Section
    |--------------------------------------------------------------------------
    */

    'create_account_title' => 'إنشاء حسابك',
    'create_account_description' => 'أنشئ حساباً لإدارة حجوزاتك بسهولة، وتتبع المدفوعات، والحصول على عروض حصرية.',
    'create_account_benefits' => [
        'عرض جميع حجوزاتك في مكان واحد',
        'دفع أسرع للحجوزات المستقبلية',
        'الحصول على عروض وخصومات خاصة',
        'ترك تقييمات للقاعات التي زرتها',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Admin Labels
    |--------------------------------------------------------------------------
    */

    'badge_guest' => 'زائر',
    'badge_guest_converted' => 'زائر (تم التحويل)',
    'badge_registered' => 'مسجل',
    'filter_booking_type' => 'نوع الحجز',
    'filter_guest_only' => 'حجوزات الزوار فقط',
    'filter_registered_only' => 'المستخدمين المسجلين فقط',



    'page_title_details' => 'تفاصيل الحجز',
    'badge_guest' => 'حجز ضيف',
    'btn_download_pdf' => 'تحميل PDF',
    'btn_create_account' => 'إنشاء حساب',

    'details_heading' => 'تفاصيل الحجز #:booking_number',
    'details_subheading' => 'عرض وإدارة معلومات حجزك أدناه.',
    'details_section_booking_info' => 'معلومات الحجز',
    'details_section_hall_info' => 'معلومات القاعة',
    'details_section_price_summary' => 'ملخص السعر',
    'details_section_your_info' => 'معلوماتك',
    'details_section_services' => 'خدمات إضافية',
    'details_section_payment_info' => 'معلومات الدفع',

    'details_label_booking_number' => 'رقم الحجز',
    'details_label_booking_status' => 'حالة الحجز',
    'details_label_payment' => 'الدفع',
    'details_label_hall' => 'القاعة',
    'details_label_date' => 'التاريخ',
    'details_label_time' => 'الوقت',
    'details_label_guests' => 'عدد الضيوف',
    'details_label_event_type' => 'نوع الفعالية',
    'details_label_services' => 'الخدمات',
    'details_label_total_amount' => 'المبلغ الإجمالي',
    'details_label_payment_status' => 'حالة الدفع',
    'details_label_guest_name' => 'اسم الضيف',
    'details_label_guest_email' => 'البريد الإلكتروني',
    'details_label_guest_phone' => 'رقم الهاتف',
    'details_label_phone' => 'الهاتف',
    'details_label_special_requests' => 'طلبات خاصة',

    'details_payment_pending_message' => 'حجزك ينتظر الدفع. يرجى إكمال الدفع للتأكيد.',
    'details_no_services' => 'لم يتم اختيار خدمات إضافية.',
    'details_need_help' => 'تحتاج مساعدة؟',
    'details_help_message' => 'إذا كان لديك أي أسئلة حول حجزك، يرجى الاتصال بنا.',
    'details_thank_you' => 'شكراً لحجزك! نحن نتطلع لاستضافتك.',
    'details_contact_info' => 'إذا كان لديك أي أسئلة، يرجى الاتصال بنا على :support_email.',

    'price_hall_rental' => 'إيجار القاعة',
    'price_services' => 'الخدمات',
    'price_platform_fee' => 'رسوم المنصة',
    'price_total' => 'الإجمالي',
    'price_advance_paid' => 'دفعة مقدمة',
    'price_balance_due' => 'الرصيد المستحق',

    'currency_omr' => 'ريال عماني',

    'btn_complete_payment' => 'إكمال الدفع',
    'btn_browse_more_halls' => 'تصفح المزيد من القاعات',

    'status_pending' => 'قيد الانتظار',
    'status_confirmed' => 'تم التأكيد',
    'status_cancelled' => 'ملغى',
    'status_completed' => 'مكتمل',

    'payment_status_pending' => 'قيد الانتظار',
    'payment_status_paid' => 'مدفوع',
    'payment_status_partial' => 'جزئي',
    'payment_status_refunded' => 'تم الاسترداد',

    'time_slot_morning' => 'صباحاً (8:00 صباحاً - 12:00 ظهراً)',
    'time_slot_afternoon' => 'بعد الظهر (1:00 ظهراً - 5:00 مساءً)',
    'time_slot_evening' => 'مساءً (6:00 مساءً - 10:00 مساءً)',
    'time_slot_full_day' => 'طوال اليوم (8:00 صباحاً - 10:00 مساءً)',

    'event_type_wedding' => 'زفاف',
    'event_type_birthday' => 'عيد ميلاد',
    'event_type_corporate' => 'فعالية شركات',
    'event_type_graduation' => 'تخرج',
    'event_type_other' => 'أخرى',

    'date_format' => 'l، j F، Y',

    // Success page translations
    'success_title' => 'تم الدفع بنجاح!',
    'success_subtitle' => 'تم تأكيد حجزك بنجاح!',
    'success_booking_details' => 'تفاصيل الحجز',
    'success_label_location' => 'الموقع',
    'success_label_additional_services' => 'خدمات إضافية',
    'success_save_link_title' => 'احفظ رابط حجزك',

    // Account creation translations
    'create_account_title' => 'إنشاء حساب',
    'create_account_description' => 'أنشئ حساب مجاني لإدارة حجوزاتك بسهولة',
    'create_account_benefits' => [
        'الوصول إلى جميع حجوزاتك في مكان واحد',
        'استلام تحديثات وتذكيرات الحجز',
        'دفع أسرع للحجوزات المستقبلية',
        'حفظ تفضيلاتك وتفاصيلك',
    ],

    'label_password' => 'كلمة المرور',
    'label_password_confirm' => 'تأكيد كلمة المرور',

    'btn_view_booking' => 'عرض الحجز',
    'btn_processing' => 'جاري المعالجة...',
    'btn_cancel' => 'إلغاء',
    'btn_skip_account' => 'تخطي والاستمرار في تصفح القاعات',
    'btn_login_instead' => 'تسجيل الدخول بدلاً من ذلك',
    'btn_back_to_halls' => 'العودة إلى القاعات',
    'btn_back_to_previous' => 'العودة إلى الصفحة السابقة',
    'receive_the_code' => 'لم تستلم الرمز؟',
    'back' => 'عودة',
    'majalis' => 'مجالس',
    'rights_reserved' => 'جميع الحقوق محفوظة.',

    'account_already_exists' => 'يوجد حساب مسجل بالبريد الإلكتروني الخاص بك. يرجى تسجيل الدخول للوصول إلى حجوزاتك.',


];
