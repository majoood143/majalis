<?php

return [
    // General
    'export_users' => 'تصدير المستخدمين',
    'yes' => 'نعم',
    'no' => 'لا',
    
    // Resource
    'resource' => [
        'model_label' => 'مستخدم',
        'plural_model_label' => 'المستخدمين',
        'navigation_label' => 'المستخدمين',
        'navigation_group' => 'إدارة المستخدمين',
    ],
    
    // Tabs
    'tabs' => [
        'all_users' => 'جميع المستخدمين',
        'administrators' => 'المسؤولون',
        'hall_owners' => 'أصحاب القاعات',
        'customers' => 'العملاء',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'email_verified' => 'البريد مؤكد',
        'unverified' => 'غير مؤكد',
    ],
    
    // Form
    'form' => [
        'sections' => [
            'user_information' => 'معلومات المستخدم',
            'contact_information' => 'معلومات الاتصال',
        ],
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_helper' => 'اتركه فارغًا للحفاظ على كلمة المرور الحالية',
        'role' => 'الدور',
        'phone' => 'الهاتف',
        'phone_country_code' => 'رمز الدولة',
        'is_active' => 'نشط',
    ],
    
    // Table
    'table' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'role' => 'الدور',
        'phone' => 'الهاتف',
        'verified' => 'مؤكد',
        'active' => 'نشط',
        'created_at' => 'تاريخ الإنشاء',
    ],
    
    // Filters
    'filters' => [
        'role' => 'الدور',
        'active' => 'نشط',
        'active_true' => 'المستخدمون النشطون',
        'active_false' => 'المستخدمون غير النشطين',
        'email_verified' => 'البريد مؤكد',
        'verified_true' => 'مؤكد',
        'verified_false' => 'غير مؤكد',
    ],
    
    // Actions
    'actions' => [
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'delete_bulk' => 'حذف المحدد',
    ],
    
    // Export
    'export' => [
        'id' => 'الرقم',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'role' => 'الدور',
        'phone' => 'الهاتف',
        'email_verified' => 'البريد مؤكد',
        'active' => 'نشط',
        'created_at' => 'تاريخ الإنشاء',
        'success_title' => 'تم التصدير بنجاح',
        'success_body' => 'تم تصدير الملف :filename بنجاح.',
        'download' => 'تحميل',
    ],
    
    // Roles
    'roles' => [
        'admin' => 'مسؤول',
        'hall_owner' => 'صاحب قاعة',
        'customer' => 'عميل',
    ],
];