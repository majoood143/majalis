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

    /*
    |--------------------------------------------------------------------------
    | Booking Details Page
    |--------------------------------------------------------------------------
    */

    'page_title_details' => 'Booking Details',
    'badge_guest' => 'Guest Booking',
    'btn_download_pdf' => 'Download PDF',
    'btn_create_account' => 'Create Account',

    // New translations for booking details page
    'details_heading' => 'Booking #:booking_number Details',
    'details_subheading' => 'View and manage your booking information below.',
    'details_section_booking_info' => 'Booking Information',
    'details_section_hall_info' => 'Hall Information',
    'details_section_price_summary' => 'Price Summary',
    'details_section_your_info' => 'Your Information',
    'details_section_services' => 'Additional Services',
    'details_section_payment_info' => 'Payment Information',

    'details_label_booking_number' => 'Booking Number',
    'details_label_booking_status' => 'Booking Status',
    'details_label_payment' => 'Payment',
    'details_label_hall' => 'Hall',
    'details_label_date' => 'Date',
    'details_label_time' => 'Time',
    'details_label_guests' => 'Number of Guests',
    'details_label_event_type' => 'Event Type',
    'details_label_services' => 'Services',
    'details_label_total_amount' => 'Total Amount',
    'details_label_payment_status' => 'Payment Status',
    'details_label_guest_name' => 'Guest Name',
    'details_label_guest_email' => 'Guest Email',
    'details_label_guest_phone' => 'Guest Phone',
    'details_label_phone' => 'Phone',
    'details_label_special_requests' => 'Special Requests',

    'details_payment_pending_message' => 'Your booking is awaiting payment. Please complete payment to confirm.',
    'details_no_services' => 'No additional services selected.',
    'details_need_help' => 'Need Help?',
    'details_help_message' => 'If you have any questions about your booking, please contact us.',
    'details_thank_you' => 'Thank you for your booking! We look forward to hosting you.',
    'details_contact_info' => 'If you have any questions, please contact us at :support_email.',

    'price_hall_rental' => 'Hall Rental',
    'price_services' => 'Services',
    'price_platform_fee' => 'Platform Fee',
    'price_total' => 'Total',
    'price_advance_paid' => 'Advance Paid',
    'price_balance_due' => 'Balance Due',

    'currency_omr' => 'OMR',

    'btn_complete_payment' => 'Complete Payment',
    'btn_browse_more_halls' => 'Browse More Halls',

    // Status translations
    'status_pending' => 'Pending',
    'status_confirmed' => 'Confirmed',
    'status_cancelled' => 'Cancelled',
    'status_completed' => 'Completed',

    // Payment status translations
    'payment_status_pending' => 'Pending',
    'payment_status_paid' => 'Paid',
    'payment_status_partial' => 'Partial',
    'payment_status_refunded' => 'Refunded',

    // Time slot translations
    'time_slot_morning' => 'Morning (8:00 AM - 12:00 PM)',
    'time_slot_afternoon' => 'Afternoon (1:00 PM - 5:00 PM)',
    'time_slot_evening' => 'Evening (6:00 PM - 10:00 PM)',
    'time_slot_full_day' => 'Full Day (8:00 AM - 10:00 PM)',

    // Event type translations
    'event_type_wedding' => 'Wedding',
    'event_type_birthday' => 'Birthday',
    'event_type_corporate' => 'Corporate',
    'event_type_graduation' => 'Graduation',
    'event_type_other' => 'Other',

    // Date format
    'date_format' => 'l, F j, Y',

    // Success page translations
    'success_title' => 'Payment Successful!',
    'success_subtitle' => 'Your booking has been confirmed successfully!',
    'success_booking_details' => 'Booking Details',
    'success_label_location' => 'Location',
    'success_label_additional_services' => 'Additional Services',
    'success_save_link_title' => 'Save Your Booking Link',

    // Account creation translations
    'create_account_title' => 'Create an Account',
    'create_account_description' => 'Create a free account to manage your bookings easily',
    'create_account_benefits' => [
        'Access all your bookings in one place',
        'Receive booking updates and reminders',
        'Faster checkout for future bookings',
        'Save your preferences and details',
    ],

    'label_password' => 'Password',
    'label_password_confirm' => 'Confirm Password',

    'btn_view_booking' => 'View Booking',
    'btn_processing' => 'Processing...',
    'btn_cancel' => 'Cancel',
    'btn_skip_account' => 'Skip and continue browsing halls',
    'btn_login_instead' => 'Login Instead',
    'btn_back_to_halls' => 'Back to Halls',
    'btn_back_to_previous' => 'Back to Previous Page',
    'receive_the_code' => 'Didn\'t receive the code?',
    'back'=> 'Back',
    'majalis' => 'Majalis',
    'rights_reserved' => 'All rights reserved.',
    'terms_agree'=> 'By continuing, you agree to our Terms of Service and Privacy Policy',
    'or'=> 'Or',
    // Additional keys needed for book.blade.php
    'booking_per_slot' => 'per slot',
    'phone_country_code' => '+968',
    'phone_placeholder_info' => 'e.g., 9xxxxxxx',

    // Placeholder for missing translations that might be needed
    // (These might already exist in your validation messages or main lang files)
    'validation_name_required' => 'Name is required',
    'validation_email_required' => 'Email is required',
    'validation_email_invalid' => 'Please enter a valid email address',
    'validation_phone_required' => 'Phone number is required',
    'validation_phone_digits' => 'Phone number must be 8 digits',
    // Booking form specific translations
    'form_your_information' => 'Your Information',
    'form_capacity' => 'Capacity',
    'form_guests_label' => 'guests',
    'form_select_event_type' => 'Select event type',
    'form_special_requests_placeholder' => 'Any special requirements or notes for your booking...',
    'form_agree_to' => 'I agree to the',
    'form_and' => 'and',
    'form_terms_conditions' => 'Terms & Conditions',
    'form_cancellation_policy' => 'Cancellation Policy',
    'form_booking_summary' => 'Booking Summary',
    'form_note_label' => 'Note',
    'form_advance_payment_note' => 'This hall allows advance payment of',
    'form_availability_error' => 'Error checking availability',

    // Event type additions (for conference which wasn't in your original list)
    'event_type_conference' => 'Conference',

    // JavaScript messages (for availability checking)
    'form_available_message' => 'This time slot is available',
    'form_not_available_message' => 'This time slot is not available',
    'form_select_date_time' => 'Please select a date and time slot',

    'account_already_exists' => 'An account with your email already exists. Please login to access your bookings.',

];
