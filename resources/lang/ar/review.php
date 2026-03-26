<?php

return [
    // Resource Labels
    'singular' => 'تقييم',
    'plural' => 'التقييمات',
    'navigation_label' => 'التقييمات',
    
    // Sections
    'sections' => [
        'review_information' => 'معلومات التقييم',
        'detailed_ratings' => 'التقييمات التفصيلية',
        'moderation' => 'المراجعة',
        'owner_response' => 'رد المالك',
    ],

    // Fields
    'fields' => [
        'hall' => 'القاعة',
        'booking' => 'الحجز',
        'user' => 'المستخدم',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'cleanliness_rating' => 'تقييم النظافة',
        'service_rating' => 'تقييم الخدمة',
        'value_rating' => 'تقييم القيمة',
        'location_rating' => 'تقييم الموقع',
        'is_approved' => 'مقبول',
        'is_featured' => 'مميز',
        'admin_notes' => 'ملاحظات الإدارة',
        'owner_response' => 'رد المالك',
        'owner_response_at' => 'تاريخ رد المالك',
        'rejection_reason' => 'سبب الرفض',
    ],

    // Ratings
    'ratings' => [
        '1_star' => '⭐ نجمة واحدة',
        '2_stars' => '⭐⭐ نجمتان',
        '3_stars' => '⭐⭐⭐ ثلاث نجوم',
        '4_stars' => '⭐⭐⭐⭐ أربع نجوم',
        '5_stars' => '⭐⭐⭐⭐⭐ خمس نجوم',
    ],

    // Status
    'status' => [
        'approved' => 'مقبول',
        'pending' => 'قيد الانتظار',
        'featured' => 'مميز',
        'not_featured' => 'غير مميز',
    ],

    // Actions
    'actions' => [
        'export' => 'تصدير التقييمات',
        'export_modal_heading' => 'تصدير التقييمات',
        'export_modal_description' => 'تصدير جميع بيانات التقييمات إلى ملف CSV.',
        'bulk_approve' => 'الموافقة على المعلقة',
        'bulk_approve_modal_heading' => 'الموافقة على التقييمات المعلقة',
        'bulk_approve_modal_description' => 'سيتم الموافقة على جميع التقييمات المعلقة.',
        'approve' => 'قبول',
        'reject' => 'رفض',
        'download' => 'تحميل الملف',
    ],

    // Tabs
    'tabs' => [
        'all' => 'جميع التقييمات',
        'pending' => 'بانتظار الموافقة',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        '5_stars' => '5 نجوم',
        'low_rated' => 'تقييم منخفض (≤2)',
        'with_response' => 'بها رد',
    ],

    // Columns
    'columns' => [
        'hall' => 'القاعة',
        'user' => 'المستخدم',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'is_approved' => 'مقبول',
        'is_featured' => 'مميز',
        'owner_response' => 'رد المالك',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Filters
    'filters' => [
        'hall' => 'القاعة',
        'rating' => 'التقييم',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        'has_owner_response' => 'يحتوي على رد المالك',
    ],

    // Export Headers
    'export' => [
        'id' => 'المعرف',
        'hall' => 'القاعة',
        'user' => 'المستخدم',
        'booking' => 'الحجز',
        'rating' => 'التقييم',
        'comment' => 'التعليق',
        'cleanliness' => 'النظافة',
        'service' => 'الخدمة',
        'value' => 'القيمة',
        'location' => 'الموقع',
        'approved' => 'مقبول',
        'featured' => 'مميز',
        'owner_response' => 'رد المالك',
        'created_at' => 'تاريخ الإنشاء',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'تم التصدير بنجاح',
        'export_success_body' => 'تم تصدير بيانات التقييمات بنجاح.',
        'export_error' => 'فشل التصدير',
        'bulk_approve_success' => 'تمت الموافقة على التقييمات',
        'bulk_approve_success_body' => 'تمت الموافقة على :count تقييم.',
        'update_error' => 'فشلت العملية',
    ],

    // Columns (additional)
    'columns_extra' => [
        'is_late_review'    => 'تقييم متأخر',
        'marketing_consent' => 'الموافقة التسويقية',
    ],

    // Filters (additional)
    'filters_extra' => [
        'late_review'       => 'تقييم متأخر',
        'marketing_consent' => 'الموافقة التسويقية',
    ],

    // Messages shown on review submission pages
    'messages' => [
        'invalid_link'          => 'رابط التقييم غير صالح. يرجى استخدام الرابط الموجود في بريدك الإلكتروني.',
        'booking_not_found'     => 'الحجز غير موجود.',
        'invalid_token'         => 'تم التلاعب برابط التقييم أو أنه غير صالح.',
        'booking_not_completed' => 'يمكن تقديم التقييمات فقط للحجوزات المكتملة.',
        'window_expired'        => 'انتهت فترة التقييم لهذا الحجز (14 يومًا بعد الحدث).',
        'already_submitted'     => 'لقد قدمت تقييمًا لهذا الحجز بالفعل. شكرًا لك!',
        'rating_required'       => 'يرجى اختيار تقييم النجوم قبل الإرسال.',
        'late_review_notice'    => 'أنت تقدم هذا التقييم خلال فترة السماح (8-14 يومًا بعد حدثك). شكرًا لمشاركة ملاحظاتك!',
    ],

    // Review submission page labels
    'page' => [
        'submit_title'            => 'اترك تقييمًا',
        'submit_heading'          => 'كيف كانت تجربتك؟',
        'submit_subheading'       => 'ملاحظاتك تساعد الآخرين على اختيار المكان المثالي.',
        'booking_summary'         => 'بيانات حجزك',
        'event_date'              => 'تاريخ الحدث',
        'time_slot'               => 'الفترة الزمنية',
        'overall_rating'          => 'التقييم الإجمالي',
        'star_label_1'            => '1 – ضعيف',
        'star_label_2'            => '2 – مقبول',
        'star_label_3'            => '3 – جيد',
        'star_label_4'            => '4 – جيد جدًا',
        'star_label_5'            => '5 – ممتاز',
        'comment_placeholder'     => 'أخبرنا عن تجربتك…',
        'comment_hint'            => 'اختياري. شاركنا ما أعجبك أو ما يمكن تحسينه.',
        'comment_required_hint'   => 'يرجى تزويدنا بمزيد من التفاصيل حتى نتمكن من التحسين (10 أحرف على الأقل).',
        'photos_label'            => 'أضف صورًا (اختياري)',
        'photos_choose'           => 'انقر للاختيار',
        'photos_or_drag'          => 'أو اسحب الصور هنا',
        'photos_hint'             => 'حتى 5 صور · JPEG، PNG، WebP · بحد أقصى 4 ميجابايت لكل صورة',
        'marketing_consent_label' => 'أود تلقي العروض الخاصة والعروض الترويجية.',
        'submit_button'           => 'إرسال التقييم',
        'link_invalid_title'      => 'الرابط غير صالح أو منتهي الصلاحية',
        'thankyou_title'          => 'شكرًا لك!',
        'thankyou_heading'        => 'شكرًا على تقييمك!',
        'thankyou_body'           => 'تم استلام ملاحظاتك وستساعد الضيوف القادمين في اتخاذ أفضل قرار.',
        'thankyou_late_badge'     => 'تقييم في فترة السماح',
        'thankyou_cta'            => 'استكشف المزيد من القاعات',
        'powered_by'              => 'مدعوم من :app',
    ],

    // Common
    'yes' => 'نعم',
    'no' => 'لا',
    'n_a' => 'غير متوفر',
    'placeholder' => [
        'no_response' => 'لا يوجد رد',
    ],

    // Customer-facing hall details page
    'customer' => [
        'recent_reviews'   => 'أحدث التقييمات',
        'no_reviews'       => 'لا توجد تقييمات بعد. كن أول من يشارك تجربته!',
        'verified_guest'   => 'ضيف موثق',
        'owner_response'   => 'رد صاحب القاعة',
        'featured_review'  => 'مميز',
        'read_more'        => 'قراءة المزيد',
        'show_less'        => 'عرض أقل',
        'cleanliness'      => 'النظافة',
        'service'          => 'الخدمة',
        'value'            => 'القيمة',
        'location'         => 'الموقع',
        'based_on'         => 'بناءً على :count تقييم',
        'out_of'           => 'من 5',
    ],
];
