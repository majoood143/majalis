<?php

return [
    'title' => 'Booking: :number',
    'subheading' => ':hall • :date • :time_slot',

    'actions' => [
        'approve' => [
            'label' => 'Approve Booking',
            'modal_heading' => 'Approve Booking',
            'modal_description' => 'Are you sure you want to approve this booking? The customer will receive a confirmation notification.',
            'submit_label' => 'Yes, Approve',
        ],

        'reject' => [
            'label' => 'Reject Booking',
            'modal_heading' => 'Reject Booking',
            'modal_description' => 'Please provide a reason for rejecting this booking. The customer will be notified.',
            'reason_label' => 'Reason for Rejection',
            'reason_placeholder' => 'e.g., Hall unavailable due to maintenance, double booking error, etc.',
            'reason_helper' => 'This reason will be shared with the customer.',
            'reason_prefix' => 'Rejected by hall owner: ',
        ],

        'record_balance' => [
            'label' => 'Record Balance Payment',
            'modal_heading' => 'Record Balance Payment',
            'modal_description' => 'Record that the remaining balance has been received from the customer.',
            'section_title' => 'Payment Details',
            'balance_summary_label' => 'Balance Summary',
            'balance_summary_content' => 'Total: OMR :total | Advance Paid: OMR :advance | Balance Due: OMR :balance',
            'amount_received_label' => 'Amount Received',
            'amount_received_helper' => 'Enter the actual amount received from customer',
            'payment_method_label' => 'Payment Method',
            'payment_methods' => [
                'card' => 'Card (POS Machine)',
                'cheque' => 'Cheque',
            ],
            'reference_label' => 'Receipt/Reference Number',
            'reference_placeholder' => 'e.g., Receipt #12345 or Transfer Ref',
            'received_at_label' => 'Received Date & Time',
            'notes_label' => 'Additional Notes',
            'notes_placeholder' => 'Any relevant notes about this payment...',
            'notes_format' => "Balance Payment Recorded:\n- Amount: OMR %s\n- Method: %s\n- Reference: %s\n- Received: %s",
            'notes_additional' => 'Notes:',
        ],

        'contact' => [
            'group_label' => 'Contact Customer',
            'call' => 'Call Customer',
            'email' => 'Send Email',
            'email_subject' => 'Regarding Booking :number',
            'whatsapp' => 'WhatsApp Message',
            'whatsapp_message' => 'Hello! Regarding your booking :number at :hall on :date.',
        ],

        'download_invoice' => 'Download Invoice',
        'back' => 'Back to Bookings',
    ],

    'notifications' => [
        'approved' => [
            'title' => 'Booking Approved',
            'body' => 'The booking has been approved and the customer has been notified.',
        ],
        'rejected' => [
            'title' => 'Booking Rejected',
            'body' => 'The booking has been rejected and the customer has been notified.',
        ],
        'balance_recorded' => [
            'title' => 'Balance Payment Recorded',
            'body' => 'The balance payment of OMR :amount has been recorded successfully.',
        ],
    ],
];
