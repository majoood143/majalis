<?php

declare(strict_types=1);

/**
 * Arabic validation messages.
 *
 * Covers the rules used by the customer forms and the mobile API (/api/v1).
 * Any rule missing here falls back to the English message.
 */
return [

    'accepted' => 'يجب قبول :attribute.',
    'array' => 'يجب أن يكون :attribute قائمة.',
    'between' => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => ':attribute ليس تاريخًا صحيحًا.',
    'date_format' => 'يجب أن يكون :attribute بالصيغة :format.',
    'after' => 'يجب أن يكون :attribute بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute في :date أو بعده.',
    'email' => 'يجب أن يكون :attribute بريدًا إلكترونيًا صحيحًا.',
    'exists' => ':attribute المحدد غير صالح.',
    'in' => ':attribute المحدد غير صالح.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'lowercase' => 'يجب أن يكون :attribute بأحرف صغيرة.',
    'max' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
        'string' => 'يجب ألا يزيد :attribute عن :max حرفًا.',
    ],
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على :min عناصر على الأقل.',
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'string' => 'يجب ألا يقل :attribute عن :min أحرف.',
    ],
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'password' => [
        'letters' => 'يجب أن تحتوي :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن تحتوي :attribute على حرف كبير وحرف صغير على الأقل.',
        'numbers' => 'يجب أن تحتوي :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن تحتوي :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'ظهرت :attribute في تسريب بيانات. اختر كلمة مرور أخرى.',
    ],
    'regex' => 'صيغة :attribute غير صحيحة.',
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'unique' => ':attribute مستخدم من قبل.',

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'password' => 'كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'device_name' => 'اسم الجهاز',
        'number_of_guests' => 'عدد الضيوف',
        'customer_phone' => 'رقم الهاتف',
        'promo_code' => 'رمز الخصم',
        'days' => 'الأيام',
        'language_preference' => 'اللغة',
        'token' => 'رمز الجهاز',
        'platform' => 'نظام التشغيل',
        'date' => 'التاريخ',
        'time_slot' => 'الفترة',
        'from' => 'من تاريخ',
        'to' => 'إلى تاريخ',
    ],

];
