<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.required' => 'الاسم مطلوب',
                'name.unique' => 'الاسم مستخدم بالفعل',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.title.string' => 'العنوان يجب أن يكون نص',
                'locales.*.description.string' => 'الوصف يجب أن يكون نص',
                'locales.*.keywords.string' => 'الكلمات المفتاحية يجب أن تكون نص',
            ],
            'attributes' => [
                'name' => 'الاسم',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.description' => 'الوصف',
                'locales.*.keywords' => 'الكلمات المفتاحية',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'صفحة الميتا المحددة غير موجودة',
                'name.required' => 'الاسم مطلوب',
                'name.unique' => 'الاسم مستخدم بالفعل',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.title.string' => 'العنوان يجب أن يكون نص',
                'locales.*.title.max' => 'العنوان يجب أن يكون أقل من 255 حرف',
                'locales.*.description.string' => 'الوصف يجب أن يكون نص',
                'locales.*.keywords.string' => 'الكلمات المفتاحية يجب أن تكون نص',
                'locales.*.keywords.max' => 'الكلمات المفتاحية يجب أن تكون أقل من 255 حرف',
            ],
            'attributes' => [
                'id' => 'معرف صفحة الميتا',
                'name' => 'الاسم',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.title' => 'العنوان',
                'locales.*.description' => 'الوصف',
                'locales.*.keywords' => 'الكلمات المفتاحية',
            ],
        ],
        'update_all' => [
            'messages' => [
                'pages.required' => 'الصفحات مطلوبة',
                'pages.array' => 'الصفحات يجب أن تكون مصفوفة',
                'pages.min' => 'يجب إضافة صفحة واحدة على الأقل',
                'pages.*.id.required' => 'معرف الصفحة مطلوب',
                'pages.*.id.exists' => 'الصفحة المحددة غير موجودة',
                'pages.*.translations.required' => 'ترجمات الصفحة مطلوبة',
                'pages.*.translations.array' => 'ترجمات الصفحة يجب أن تكون مصفوفة',
                'pages.*.translations.*.title.string' => 'عنوان الصفحة يجب أن يكون نص',
                'pages.*.translations.*.title.max' => 'عنوان الصفحة يجب أن يكون أقل من 255 حرف',
                'pages.*.translations.*.description.string' => 'وصف الصفحة يجب أن يكون نص',
                'pages.*.translations.*.keywords.string' => 'كلمات الصفحة المفتاحية يجب أن تكون نص',
                'pages.*.translations.*.keywords.max' => 'كلمات الصفحة المفتاحية يجب أن تكون أقل من 255 حرف',
            ],
            'attributes' => [
                'pages' => 'الصفحات',
                'pages.*.id' => 'معرف الصفحة',
                'pages.*.translations' => 'ترجمات الصفحة',
                'pages.*.translations.*.title' => 'عنوان الصفحة',
                'pages.*.translations.*.description' => 'وصف الصفحة',
                'pages.*.translations.*.keywords' => 'كلمات الصفحة المفتاحية',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'صفحة الميتا المحددة غير موجودة',
            ],
            'attributes' => [
                'id' => 'معرف صفحة الميتا',
            ],
        ],
    ],
];
