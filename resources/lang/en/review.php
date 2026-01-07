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

    // Common
    'yes' => 'Yes',
    'no' => 'No',
    'n_a' => 'N/A',
    'placeholder' => [
        'no_response' => 'No response',
    ],
];
