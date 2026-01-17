<?php

declare(strict_types=1);

/**
 * Guest Booking Language File (English)
 *
 * Contains all translation strings for the guest booking feature.
 *
 * @package Lang\En
 * @version 1.0.0
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Page Titles
    |--------------------------------------------------------------------------
    */

    'page_title_book' => 'Book as Guest',
    'page_title_verify' => 'Verify Your Email',
    'page_title_form' => 'Complete Your Booking',
    'page_title_payment' => 'Payment',
    'page_title_success' => 'Booking Confirmed',
    'page_title_details' => 'Booking Details',

    /*
    |--------------------------------------------------------------------------
    | Step Labels
    |--------------------------------------------------------------------------
    */

    'step_1_guest_info' => 'Your Information',
    'step_2_verify' => 'Verify Email',
    'step_3_booking' => 'Booking Details',
    'step_4_payment' => 'Payment',

    /*
    |--------------------------------------------------------------------------
    | Form Labels
    |--------------------------------------------------------------------------
    */

    'label_name' => 'Full Name',
    'label_email' => 'Email Address',
    'label_phone' => 'Phone Number',
    'label_otp' => 'Verification Code',
    'label_password' => 'Password',
    'label_password_confirm' => 'Confirm Password',

    /*
    |--------------------------------------------------------------------------
    | Placeholders
    |--------------------------------------------------------------------------
    */

    'placeholder_name' => 'Enter your full name',
    'placeholder_email' => 'Enter your email address',
    'placeholder_phone' => 'e.g., 9xxxxxxx',
    'placeholder_otp' => 'Enter 6-digit code',

    /*
    |--------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------
    */

    'btn_continue' => 'Continue',
    'btn_verify' => 'Verify',
    'btn_resend_otp' => 'Resend Code',
    'btn_submit_booking' => 'Proceed to Payment',
    'btn_pay_now' => 'Pay Now',
    'btn_create_account' => 'Create Account',
    'btn_skip_account' => 'No Thanks',
    'btn_view_booking' => 'View Booking',
    'btn_download_pdf' => 'Download PDF',
    'btn_login_instead' => 'Log In Instead',
    'btn_continue_as_guest' => 'Continue as Guest',

    /*
    |--------------------------------------------------------------------------
    | Success Messages
    |--------------------------------------------------------------------------
    */

    'otp_sent' => 'A verification code has been sent to :email',
    'otp_resent' => 'A new verification code has been sent to your email.',
    'otp_verified' => 'Email verified successfully! Please complete your booking.',
    'booking_created' => 'Your booking has been created. Please complete payment.',
    'payment_successful' => 'Payment successful! Your booking is confirmed.',
    'account_created' => 'Account created successfully! :count booking(s) have been linked to your account.',

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    */

    'session_expired' => 'Your session has expired. Please start again.',
    'session_invalid' => 'Invalid session. Please start the booking process again.',
    'verification_required' => 'Please verify your email before proceeding.',
    'otp_required' => 'Please enter the verification code.',
    'otp_invalid_length' => 'The verification code must be 6 digits.',
    'otp_digits_only' => 'The verification code must contain only numbers.',
    'otp_incorrect' => 'Incorrect verification code. :remaining attempts remaining.',
    'otp_expired' => 'The verification code has expired. Please request a new one.',
    'otp_locked' => 'Too many failed attempts. Please request a new verification code.',
    'otp_resend_wait' => 'Please wait :seconds seconds before requesting a new code.',
    'otp_resend_failed' => 'Failed to send verification code. Please try again.',
    'initiation_failed' => 'Failed to start booking. Please try again.',
    'booking_not_found' => 'Booking not found or access link has expired.',
    'max_pending_bookings' => 'You have reached the maximum of :count pending bookings. Please complete or cancel existing bookings first.',
    'too_many_sessions' => 'Too many pending booking sessions. Please complete or wait for existing sessions to expire.',
    'account_already_exists' => 'An account with this email already exists. Please log in.',
    'account_creation_failed' => 'Failed to create account. Please try again.',

    /*
    |--------------------------------------------------------------------------
    | Info Messages
    |--------------------------------------------------------------------------
    */

    'email_registered_prompt' => 'This email is already registered. Would you like to log in or continue as a guest?',
    'otp_info' => 'We\'ve sent a 6-digit verification code to your email. Please enter it below.',
    'otp_expires_info' => 'This code will expire in 10 minutes.',
    'booking_access_info' => 'Save this link to access your booking details anytime.',
    'create_account_prompt' => 'Would you like to create an account? This will let you easily manage all your bookings.',
    'guest_booking_note' => 'You are booking as a guest. You can create an account after completing your booking.',

    /*
    |--------------------------------------------------------------------------
    | Email: OTP
    |--------------------------------------------------------------------------
    */

    'otp_email_subject' => 'Your Booking Verification Code - :app',
    'otp_email_greeting' => 'Hello :name,',
    'otp_email_intro' => 'You are booking :hall. Please use the following code to verify your email:',
    'otp_email_code_label' => 'Your verification code is:',
    'otp_email_expires' => 'This code will expire in :minutes minutes.',
    'otp_email_warning' => 'If you did not request this code, please ignore this email.',
    'otp_email_salutation' => 'Best regards,|:app Team',

    /*
    |--------------------------------------------------------------------------
    | Email: Booking Confirmation
    |--------------------------------------------------------------------------
    */

    'confirmation_email_subject' => 'Booking Confirmed - :booking_number',
    'confirmation_email_greeting' => 'Hello :name,',
    'confirmation_email_intro' => 'Your booking has been confirmed! Here are the details:',
    'view_booking_details' => 'View Booking Details',
    'confirmation_email_access_info' => 'You can use the link above to view your booking details anytime.',
    'confirmation_email_create_account_hint' => 'Tip: Create an account to easily manage all your bookings in one place!',
    'confirmation_email_salutation' => 'Thank you for choosing :app!',

    /*
    |--------------------------------------------------------------------------
    | Booking Choice Modal
    |--------------------------------------------------------------------------
    */

    'modal_title' => 'How would you like to book?',
    'modal_login_option' => 'Log In',
    'modal_login_description' => 'Access your account to manage bookings easily',
    'modal_register_option' => 'Create Account',
    'modal_register_description' => 'Sign up for easier booking management',
    'modal_guest_option' => 'Continue as Guest',
    'modal_guest_description' => 'Book without creating an account',

    /*
    |--------------------------------------------------------------------------
    | Account Creation Section
    |--------------------------------------------------------------------------
    */

    'create_account_title' => 'Create Your Account',
    'create_account_description' => 'Create an account to easily manage your bookings, track payments, and get exclusive offers.',
    'create_account_benefits' => [
        'View all your bookings in one place',
        'Faster checkout for future bookings',
        'Receive special offers and discounts',
        'Leave reviews for halls you\'ve visited',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Admin Labels
    |--------------------------------------------------------------------------
    */

    'badge_guest' => 'Guest',
    'badge_guest_converted' => 'Guest (Converted)',
    'badge_registered' => 'Registered',
    'filter_booking_type' => 'Booking Type',
    'filter_guest_only' => 'Guest Bookings Only',
    'filter_registered_only' => 'Registered Users Only',

];
