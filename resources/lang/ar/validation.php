<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'required_without' => 'حقل :attribute مطلوب عند غياب :values.',
    'email' => 'يجب أن يكون :attribute بريدًا إلكترونيًا صحيحًا.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'min' => [
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'string' => 'يجب أن يحتوي :attribute على :min أحرف على الأقل.',
    ],
    'max' => [
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
        'string' => 'يجب ألا يزيد :attribute عن :max أحرف.',
        'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.',
    ],
    'exists' => ':attribute المحدد غير صالح.',
    'unique' => ':attribute مستخدم بالفعل.',
    'in' => ':attribute المحدد غير صالح.',
    'date' => ':attribute ليس تاريخًا صحيحًا.',
    'digits' => 'يجب أن يكون :attribute مكونًا من :digits أرقام.',
    'attributes' => [
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'type' => 'نوع الحجز',
        'doctor_id' => 'الطبيب',
        'department_id' => 'القسم',
        'payment_method' => 'طريقة الدفع',
    ],
];
