<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'file.file' => 'يجب أن يكون الملف ملفًا.',
                'file.max' => 'قد لا يزيد حجم الملف عن 10240 كيلوبايت.',
                'locales.required' => 'حقل اللغات مطلوب.',
                'locales.array' => 'يجب أن تكون اللغات مصفوفة.',
                'locales.min' => 'مطلوب لغة واحدة على الأقل.',
                'locales.*.locale.required' => 'حقل اللغة مطلوب.',
                'locales.*.locale.string' => 'يجب أن تكون اللغة نصًا.',
                'locales.*.title.required' => 'حقل العنوان مطلوب.',
                'locales.*.title.string' => 'يجب أن يكون العنوان نصًا.',
                'locales.*.title.max' => 'قد لا يزيد العنوان عن 255 حرفًا.',
                'file_path.required' => 'مسار الملف مطلوب.',
            ],
            'attributes' => [
                'file' => 'الملف',
                'locales' => 'اللغات',
                'locales.*.locale' => 'اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.description' => 'الوصف',
                'is_active' => 'نشط',
                'sort_number' => 'رقم الفرز',
                'file_path' => 'مسار الملف',
            ],
        ],
        'update' => [
            'messages' => [
                'file.file' => 'يجب أن يكون الملف ملفًا.',
                'file.max' => 'قد لا يزيد حجم الملف عن 10240 كيلوبايت.',
                'locales.array' => 'يجب أن تكون اللغات مصفوفة.',
                'locales.min' => 'مطلوب لغة واحدة على الأقل.',
                'locales.*.locale.required' => 'حقل اللغة مطلوب.',
                'locales.*.locale.string' => 'يجب أن تكون اللغة نصًا.',
                'locales.*.title.required' => 'حقل العنوان مطلوب.',
                'locales.*.title.string' => 'يجب أن يكون العنوان نصًا.',
                'locales.*.title.max' => 'قد لا يزيد العنوان عن 255 حرفًا.',
            ],
            'attributes' => [
                'file' => 'الملف',
                'locales' => 'اللغات',
                'locales.*.locale' => 'اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.description' => 'الوصف',
                'is_active' => 'نشط',
                'sort_number' => 'رقم الفرز',
                'delete_file' => 'حذف الملف',
                'file_path' => 'مسار الملف',
            ],
        ],
    ],
];
