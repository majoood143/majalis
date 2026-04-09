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
        'regards' => 'Warm Regards,',
        'team'    => 'The :app Team',
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
    ],
];
