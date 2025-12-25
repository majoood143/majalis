<?php

/**
 * Arabic translations for Advance Payment feature
 *
 * Add these to your existing resources/lang/ar/halls.php file
 */

return [
    // Advance Payment - Hall Settings
    'advance_payment' => 'الدفعة المقدمة',
    'advance_payment_settings' => 'إعدادات الدفعة المقدمة',
    'allows_advance_payment' => 'تفعيل الدفعة المقدمة',
    'allows_advance_payment_help' => 'مطالبة العملاء بدفع مبلغ مقدم عند الحجز',
    'advance_payment_type' => 'نوع الدفعة',
    'advance_payment_type_help' => 'كيفية حساب المبلغ المقدم',
    'advance_type_fixed' => 'مبلغ ثابت',
    'advance_type_percentage' => 'نسبة مئوية من الإجمالي',
    'advance_payment_amount' => 'المبلغ المقدم',
    'advance_payment_amount_help' => 'المبلغ الثابت الواجب دفعه مقدماً (ريال عماني)',
    'advance_payment_amount_placeholder' => 'مثال: 500.000',
    'advance_payment_percentage' => 'نسبة الدفعة المقدمة',
    'advance_payment_percentage_help' => 'النسبة المئوية من إجمالي الحجز الواجب دفعها مقدماً',
    'advance_payment_percentage_placeholder' => 'مثال: 20',
    'minimum_advance_payment' => 'الحد الأدنى للدفعة المقدمة',
    'minimum_advance_payment_help' => 'الحد الأدنى للمبلغ المقدم المطلوب (اختياري)',
    'minimum_advance_payment_placeholder' => 'مثال: 100.000',

    // Advance Payment - Preview & Display
    'advance_payment_preview' => 'معاينة',
    'advance_payment_preview_help' => 'مثال على الحساب بناءً على إعداداتك',
    'preview_for_price' => 'إذا كان إجمالي الحجز = :price ريال عماني:',
    'customer_pays_advance' => 'يدفع العميل مقدماً',
    'balance_due_before_event' => 'المبلغ المتبقي المستحق قبل الفعالية',
    'advance_required' => 'دفعة مقدمة مطلوبة',
    'advance_payment_required' => 'هذه القاعة تتطلب دفعة مقدمة',
    'advance_payment_info' => 'يجب عليك دفع :amount ريال عماني كدفعة مقدمة. يجب دفع المبلغ المتبقي :balance ريال عماني قبل الفعالية.',

    // Booking - Payment Type
    'payment_type' => 'نوع الدفع',
    'payment_type_full' => 'دفع كامل',
    'payment_type_advance' => 'دفعة مقدمة',
    'full_payment' => 'دفع كامل',
    'advance_payment_only' => 'دفعة مقدمة فقط',
    'pay_full_amount' => 'دفع المبلغ الكامل',
    'pay_advance_only' => 'دفع الدفعة المقدمة فقط',

    // Booking - Advance Payment Details
    'advance_paid' => 'الدفعة المقدمة المدفوعة',
    'balance_due' => 'المبلغ المتبقي',
    'balance_pending' => 'في انتظار دفع المتبقي',
    'balance_paid' => 'تم دفع المتبقي',
    'balance_payment_status' => 'حالة دفع المبلغ المتبقي',
    'balance_not_paid' => 'لم يتم الدفع بعد',
    'balance_paid_on' => 'تم الدفع في :date',
    'balance_payment_method' => 'طريقة دفع المبلغ المتبقي',
    'balance_payment_reference' => 'رقم المرجع',
    'mark_balance_as_paid' => 'تعيين المبلغ المتبقي كمدفوع',
    'balance_payment_details' => 'تفاصيل دفع المبلغ المتبقي',

    // Payment Methods
    'bank_transfer' => 'تحويل بنكي',
    'cash' => 'نقداً',
    'card' => 'بطاقة',
    'online_payment' => 'دفع إلكتروني',

    // Messages & Notifications
    'advance_payment_calculated' => 'تم حساب الدفعة المقدمة بناءً على إعدادات القاعة',
    'balance_marked_as_paid' => 'تم تعيين المبلغ المتبقي كمدفوع بنجاح',
    'balance_payment_recorded' => 'تم تسجيل دفع المبلغ المتبقي بنجاح',
    'invalid_advance_settings' => 'إعدادات الدفعة المقدمة غير صحيحة لهذه القاعة',
    'advance_amount_exceeds_total' => 'لا يمكن أن يتجاوز المبلغ المقدم إجمالي مبلغ الحجز',

    // Validation
    'advance_amount_required' => 'المبلغ المقدم مطلوب عند استخدام النوع الثابت',
    'advance_percentage_required' => 'نسبة الدفعة المقدمة مطلوبة عند استخدام نوع النسبة المئوية',
    'advance_percentage_max' => 'لا يمكن أن تتجاوز نسبة الدفعة المقدمة 100%',
    'minimum_advance_min' => 'لا يمكن أن يكون الحد الأدنى للدفعة المقدمة سالباً',

    // Help Text
    'advance_payment_explanation' => 'تتيح لك الدفعة المقدمة تأمين الحجوزات بدفع جزئي مقدماً. يجب دفع المبلغ المتبقي قبل تاريخ الفعالية.',
    'advance_includes_services' => 'ملاحظة: يشمل المبلغ المقدم سعر القاعة والخدمات، حيث يجب حجز الخدمات من الموردين.',
    'balance_payment_explanation' => 'بعد الدفعة المقدمة، يجب على العملاء دفع المبلغ المتبقي عبر التحويل البنكي أو نقداً قبل الفعالية. يمكنك تعيينه كمدفوع يدوياً هنا.',
    'balance_payment_required' => 'ملاحظة: لا يمكن إكمال الحجز بدون دفع المبلغ المتبقي قبل الفعالية.',
    'advance_paid_success'=>'تم دفع الدفعة المقدمة بنجاح.',
    'advance_amount' => 'مبلغ الدفعة المقدمة',
];
