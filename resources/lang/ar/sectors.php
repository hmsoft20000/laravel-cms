<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'الملف يجب أن يكون ملف',
                'work_ratio.numeric' => 'نسبة العمل يجب أن تكون رقم',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.min' => 'يجب إضافة ترجمة واحدة على الأقل',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                'locales.*.name.string' => 'الاسم يجب أن يكون نص',
                'locales.*.name.unique' => 'الاسم مستخدم بالفعل',
            ],
            'attributes' => [
                'image' => 'الصورة',
                'work_ratio' => 'نسبة العمل',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.short_content' => 'المحتوى المختصر',
                'locales.*.name' => 'الاسم',
            ],
        ],
        'update' => [
            'messages' => [
                'image.file' => 'الملف يجب أن يكون ملف',
                'work_ratio.numeric' => 'نسبة العمل يجب أن تكون رقم',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.min' => 'يجب إضافة ترجمة واحدة على الأقل',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                'locales.*.name.string' => 'الاسم يجب أن يكون نص',
                'locales.*.name.unique' => 'الاسم مستخدم بالفعل',
            ],
            'attributes' => [
                'image' => 'الصورة',
                'work_ratio' => 'نسبة العمل',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.short_content' => 'المحتوى المختصر',
                'locales.*.name' => 'الاسم',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.required' => 'البيانات مطلوبة',
                '*.array' => 'البيانات يجب أن تكون مصفوفة',
                '*.id.required' => 'معرف القطاع مطلوب',
                '*.id.integer' => 'معرف القطاع يجب أن يكون رقم صحيح',
                '*.id.exists' => 'القطاع المحدد غير موجود',
                '*.image.file' => 'الملف يجب أن يكون ملف',
                '*.work_ratio.numeric' => 'نسبة العمل يجب أن تكون رقم',
                '*.locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                '*.locales.min' => 'يجب إضافة ترجمة واحدة على الأقل',
                '*.locales.*.locale.required' => 'رمز اللغة مطلوب',
                '*.locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                '*.locales.*.name.string' => 'الاسم يجب أن يكون نص',
            ],
            'attributes' => [
                '*' => 'البيانات',
                '*.id' => 'معرف القطاع',
                '*.image' => 'الصورة',
                '*.work_ratio' => 'نسبة العمل',
                '*.locales' => 'الترجمات',
                '*.locales.*.locale' => 'رمز اللغة',
                '*.locales.*.short_content' => 'المحتوى المختصر',
                '*.locales.*.name' => 'الاسم',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'القطاع المحدد غير موجود',
            ],
            'attributes' => [
                'id' => 'معرف القطاع',
            ],
        ],
    ],
];
