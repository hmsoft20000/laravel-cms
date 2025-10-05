<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'الملف يجب أن يكون ملف',
                'role.exists' => 'الدور المحدد غير موجود',
                'role_ids.array' => 'معرفات الأدوار يجب أن تكون مصفوفة',
                'role_ids.*.required' => 'معرف الدور مطلوب',
                'role_ids.*.integer' => 'معرف الدور يجب أن يكون رقم صحيح',
                'role_ids.*.exists' => 'الدور المحدد غير موجود',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.name.string' => 'الاسم يجب أن يكون نص',
                'locales.*.name.unique' => 'الاسم مستخدم بالفعل',
            ],
            'attributes' => [
                'image' => 'الصورة',
                'role' => 'معرف الدور',
                'role_ids' => 'معرفات الأدوار',
                'role_ids.*' => 'معرف الدور',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.name' => 'الاسم',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'المنظمة المحددة غير موجودة',
                'image.file' => 'الملف يجب أن يكون ملف',
                'role.exists' => 'الدور المحدد غير موجود',
                'role_ids.array' => 'معرفات الأدوار يجب أن تكون مصفوفة',
                'role_ids.*.integer' => 'معرف الدور يجب أن يكون رقم صحيح',
                'role_ids.*.exists' => 'الدور المحدد غير موجود',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.name.string' => 'الاسم يجب أن يكون نص',
            ],
            'attributes' => [
                'id' => 'معرف المنظمة',
                'image' => 'الصورة',
                'role' => 'معرف الدور',
                'role_ids' => 'معرفات الأدوار',
                'role_ids.*' => 'معرف الدور',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.name' => 'الاسم',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.id.exists' => 'المنظمة المحددة غير موجودة',
                '*.image.file' => 'الملف يجب أن يكون ملف',
                '*.role_ids.array' => 'معرفات الأدوار يجب أن تكون مصفوفة',
                '*.role_ids.*.integer' => 'معرف الدور يجب أن يكون رقم صحيح',
                '*.role_ids.*.exists' => 'الدور المحدد غير موجود',
                '*.locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                '*.locales.*.locale.required' => 'رمز اللغة مطلوب',
                '*.locales.*.name.string' => 'الاسم يجب أن يكون نص',
            ],
            'attributes' => [
                '*.id' => 'معرف المنظمة',
                '*.image' => 'الصورة',
                '*.role_ids' => 'معرفات الأدوار',
                '*.role_ids.*' => 'معرف الدور',
                '*.locales' => 'الترجمات',
                '*.locales.*.locale' => 'رمز اللغة',
                '*.locales.*.name' => 'الاسم',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'المنظمة المحددة غير موجودة',
            ],
            'attributes' => [
                'id' => 'معرف المنظمة',
            ],
        ],
    ],
];
