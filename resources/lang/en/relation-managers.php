<?php

return [
    'payments' => [
        'title' => 'Payment History',

        'columns' => [
            'reference' => 'Reference',
            'amount' => 'Amount',
            'method' => 'Method',
            'status' => 'Status',
            'paid_at' => 'Paid At',
            'refund' => 'Refund',
            'transaction_id' => 'Transaction ID',
            'created' => 'Created',
        ],

        'filters' => [
            'status' => 'Status',
            'payment_method' => 'Payment Method',
        ],

        'messages' => [
            'reference_copied' => 'Reference copied',
            'view_payment' => 'Payment: :ref',
        ],

        'empty_state' => [
            'heading' => 'No payments recorded',
            'description' => 'Payment records will appear here once processed.',
        ],
    ],
];
