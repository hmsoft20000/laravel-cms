<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'image.file' => 'الملف يجب أن يكون ملف',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.title.string' => 'العنوان يجب أن يكون نص',
                'locales.*.slug.string' => 'الرابط يجب أن يكون نص',
                'locales.*.slug.unique' => 'الرابط مستخدم بالفعل',
                'locales.*.sub_title.string' => 'العنوان الفرعي يجب أن يكون نص',
                'locales.*.meta_title.string' => 'عنوان الميتا يجب أن يكون نص',
                'locales.*.meta_description.string' => 'وصف الميتا يجب أن يكون نص',
                'meta_keywords.array' => 'كلمات الميتا يجب أن تكون مصفوفة',
                'meta_keywords.*.string' => 'كلمة الميتا يجب أن تكون نص',
            ],
            'attributes' => [
                'image' => 'الصورة',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.slug' => 'الرابط',
                'locales.*.sub_title' => 'العنوان الفرعي',
                'locales.*.meta_title' => 'عنوان الميتا',
                'locales.*.meta_description' => 'وصف الميتا',
                'meta_keywords' => 'كلمات الميتا',
                'meta_keywords.*' => 'كلمة الميتا',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'عنصر القيم المحدد غير موجود',
                'image.file' => 'الملف يجب أن يكون ملف',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.title.string' => 'العنوان يجب أن يكون نص',
                'locales.*.slug.string' => 'الرابط يجب أن يكون نص',
                'locales.*.slug.unique' => 'الرابط مستخدم بالفعل',
                'locales.*.sub_title.string' => 'العنوان الفرعي يجب أن يكون نص',
                'locales.*.meta_title.string' => 'عنوان الميتا يجب أن يكون نص',
                'locales.*.meta_description.string' => 'وصف الميتا يجب أن يكون نص',
                'meta_keywords.array' => 'كلمات الميتا يجب أن تكون مصفوفة',
                'meta_keywords.*.string' => 'كلمة الميتا يجب أن تكون نص',
            ],
            'attributes' => [
                'id' => 'معرف عنصر القيم',
                'image' => 'الصورة',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.slug' => 'الرابط',
                'locales.*.sub_title' => 'العنوان الفرعي',
                'locales.*.meta_title' => 'عنوان الميتا',
                'locales.*.meta_description' => 'وصف الميتا',
                'meta_keywords' => 'كلمات الميتا',
                'meta_keywords.*' => 'كلمة الميتا',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'عنصر القيم المحدد غير موجود',
            ],
            'attributes' => [
                'id' => 'معرف عنصر القيم',
            ],
        ],
    ],
];
