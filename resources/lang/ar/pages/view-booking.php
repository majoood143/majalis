<?php

return [
    'title' => 'الحجز: :number',
    'subheading' => ':hall • :date • :time_slot',

    'actions' => [
        'approve' => [
            'label' => 'الموافقة على الحجز',
            'modal_heading' => 'الموافقة على الحجز',
            'modal_description' => 'هل أنت متأكد من رغبتك في الموافقة على هذا الحجز؟ سيتم إرسال إشعار تأكيد للعميل.',
            'submit_label' => 'نعم، أوافق',
        ],

        'reject' => [
            'label' => 'رفض الحجز',
            'modal_heading' => 'رفض الحجز',
            'modal_description' => 'يرجى تقديم سبب لرفض هذا الحجز. سيتم إشعار العميل.',
            'reason_label' => 'سبب الرفض',
            'reason_placeholder' => 'مثال: القاعة غير متاحة بسبب الصيانة، خطأ في الحجز المزدوج، إلخ.',
            'reason_helper' => 'سيتم مشاركة هذا السبب مع العميل.',
            'reason_prefix' => 'مرفوض من مالك القاعة: ',
        ],

        'record_balance' => [
            'label' => 'تسجيل دفع الرصيد',
            'modal_heading' => 'تسجيل دفع الرصيد',
            'modal_description' => 'تسجيل استلام الرصيد المتبقي من العميل.',
            'section_title' => 'تفاصيل الدفع',
            'balance_summary_label' => 'ملخص الرصيد',
            'balance_summary_content' => 'الإجمالي: :total ريال | المدفوع مقدمًا: :advance ريال | الرصيد المتبقي: :balance ريال',
            'amount_received_label' => 'المبلغ المستلم',
            'amount_received_helper' => 'أدخل المبلغ الفعلي المستلم من العميل',
            'payment_method_label' => 'طريقة الدفع',
            'payment_methods' => [
                'card' => 'بطاقة (جهاز نقاط البيع)',
                'cheque' => 'شيك',
            ],
            'reference_label' => 'رقم الإيصال/المرجع',
            'reference_placeholder' => 'مثال: إيصال #12345 أو رقم التحويل',
            'received_at_label' => 'تاريخ ووقت الاستلام',
            'notes_label' => 'ملاحظات إضافية',
            'notes_placeholder' => 'أي ملاحظات ذات صلة حول هذه الدفعة...',
            'notes_format' => "تم تسجيل دفع الرصيد:\n- المبلغ: %s ريال\n- الطريقة: %s\n- المرجع: %s\n- تاريخ الاستلام: %s",
            'notes_additional' => 'ملاحظات:',
        ],

        'contact' => [
            'group_label' => 'الاتصال بالعميل',
            'call' => 'الاتصال بالعميل',
            'email' => 'إرسال بريد إلكتروني',
            'email_subject' => 'بخصوص الحجز :number',
            'whatsapp' => 'رسالة واتساب',
            'whatsapp_message' => 'مرحباً! بخصوص حجزك :number في :hall بتاريخ :date.',
        ],

        'download_invoice' => 'تحميل الفاتورة',
        'back' => 'العودة إلى الحجوزات',
    ],

    'notifications' => [
        'approved' => [
            'title' => 'تمت الموافقة على الحجز',
            'body' => 'تمت الموافقة على الحجز وتم إشعار العميل.',
        ],
        'rejected' => [
            'title' => 'تم رفض الحجز',
            'body' => 'تم رفض الحجز وتم إشعار العميل.',
        ],
        'balance_recorded' => [
            'title' => 'تم تسجيل دفع الرصيد',
            'body' => 'تم تسجيل دفع الرصيد بقيمة :amount ريال بنجاح.',
        ],
    ],
];
