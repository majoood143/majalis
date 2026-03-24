<?php

return [

    // Resource Labels
    'singular' => 'Review',
    'plural' => 'Reviews',
    'navigation_label' => 'Reviews',
    // Sections
    'sections' => [
        'review_information' => 'Review Information',
        'detailed_ratings' => 'Detailed Ratings',
        'moderation' => 'Moderation',
        'owner_response' => 'Owner Response',
    ],

    // Fields
    'fields' => [
        'hall' => 'Hall',
        'booking' => 'Booking',
        'user' => 'User',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'cleanliness_rating' => 'Cleanliness Rating',
        'service_rating' => 'Service Rating',
        'value_rating' => 'Value Rating',
        'location_rating' => 'Location Rating',
        'is_approved' => 'Is Approved',
        'is_featured' => 'Is Featured',
        'admin_notes' => 'Admin Notes',
        'owner_response' => 'Owner Response',
        'owner_response_at' => 'Owner Response At',
        'rejection_reason' => 'Rejection Reason',
    ],

    // Ratings
    'ratings' => [
        '1_star' => '⭐ 1 Star',
        '2_stars' => '⭐⭐ 2 Stars',
        '3_stars' => '⭐⭐⭐ 3 Stars',
        '4_stars' => '⭐⭐⭐⭐ 4 Stars',
        '5_stars' => '⭐⭐⭐⭐⭐ 5 Stars',
    ],

    // Status
    'status' => [
        'approved' => 'Approved',
        'pending' => 'Pending',
        'featured' => 'Featured',
        'not_featured' => 'Not Featured',
    ],

    // Actions
    'actions' => [
        'export' => 'Export Reviews',
        'export_modal_heading' => 'Export Reviews',
        'export_modal_description' => 'Export all reviews data to CSV.',
        'bulk_approve' => 'Approve Pending',
        'bulk_approve_modal_heading' => 'Approve Pending Reviews',
        'bulk_approve_modal_description' => 'This will approve all pending reviews.',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'download' => 'Download File',
    ],

    // Tabs
    'tabs' => [
        'all' => 'All Reviews',
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'featured' => 'Featured',
        '5_stars' => '5 Stars',
        'low_rated' => 'Low Rated (≤2)',
        'with_response' => 'With Response',
    ],

    // Columns
    'columns' => [
        'hall' => 'Hall',
        'user' => 'User',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'is_approved' => 'Approved',
        'is_featured' => 'Featured',
        'owner_response' => 'Owner Response',
        'created_at' => 'Created At',
    ],

    // Filters
    'filters' => [
        'hall' => 'Hall',
        'rating' => 'Rating',
        'approved' => 'Approved',
        'featured' => 'Featured',
        'has_owner_response' => 'Has Owner Response',
    ],

    // Export Headers
    'export' => [
        'id' => 'ID',
        'hall' => 'Hall',
        'user' => 'User',
        'booking' => 'Booking',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'cleanliness' => 'Cleanliness',
        'service' => 'Service',
        'value' => 'Value',
        'location' => 'Location',
        'approved' => 'Approved',
        'featured' => 'Featured',
        'owner_response' => 'Owner Response',
        'created_at' => 'Created At',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'Export Successful',
        'export_success_body' => 'Reviews data exported successfully.',
        'export_error' => 'Export Failed',
        'bulk_approve_success' => 'Reviews Approved',
        'bulk_approve_success_body' => ':count review(s) approved successfully.',
        'update_error' => 'Operation Failed',
    ],

    // Columns (additional)
    'columns_extra' => [
        'is_late_review'    => 'Late Review',
        'marketing_consent' => 'Marketing Consent',
    ],

    // Filters (additional)
    'filters_extra' => [
        'late_review'       => 'Late Review',
        'marketing_consent' => 'Marketing Consent',
    ],

    // Messages shown on review submission pages
    'messages' => [
        'invalid_link'          => 'This review link is invalid. Please use the link from your email.',
        'booking_not_found'     => 'Booking not found.',
        'invalid_token'         => 'This review link has been tampered with or is invalid.',
        'booking_not_completed' => 'Reviews can only be submitted for completed bookings.',
        'window_expired'        => 'The review window for this booking has closed (14 days after the event).',
        'already_submitted'     => 'You have already submitted a review for this booking. Thank you!',
        'rating_required'       => 'Please select a star rating before submitting.',
        'late_review_notice'    => 'You are submitting this review during the grace period (8–14 days after your event). Thank you for still sharing your feedback!',
    ],

    // Review submission page labels
    'page' => [
        'submit_title'            => 'Leave a Review',
        'submit_heading'          => 'How was your experience?',
        'submit_subheading'       => 'Your feedback helps others choose the perfect venue.',
        'booking_summary'         => 'Your Booking',
        'event_date'              => 'Event Date',
        'time_slot'               => 'Time Slot',
        'overall_rating'          => 'Overall Rating',
        'star_label_1'            => '1 – Poor',
        'star_label_2'            => '2 – Fair',
        'star_label_3'            => '3 – Good',
        'star_label_4'            => '4 – Very Good',
        'star_label_5'            => '5 – Excellent',
        'comment_placeholder'     => 'Tell us about your experience…',
        'comment_hint'            => 'Optional. Share anything you liked or would improve.',
        'comment_required_hint'   => 'Please tell us more so we can improve (min. 10 characters).',
        'photos_label'            => 'Add Photos (optional)',
        'photos_choose'           => 'Click to choose',
        'photos_or_drag'          => 'or drag photos here',
        'photos_hint'             => 'Up to 5 images · JPEG, PNG, WebP · max 4 MB each',
        'marketing_consent_label' => 'I\'d like to receive special offers and promotions from ' . config('app.name') . '.',
        'submit_button'           => 'Submit Review',
        'link_invalid_title'      => 'Link Invalid or Expired',
        'thankyou_title'          => 'Thank You!',
        'thankyou_heading'        => 'Thank you for your review!',
        'thankyou_body'           => 'Your feedback has been received and will help future guests make the best choice.',
        'thankyou_late_badge'     => 'Grace Period Review',
        'thankyou_cta'            => 'Explore More Halls',
        'powered_by'              => 'Powered by :app',
    ],

    // Common
    'yes' => 'Yes',
    'no' => 'No',
    'n_a' => 'N/A',
    'placeholder' => [
        'no_response' => 'No response',
    ],
];
