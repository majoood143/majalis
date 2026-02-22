<?php

return [
    'payments' => [
        'title' => 'سجل المدفوعات',

        'columns' => [
            'reference' => 'المرجع',
            'amount' => 'المبلغ',
            'method' => 'طريقة الدفع',
            'status' => 'الحالة',
            'paid_at' => 'تاريخ الدفع',
            'refund' => 'المبلغ المسترجع',
            'transaction_id' => 'رقم العملية',
            'created' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'status' => 'الحالة',
            'payment_method' => 'طريقة الدفع',
        ],

        'messages' => [
            'reference_copied' => 'تم نسخ المرجع',
            'view_payment' => 'الدفع: :ref',
        ],

        'empty_state' => [
            'heading' => 'لا توجد مدفوعات مسجلة',
            'description' => 'ستظهر سجلات المدفوعات هنا بعد المعالجة.',
        ],
    ],
];
