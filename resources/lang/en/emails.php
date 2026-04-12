<?php

return [
    'ticket' => [
        // Subject lines
        'submitted_admin_subject'    => 'New Support Ticket Submitted - #:ticket_number',
        'submitted_customer_subject' => 'Your Ticket Has Been Received - #:ticket_number',

        // Admin email
        'submitted_admin_subtitle'   => 'A new support ticket has been submitted',
        'submitted_admin_title'      => 'New Support Ticket',
        'new_ticket_submitted'       => 'A customer has submitted a new support request',
        'admin_footer_note'          => 'Please review and respond to this ticket as soon as possible.',

        // Customer email
        'submitted_customer_subtitle'    => 'Thank you for submitting your request',
        'submitted_customer_title'       => 'Ticket Received',
        'submitted_customer_intro'       => 'We have received your support request and our team will review it shortly.',
        'submitted_customer_message'     => 'You can track the status of your request anytime by logging into your account. We aim to respond to all requests within 24 hours.',
        'submitted_customer_footer'      => 'If you did not submit this request, please ignore this email.',
        'greeting'                       => 'Hello :name,',

        // Common fields
        'reference_label'      => 'Reference Number',
        'customer_information' => 'Customer Information',
        'ticket_details'       => 'Ticket Details',
        'description'          => 'Description',
        'name'                 => 'Name',
        'email'                => 'Email Address',
        'submission_type'      => 'Submission Type',
        'guest_submission'     => 'Guest Submission',
        'type'                 => 'Type',
        'priority'             => 'Priority',
        'subject'              => 'Subject',
        'status'               => 'Status',

        // Buttons
        'view_ticket_btn'         => 'View Your Ticket',
        'view_and_respond_btn'    => 'View & Respond',
    ],

    'booking' => [
        // Already present
        'regards' => 'Warm Regards,',
        'team'    => 'The :app Team',

        // Common fields shared across booking emails
        'greeting'        => 'Hello :name,',
        'booking_number'  => 'Booking Number',
        'hall'            => 'Hall',
        'date'            => 'Date',
        'time_slot'       => 'Time Slot',
        'guests'          => 'Guests',
        'persons'         => 'persons',
        'event_type'      => 'Event Type',
        'location'        => 'Location',
        'view_map'        => 'View on Map',
        'your_booking'    => 'Your Booking',
        'details_title'   => 'Booking Details',
        'original_amount' => 'Original Amount',
        'services_title'  => 'Additional Services',
        'payment_summary' => 'Payment Summary',
        'hall_price'      => 'Hall Price',
        'services_price'  => 'Extra Services',
        'discount'        => 'Discount',
        'total_amount'    => 'Total Amount',
        'view_details'    => 'View Booking Details',
        'download_confirmation' => 'Download Confirmation',

        // Payment status blocks
        'payment_complete'     => 'Payment Complete',
        'amount_paid'          => 'You have paid :amount OMR for this booking.',
        'payment_pending'      => 'Payment Pending',
        'payment_pending_desc' => 'Please complete your payment of :amount OMR to confirm your booking.',

        // Advance/partial payment
        'advance_payment_received' => 'Advance Payment Received',
        'advance_paid_desc'        => 'Advance paid: :advance OMR — Balance due: :balance OMR',

        // Countdown (used with trans_choice)
        'days_until' => '{1} day until your booking|[2,*] days until your booking',

        // Important notes
        'important_notes'       => 'Important Notes',
        'note_arrive_early'     => 'Please arrive 15 minutes before your scheduled time.',
        'note_bring_id'         => 'Please bring a valid ID for verification.',
        'note_contact_changes'  => 'For any changes or cancellations, please contact us at least 24 hours in advance.',

        // ── Confirmed ──
        'confirmed' => [
            'subtitle'  => 'Your booking has been confirmed',
            'title'     => 'Booking Confirmed!',
            'intro'     => 'Your booking is confirmed and everything is set for your event. Here are your booking details.',
            'questions' => 'If you have any questions, please contact our support team.',
        ],

        // ── Cancelled (customer) ──
        'cancelled' => [
            'subtitle'          => 'Your booking has been cancelled',
            'title'             => 'Booking Cancelled',
            'intro'             => 'We regret to inform you that your booking has been cancelled.',
            'details_title'     => 'Cancelled Booking Details',
            'reason_title'      => 'Cancellation Reason',
            'refund_title'      => 'Refund Information',
            'refund_processing' => 'Your refund will be processed within 5–7 business days.',
            'no_refund_title'   => 'Refund Information',
            'no_refund_desc'    => 'Based on the cancellation policy, this booking is not eligible for a refund.',
            'what_next'         => 'What\'s Next?',
            'what_next_desc'    => 'We\'d love to help you find another hall. Browse our available halls and book again.',
            'browse_halls'      => 'Browse Halls',
            'questions'         => 'If you have any questions about this cancellation, please contact us.',
        ],

        // ── Completed ──
        'completed' => [
            'subtitle'        => 'Your event has been completed',
            'title'           => 'Thank You for Using Majalis!',
            'intro'           => 'We hope you had a wonderful experience at :hall. Thank you for choosing us!',
            'event_summary'   => 'Event Summary',
            'review_title'    => 'Share Your Experience',
            'review_desc'     => 'Your feedback helps us improve and helps other customers make better decisions.',
            'leave_review'    => 'Leave a Review',
            'why_review'      => 'Why Your Review Matters',
            'why_review_1'    => 'Help other customers choose the right hall for their event.',
            'why_review_2'    => 'Enable hall owners to improve their services.',
            'why_review_3'    => 'Help us maintain the quality of our platform.',
            'book_again_title' => 'Ready to Book Again?',
            'book_again_desc'  => 'Had a great experience? The same hall is available for future bookings.',
            'book_again_btn'   => 'Book Again',
            'thank_you'        => 'Thank you for being a valued customer. We look forward to serving you again.',
        ],

        // ── Created ──
        'created' => [
            'subtitle'         => 'Your booking request has been received',
            'intro'            => 'Thank you! We have received your booking request and it is currently being processed.',
            'next_steps_title' => 'Next Steps',
            'next_steps_desc'  => 'Your booking has been created. You will receive a confirmation once it is approved.',
            'awaiting_approval' => 'Your booking is awaiting approval from the hall owner. You will be notified once it is approved.',
            'questions'        => 'If you have any questions, please contact us at',
        ],
    ],

    // ── Payment link email ──
    'payment_link' => [
        'title'            => 'Complete Your Booking Payment',
        'intro'            => 'Your booking :number is reserved. Please complete your payment to confirm it.',
        'advance_note'     => 'Advance payment — remaining balance: :balance OMR',
        'pay_now'          => 'Pay Now',
        'link_expiry_note' => 'This payment link will expire. If you have trouble clicking the button, copy and paste the URL into your browser.',
        'footer_note'      => 'If you did not make this booking or have any questions, please contact us.',
    ],

    // ── Status badges ──
    'status' => [
        'confirmed' => 'Confirmed',
        'cancelled'  => 'Cancelled',
        'completed'  => 'Completed',
        'pending'    => 'Pending',
        'paid'       => 'Paid',
        'failed'     => 'Failed',
    ],

    // ── Payment emails ──
    'payment' => [
        'status'         => 'Payment Status',
        'transaction_id' => 'Transaction ID',
        'date'           => 'Payment Date',
        'method'         => 'Payment Method',
        'amount'         => 'Amount',

        'failed' => [
            'subtitle'        => 'Your payment was not processed',
            'title'           => 'Payment Failed',
            'intro'           => 'Unfortunately, we were unable to process your payment. Please try again.',
            'amount_due'      => 'Amount Due',
            'error_title'     => 'Error Details',
            'booking_details' => 'Booking Details',
            'common_reasons'  => 'Common Reasons for Payment Failure',
            'reason_1'        => 'Insufficient funds in your account.',
            'reason_2'        => 'Incorrect card details entered.',
            'reason_3'        => 'Card expired or blocked for online transactions.',
            'reason_4'        => 'Technical issue with your bank or the payment gateway.',
            'what_to_do'      => 'What to Do Next',
            'what_to_do_desc' => 'Please check your card details and try again. If the problem persists, contact your bank or our support team.',
            'time_warning'    => 'Your booking slot is held temporarily. Please complete the payment as soon as possible to secure your booking.',
            'retry_payment'   => 'Retry Payment',
            'contact_support' => 'Contact Support',
            'support_note'    => 'If you continue to experience issues, please contact our support team for assistance.',
        ],

        'success' => [
            'subtitle'         => 'Your payment was successful',
            'title'            => 'Payment Successful!',
            'intro'            => 'Your payment has been processed successfully. Thank you!',
            'amount_paid'      => 'Amount Paid',
            'payment_details'  => 'Payment Details',
            'booking_summary'  => 'Booking Summary',
            'confirmed'        => 'Booking Confirmed',
            'confirmed_desc'   => 'Your booking has been confirmed. You will receive a booking confirmation email shortly.',
            'receipt_note'     => 'A receipt for this payment has been attached to this email.',
            'view_booking'     => 'View Booking',
            'download_receipt' => 'Download Receipt',
            'questions'        => 'If you have any questions about this payment, please contact our support team.',
        ],
    ],

    'owner' => [
        'greeting' => 'Hello :name,',

        'verified' => [
            'subject'         => 'Your Account Has Been Verified',
            'subtitle'        => 'Your account verification is complete',
            'title'           => 'Account Verified!',
            'intro'           => 'We are pleased to inform you that your hall owner account has been successfully verified.',
            'badge'           => 'Verified Hall Owner',
            'what_you_can_do' => 'What You Can Do Now',
            'step_1_title'    => 'Add Your Hall',
            'step_1_desc'     => 'List your hall with full details, photos, and pricing.',
            'step_2_title'    => 'Set Availability',
            'step_2_desc'     => 'Configure your hall\'s available dates and time slots.',
            'step_3_title'    => 'Receive Bookings',
            'step_3_desc'     => 'Start receiving booking requests from customers.',
            'step_4_title'    => 'Manage & Earn',
            'step_4_desc'     => 'Track bookings and manage your earnings from your dashboard.',
            'tips_title'      => 'Tips for Success',
            'tip_1'           => 'Complete your hall profile with high-quality photos to attract more customers.',
            'tip_2'           => 'Keep your availability calendar up to date.',
            'tip_3'           => 'Respond to booking requests promptly to improve your ranking.',
            'need_help'       => 'Need Help?',
            'support_desc'    => 'Our support team is here to assist you. Reach us at',
            'go_to_dashboard' => 'Go to Dashboard',
            'welcome_message' => 'Welcome to Majalis — we look forward to growing together!',
        ],

        'rejected' => [
            'subject'      => 'Update on Your Account Verification',
            'subtitle'     => 'Your verification request needs attention',
            'title'        => 'Verification Unsuccessful',
            'intro'        => 'We regret to inform you that your hall owner account verification could not be approved at this time.',
            'reason_title' => 'Reason',
            'what_next'    => 'What To Do Next',
            'step_1_title' => 'Review the Reason',
            'step_1_desc'  => 'Please read the reason above carefully and update your account information or documents accordingly.',
            'step_2_title' => 'Contact Support',
            'step_2_desc'  => 'If you have questions or believe this decision was made in error, please reach out to our support team.',
            'need_help'    => 'Need Help?',
            'support_desc' => 'Our support team is ready to assist you. Contact us at',
        ],

        // ── New booking notification (to owner) ──
        'new_booking' => [
            'subtitle'            => 'You have received a new booking',
            'title'               => 'New Booking Received!',
            'intro'               => 'You have received a new booking for :hall.',
            'your_earnings'       => 'Your Earnings',
            'booking_details'     => 'Booking Details',
            'customer_info'       => 'Customer Information',
            'customer_name'       => 'Customer Name',
            'customer_email'      => 'Customer Email',
            'customer_phone'      => 'Customer Phone',
            'special_notes'       => 'Special Notes',
            'financial_summary'   => 'Financial Summary',
            'platform_commission' => 'Platform Commission',
            'your_payout'         => 'Your Payout',
            'action_required'     => 'Action Required',
            'action_desc'         => 'This hall requires approval. Please review and approve or reject this booking within 24 hours.',
            'view_booking'        => 'View Booking',
            'approve_booking'     => 'Approve / Reject Booking',
            'manage_note'         => 'You can manage all your bookings from your dashboard.',
        ],

        // ── Booking cancelled notification (to owner) ──
        'cancelled' => [
            'subtitle'            => 'A booking has been cancelled',
            'title'               => 'Booking Cancelled',
            'intro'               => 'A booking for :hall has been cancelled.',
            'booking_details'     => 'Booking Details',
            'customer'            => 'Customer',
            'reason_title'        => 'Cancellation Reason',
            'financial_impact'    => 'Financial Impact',
            'original_booking'    => 'Original Booking Value',
            'lost_earnings'       => 'Lost Earnings',
            'slot_available'      => 'Time Slot Now Available',
            'slot_available_desc' => 'The :slot time slot on :date is now available for new bookings.',
            'view_bookings'       => 'View My Bookings',
            'support_note'        => 'If you have any questions, please contact our support team.',
        ],
    ],
];
