<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */
    'navigation_label'  => 'Settings',
    'navigation_group'  => 'Settings',
    'title'             => 'Application Settings',
    'subheading'        => 'Manage your application-wide configuration',
    'save_button'       => 'Save Settings',
    'saved'             => 'Settings saved successfully',

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */
    'tabs' => [
        'general' => 'General',
        'contact' => 'Contact',
        'social'  => 'Social Media',
        'finance' => 'Finance & Banking',
        'gtag'    => 'Google Analytics',
        'seo'     => 'SEO & Meta Tags',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Headings
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'site_identity'      => 'Site Identity',
        'site_identity_desc' => 'Basic information about your platform displayed to users.',
        'regional'           => 'Regional Settings',
        'regional_desc'      => 'Timezone and language preferences.',
        'email_contact'      => 'Email Addresses',
        'email_contact_desc' => 'Primary contact and support email addresses.',
        'phone_contact'      => 'Phone & WhatsApp',
        'phone_contact_desc' => 'Phone numbers used for customer inquiries.',
        'address'            => 'Office Address',
        'social_media'       => 'Social Media Profiles',
        'social_media_desc'  => 'Add links to your official social media pages.',
        'tax'                => 'Tax Settings',
        'tax_desc'           => 'Value Added Tax rate applied to bookings.',
        'bank'               => 'Bank Account Details',
        'bank_desc'          => 'Bank information displayed on invoices and payout documents.',
        'gtag'               => 'Google Tag / Analytics',
        'gtag_desc'          => 'Configure Google Analytics tracking for your platform.',

        'favicon'            => 'Favicon',
        'favicon_desc'       => 'The small icon shown in browser tabs.',
        'open_graph'         => 'Open Graph (Facebook / LinkedIn)',
        'open_graph_desc'    => 'Controls how your pages appear when shared on social platforms.',
        'twitter_card'       => 'Twitter / X Card',
        'twitter_card_desc'  => 'Controls the preview card shown when links are shared on X (Twitter).',
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Labels
    |--------------------------------------------------------------------------
    */
    'fields' => [
        // General
        'site_name'        => 'Site Name',
        'site_tagline'     => 'Tagline',
        'site_description' => 'Site Description',
        'timezone'         => 'Timezone',
        'default_locale'   => 'Default Language',

        // Contact
        'email'            => 'Primary Email',
        'support_email'    => 'Support Email',
        'phone'            => 'Phone Number',
        'mobile'           => 'Mobile Number',
        'whatsapp'         => 'WhatsApp Number',
        'fax'              => 'Fax Number',
        'address'          => 'Office Address',
        'google_maps_url'  => 'Google Maps Link',

        // Finance
        'vat_rate'           => 'VAT Rate',
        'currency'           => 'Currency Code',
        'bank_name'          => 'Bank Name',
        'bank_account_name'  => 'Account Holder Name',
        'bank_iban'          => 'IBAN',
        'bank_swift'         => 'SWIFT / BIC Code',

        // Google Analytics
        'gtag_id'       => 'Measurement ID',
        'gtag_enabled'  => 'Enable Tracking',
        'anonymize_ip'  => 'Anonymize IP Addresses',

        // SEO
        'favicon'              => 'Favicon',
        'og_title'             => 'OG Title',
        'og_description'       => 'OG Description',
        'og_image'             => 'OG Image',
        'og_type'              => 'OG Type',
        'twitter_card'         => 'Card Type',
        'twitter_site'         => 'Twitter / X Handle',
        'twitter_title'        => 'Twitter Title',
        'twitter_description'  => 'Twitter Description',
        'twitter_image'        => 'Twitter Image',
    ],

    /*
    |--------------------------------------------------------------------------
    | Placeholders & Helpers
    |--------------------------------------------------------------------------
    */
    'placeholders' => [
        'site_tagline' => 'Book the perfect hall for your occasion',
    ],

    'helpers' => [
        'whatsapp'      => 'Include country code, e.g. +968 9000 0000',
        'vat_rate'      => 'Enter 0 to disable VAT on bookings.',
        'currency'      => 'ISO 4217 currency code, e.g. OMR, SAR, AED.',
        'anonymize_ip'  => 'Recommended for GDPR compliance.',
        'favicon'       => 'Recommended: 32×32 or 64×64 px, .ico or .png format.',
        'og_image'      => 'Recommended size: 1200×630 px.',
        'twitter_site'  => 'Your Twitter / X username including the @ symbol.',
        'twitter_image' => 'Recommended size: 1200×628 px.',
    ],

];
