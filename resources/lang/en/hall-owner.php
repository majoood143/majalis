<?php

return [

    // Resource Labels
    'singular' => 'Hall Owner',
    'plural' => 'Hall Owners',
    'navigation_label' => 'Hall Owners',
    
    // Actions
    'actions' => [
        'export' => 'Export Owners',
        'export_modal_heading' => 'Export Hall Owners',
        'export_modal_description' => 'Export all hall owner data to CSV.',
        'bulk_verify' => 'Bulk Verify',
        'bulk_verify_modal_heading' => 'Verify All Pending Owners',
        'bulk_verify_modal_description' => 'This will verify all unverified hall owners.',
        'send_notification' => 'Send Notification',
        'generate_report' => 'Generate Report',
        'verify' => 'Verify',
        'reject' => 'Reject',
        'download' => 'Download File',
        'download_report' => 'Download Report',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],

    // Fields
    'fields' => [
        'user_id' => 'Owner',
        'business_name' => 'Business Name',
        'business_name_ar' => 'Business Name (Arabic)',
        'commercial_registration' => 'Commercial Registration',
        'tax_number' => 'Tax Number',
        'business_phone' => 'Business Phone',
        'business_email' => 'Business Email',
        'business_address' => 'Business Address',
        'business_address_ar' => 'Business Address (Arabic)',
        'bank_name' => 'Bank Name',
        'bank_account_name' => 'Bank Account Name',
        'bank_account_number' => 'Bank Account Number',
        'iban' => 'IBAN',
        'commission_type' => 'Commission Type',
        'commission_value' => 'Commission Value',
        'is_verified' => 'Is Verified',
        'is_active' => 'Is Active',
        'verification_notes' => 'Verification Notes',
        'notes' => 'Notes',
        'filter' => 'Send To',
        'subject' => 'Subject',
        'message' => 'Message',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'rejection_reason' => 'Rejection Reason',
    ],

    // Options
    'options' => [
        'all' => 'All Owners',
        'verified' => 'Verified Only',
        'unverified' => 'Unverified Only',
        'active' => 'Active Only',
        'percentage' => 'Percentage',
        'fixed' => 'Fixed Amount',
        'verify' => 'Verify',
        'reject' => 'Reject',
    ],

    // Tabs
    'tabs' => [
        'all' => 'All Owners',
        'pending_verification' => 'Pending Verification',
        'verified' => 'Verified',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'custom_commission' => 'Custom Commission',
        'with_halls' => 'With Halls',
        'without_halls' => 'Without Halls',
        'incomplete_documents' => 'Incomplete Documents',
        'business_info' => 'Business Info',
        'contact' => 'Contact',
        'bank_details' => 'Bank Details',
        'documents' => 'Documents',
        'verification' => 'Verification',
        'commission' => 'Commission',
    ],

    // Columns
    'columns' => [
        'owner_name' => 'Owner Name',
        'business_name' => 'Business Name',
        'commercial_registration' => 'Commercial Registration',
        'business_phone' => 'Business Phone',
        'is_verified' => 'Verified',
        'is_active' => 'Active',
        'created_at' => 'Created At',
    ],

    // Export Headers
    'export' => [
        'id' => 'ID',
        'owner_name' => 'Owner Name',
        'business_name' => 'Business Name',
        'business_name_ar' => 'Business Name (AR)',
        'commercial_registration' => 'Commercial Registration',
        'tax_number' => 'Tax Number',
        'business_phone' => 'Business Phone',
        'business_email' => 'Business Email',
        'bank_name' => 'Bank Name',
        'iban' => 'IBAN',
        'commission_type' => 'Commission Type',
        'commission_value' => 'Commission Value',
        'verified' => 'Verified',
        'active' => 'Active',
        'verified_at' => 'Verified At',
        'total_halls' => 'Total Halls',
        'created_at' => 'Created At',
    ],

    // Info List
    'infolist' => [
        'business_information' => 'Business Information',
        'owner' => 'Owner',
        'contact_information' => 'Contact Information',
        'bank_details' => 'Bank Details',
        'verification_status' => 'Verification Status',
        'verified' => 'Verified',
        'pending' => 'Pending',
        'verified_by' => 'Verified By',
        'statistics' => 'Statistics',
        'total_halls' => 'Total Halls',
        'active_halls' => 'Active Halls',
        'total_bookings' => 'Total Bookings',
        'total_revenue' => 'Total Revenue',
    ],

    // Notifications
    'notifications' => [
        'export_success' => 'Export Successful',
        'export_success_body' => 'Hall owners exported successfully.',
        'export_error' => 'Export Failed',
        'bulk_verify_success' => 'Bulk Verification Completed',
        'bulk_verify_success_body' => ':count owner(s) have been verified.',
        'notification_sent' => 'Notifications Sent',
        'notification_sent_body' => ':count notification(s) sent successfully.',
        'report_generated' => 'Report Generated Successfully',
        'report_generated_body' => 'All hall owners report has been generated.',
        'report_failed' => 'Report Generation Failed',
        'owner_verified' => 'Owner Verified',
        'owner_rejected' => 'Owner Rejected',
        'owner_updated' => 'Owner Updated',
        'owner_deleted' => 'Owner Deleted',
        'update_error' => 'Operation Failed',
    ],

    // Filters
    'filters' => [
        'verified' => 'Verified',
        'active' => 'Active',
    ],

    // Common
    'yes' => 'Yes',
    'no' => 'No',
    'note' => 'Leave empty to use global commission settings',
    'n_a' => 'N/A',
    'not_verified' => 'Not Verified',
];
