<?php

declare(strict_types=1);

/**
 * English Email Translations
 *
 * Contains all translation keys for email notifications including:
 * - Booking emails (created, confirmed, cancelled, completed, reminder)
 * - Payment emails (success, failed)
 * - Owner emails (new booking, cancelled, verified)
 * - Common elements (footer, status labels)
 *
 * @package Lang\En
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Common / Footer
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'tagline' => 'Premium Hall Booking Platform',
        'location' => 'Sultanate of Oman',
        'auto_generated' => 'This is an automated email. Please do not reply directly to this message.',
        'rights' => 'All rights reserved.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Labels
    |--------------------------------------------------------------------------
    */
    'status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Emails - Common
    |--------------------------------------------------------------------------
    */
    'booking' => [
        'greeting' => 'Dear :name,',
        'regards' => 'Best regards,',
        'team' => 'The :app Team',
        
        // Field labels
        'details_title' => 'Booking Details',
        'your_booking' => 'Your Booking',
        'booking_number' => 'Booking Number',
        'hall' => 'Hall',
        'date' => 'Date',
        'time_slot' => 'Time Slot',
        'guests' => 'Number of Guests',
        'persons' => 'persons',
        'event_type' => 'Event Type',
        'location' => 'Location',
        
        // Services
        'services_title' => 'Extra Services',
        
        // Payment
        'payment_summary' => 'Payment Summary',
        'hall_price' => 'Hall Price',
        'services_price' => 'Services',
        'discount' => 'Discount',
        'total_amount' => 'Total Amount',
        'original_amount' => 'Original Amount',
        'payment_complete' => 'Payment Complete',
        'amount_paid' => 'Amount Paid: :amount OMR',
        'payment_pending' => 'Payment Pending',
        'payment_pending_desc' => 'Please complete your payment of :amount OMR to confirm your booking.',
        
        // Notes
        'important_notes' => 'Important Notes',
        'note_arrive_early' => 'Please arrive 15-30 minutes before your scheduled time.',
        'note_bring_id' => 'Bring a valid ID and your booking confirmation.',
        'note_contact_changes' => 'Contact us immediately if you need to make any changes.',
        
        // Actions
        'view_details' => 'View Booking Details',
        'view_map' => 'View on Map',
        'download_confirmation' => 'Download Confirmation',

        /*
        |----------------------------------------------------------------------
        | Booking Created
        |----------------------------------------------------------------------
        */
        'created' => [
            'subtitle' => 'Your booking has been received',
            'intro' => 'Thank you for your booking! We have received your request and here are the details.',
            'next_steps_title' => 'What happens next?',
            'next_steps_desc' => 'Your booking is being processed. You will receive a confirmation email once it is approved.',
            'awaiting_approval' => 'Your booking is awaiting approval from the hall owner.',
            'view_booking' => 'View My Booking',
            'questions' => 'If you have any questions, please contact us at',
        ],

        /*
        |----------------------------------------------------------------------
        | Booking Confirmed
        |----------------------------------------------------------------------
        */
        'confirmed' => [
            'subtitle' => 'Great news! Your booking is confirmed',
            'title' => 'Booking Confirmed!',
            'intro' => 'Your booking has been confirmed. We look forward to hosting your event!',
            'questions' => 'Have questions? Our team is here to help you.',
        ],
        
        'days_until' => '{1} day until your event|[2,*] days until your event',

        /*
        |----------------------------------------------------------------------
        | Booking Cancelled
        |----------------------------------------------------------------------
        */
        'cancelled' => [
            'subtitle' => 'Booking cancellation notice',
            'title' => 'Booking Cancelled',
            'intro' => 'We\'re sorry to inform you that your booking has been cancelled.',
            'details_title' => 'Cancelled Booking Details',
            'reason_title' => 'Cancellation Reason',
            'refund_title' => 'Refund Information',
            'refund_processing' => 'Your refund will be processed within 5-7 business days.',
            'no_refund_title' => 'Refund Status',
            'no_refund_desc' => 'Based on our cancellation policy, this booking is not eligible for a refund.',
            'what_next' => 'What\'s Next?',
            'what_next_desc' => 'We\'d love to help you find another perfect hall for your event. Browse our available halls and book again.',
            'browse_halls' => 'Browse Available Halls',
            'questions' => 'If you have questions about this cancellation, please contact us at',
        ],

        /*
        |----------------------------------------------------------------------
        | Booking Completed
        |----------------------------------------------------------------------
        */
        'completed' => [
            'subtitle' => 'Thank you for choosing us',
            'title' => 'Thank You!',
            'intro' => 'We hope you had a wonderful experience at :hall.',
            'event_summary' => 'Event Summary',
            'review_title' => 'How was your experience?',
            'review_desc' => 'Your feedback helps us improve and helps other customers make informed decisions.',
            'leave_review' => 'Leave a Review',
            'why_review' => 'Why should you leave a review?',
            'why_review_1' => 'Help other customers find the perfect venue',
            'why_review_2' => 'Support local hall owners in improving their services',
            'why_review_3' => 'Share your memorable moments with the community',
            'book_again_title' => 'Planning another event?',
            'book_again_desc' => 'Book the same hall again and enjoy our loyalty benefits.',
            'book_again_btn' => 'Book Again',
            'thank_you' => 'Thank you for being a valued customer!',
        ],

        /*
        |----------------------------------------------------------------------
        | Booking Reminder
        |----------------------------------------------------------------------
        */
        'reminder' => [
            'subtitle' => 'Your event is tomorrow!',
            'title' => 'Reminder: Your Booking is Tomorrow!',
            'intro' => 'This is a friendly reminder that your booking is coming up tomorrow.',
            'tomorrow' => 'Tomorrow',
            'event_details' => 'Event Details',
            'location' => 'Venue Location',
            'checklist' => 'Pre-Event Checklist',
            'check_1' => 'Confirm the venue address and directions',
            'check_2' => 'Prepare your booking confirmation (print or digital)',
            'check_3' => 'Arrange transportation for you and your guests',
            'check_4' => 'Coordinate with any vendors or service providers',
            'contact' => 'Need to contact the venue?',
            'hall_phone' => 'Hall',
            'contact_phone' => 'Contact',
            'look_forward' => 'We look forward to seeing you tomorrow!',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Emails
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'transaction_id' => 'Transaction ID',
        'date' => 'Payment Date',
        'method' => 'Payment Method',
        'amount' => 'Amount',
        'status' => 'Payment Status',

        /*
        |----------------------------------------------------------------------
        | Payment Success
        |----------------------------------------------------------------------
        */
        'success' => [
            'subtitle' => 'Payment received successfully',
            'title' => 'Payment Successful!',
            'intro' => 'Your payment has been processed successfully. Thank you!',
            'amount_paid' => 'Amount Paid',
            'payment_details' => 'Payment Details',
            'booking_summary' => 'Booking Summary',
            'confirmed' => 'Your booking is now confirmed!',
            'confirmed_desc' => 'You will receive a booking confirmation email with all the details.',
            'receipt_note' => 'A detailed receipt is attached to this email for your records.',
            'view_booking' => 'View My Booking',
            'download_receipt' => 'Download Receipt',
            'questions' => 'If you have any questions about this payment, please contact our support team.',
        ],

        /*
        |----------------------------------------------------------------------
        | Payment Failed
        |----------------------------------------------------------------------
        */
        'failed' => [
            'subtitle' => 'Payment could not be processed',
            'title' => 'Payment Failed',
            'intro' => 'Unfortunately, we were unable to process your payment. Please try again.',
            'amount_due' => 'Amount Due',
            'error_title' => 'Error Details',
            'booking_details' => 'Booking Details',
            'common_reasons' => 'Common Reasons for Payment Failure',
            'reason_1' => 'Insufficient funds in your account',
            'reason_2' => 'Card expired or invalid card details',
            'reason_3' => 'Transaction declined by your bank',
            'reason_4' => 'Network or connectivity issues',
            'what_to_do' => 'What should you do?',
            'what_to_do_desc' => 'Please check your payment details and try again. If the problem persists, contact your bank or try a different payment method.',
            'time_warning' => 'Your booking will be held for 24 hours. Please complete payment to secure your reservation.',
            'retry_payment' => 'Try Payment Again',
            'contact_support' => 'Contact Support',
            'support_note' => 'If you continue to experience issues, our support team is here to help.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Owner Emails
    |--------------------------------------------------------------------------
    */
    'owner' => [
        'greeting' => 'Dear :name,',

        /*
        |----------------------------------------------------------------------
        | New Booking (for Owner)
        |----------------------------------------------------------------------
        */
        'new_booking' => [
            'subtitle' => 'You have a new booking!',
            'title' => 'New Booking Received!',
            'intro' => 'Great news! You have received a new booking for :hall.',
            'your_earnings' => 'Your Earnings',
            'booking_details' => 'Booking Details',
            'customer_info' => 'Customer Information',
            'customer_name' => 'Name',
            'customer_email' => 'Email',
            'customer_phone' => 'Phone',
            'special_notes' => 'Special Notes from Customer',
            'financial_summary' => 'Financial Summary',
            'platform_commission' => 'Platform Commission',
            'your_payout' => 'Your Payout',
            'action_required' => 'Action Required',
            'action_desc' => 'This booking requires your approval. Please review and approve or reject within 24 hours.',
            'view_booking' => 'View Booking',
            'approve_booking' => 'Approve Booking',
            'manage_note' => 'You can manage all your bookings from your Owner Dashboard.',
        ],

        /*
        |----------------------------------------------------------------------
        | Booking Cancelled (for Owner)
        |----------------------------------------------------------------------
        */
        'cancelled' => [
            'subtitle' => 'A booking has been cancelled',
            'title' => 'Booking Cancelled',
            'intro' => 'A booking for :hall has been cancelled.',
            'booking_details' => 'Cancelled Booking Details',
            'customer' => 'Customer',
            'reason_title' => 'Cancellation Reason',
            'financial_impact' => 'Financial Impact',
            'original_booking' => 'Original Booking Value',
            'lost_earnings' => 'Lost Earnings',
            'slot_available' => 'Slot Now Available',
            'slot_available_desc' => 'The :slot slot on :date is now available for new bookings.',
            'view_bookings' => 'View All Bookings',
            'support_note' => 'If you have any concerns, please contact our support team.',
        ],

        /*
        |----------------------------------------------------------------------
        | Account Verified
        |----------------------------------------------------------------------
        */
        'verified' => [
            'subtitle' => 'Welcome to Majalis!',
            'title' => 'Account Verified!',
            'intro' => 'Congratulations! Your hall owner account has been verified and you can now start managing your halls.',
            'badge' => 'Verified Owner',
            'what_you_can_do' => 'What You Can Do Now',
            'step_1_title' => 'Add Your Halls',
            'step_1_desc' => 'List your halls with photos, features, and pricing.',
            'step_2_title' => 'Set Availability',
            'step_2_desc' => 'Configure your calendar and available time slots.',
            'step_3_title' => 'Manage Bookings',
            'step_3_desc' => 'Receive and manage booking requests from customers.',
            'step_4_title' => 'Track Earnings',
            'step_4_desc' => 'Monitor your revenue and payout history.',
            'tips_title' => 'Tips for Success',
            'tip_1' => 'Upload high-quality photos of your hall',
            'tip_2' => 'Keep your availability calendar up to date',
            'tip_3' => 'Respond to booking requests promptly',
            'need_help' => 'Need Help?',
            'support_desc' => 'Our support team is here to help you get started. Contact us at',
            'go_to_dashboard' => 'Go to Dashboard',
            'welcome_message' => 'Welcome to the Majalis family! We\'re excited to have you on board.',
        ],
    ],
];
