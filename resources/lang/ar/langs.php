<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.required' => 'الاسم مطلوب',
                'name.string' => 'الاسم يجب أن يكون نص',
                'locale.required' => 'رمز اللغة مطلوب',
                'locale.string' => 'رمز اللغة يجب أن يكون نص',
                'direction.required' => 'الاتجاه مطلوب',
                'direction.string' => 'الاتجاه يجب أن يكون نص',
                'is_active.required' => 'حالة التفعيل مطلوبة',
            ],
            'attributes' => [
                'name' => 'الاسم',
                'locale' => 'رمز اللغة',
                'direction' => 'الاتجاه',
                'is_active' => 'حالة التفعيل',
            ],
        ],
        'update' => [
            'messages' => [
                'name.required' => 'الاسم مطلوب',
                'name.string' => 'الاسم يجب أن يكون نص',
                'locale.required' => 'رمز اللغة مطلوب',
                'locale.string' => 'رمز اللغة يجب أن يكون نص',
                'direction.required' => 'الاتجاه مطلوب',
                'direction.string' => 'الاتجاه يجب أن يكون نص',
                'is_active.required' => 'حالة التفعيل مطلوبة',
            ],
            'attributes' => [
                'name' => 'الاسم',
                'locale' => 'رمز اللغة',
                'direction' => 'الاتجاه',
                'is_active' => 'حالة التفعيل',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.required' => 'البيانات مطلوبة',
                '*.array' => 'البيانات يجب أن تكون مصفوفة',
                '*.id.required' => 'معرف اللغة مطلوب',
                '*.id.integer' => 'معرف اللغة يجب أن يكون رقم صحيح',
                '*.id.exists' => 'اللغة المحددة غير موجودة',
                '*.name.required' => 'الاسم مطلوب',
                '*.name.string' => 'الاسم يجب أن يكون نص',
                '*.locale.required' => 'رمز اللغة مطلوب',
                '*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                '*.direction.required' => 'الاتجاه مطلوب',
                '*.direction.string' => 'الاتجاه يجب أن يكون نص',
                '*.is_active.required' => 'حالة التفعيل مطلوبة',
            ],
            'attributes' => [
                '*' => 'البيانات',
                '*.id' => 'معرف اللغة',
                '*.name' => 'الاسم',
                '*.locale' => 'رمز اللغة',
                '*.direction' => 'الاتجاه',
                '*.is_active' => 'حالة التفعيل',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'اللغة المحددة غير موجودة',
            ],
            'attributes' => [
                'id' => 'معرف اللغة',
            ],
        ],
    ],
];
