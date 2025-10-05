<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.string' => 'الاسم يجب أن يكون نص',
                'name.required' => 'الاسم مطلوب',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.job.string' => 'الوظيفة يجب أن تكون نص',
                'locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                'social_links.array' => 'الروابط الاجتماعية يجب أن تكون مصفوفة',
                'social_links.*.link.string' => 'الرابط يجب أن يكون نص',
                'social_links.*.link.required' => 'الرابط مطلوب',
            ],
            'attributes' => [
                'name' => 'الاسم',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.job' => 'الوظيفة',
                'locales.*.short_content' => 'المحتوى المختصر',
                'social_links' => 'الروابط الاجتماعية',
                'social_links.*.link' => 'الرابط',
            ],
        ],
        'update' => [
            'messages' => [
                'id.exists' => 'عضو الفريق المحدد غير موجود',
                'name.string' => 'الاسم يجب أن يكون نص',
                'name.required' => 'الاسم مطلوب',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.job.string' => 'الوظيفة يجب أن تكون نص',
                'locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                'social_links.array' => 'الروابط الاجتماعية يجب أن تكون مصفوفة',
                'social_links.*.link.string' => 'الرابط يجب أن يكون نص',
                'social_links.*.link.required' => 'الرابط مطلوب',
            ],
            'attributes' => [
                'id' => 'معرف عضو الفريق',
                'name' => 'الاسم',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.job' => 'الوظيفة',
                'locales.*.short_content' => 'المحتوى المختصر',
                'social_links' => 'الروابط الاجتماعية',
                'social_links.*.link' => 'الرابط',
            ],
        ],
        'update_all' => [
            'messages' => [
                '*.id.exists' => 'عضو الفريق المحدد غير موجود',
                '*.name.string' => 'الاسم يجب أن يكون نص',
                '*.name.required' => 'الاسم مطلوب',
                '*.locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                '*.locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                '*.locales.*.job.string' => 'الوظيفة يجب أن تكون نص',
                '*.locales.*.short_content.string' => 'المحتوى المختصر يجب أن يكون نص',
                '*.social_links.array' => 'الروابط الاجتماعية يجب أن تكون مصفوفة',
                '*.social_links.*.link.string' => 'الرابط يجب أن يكون نص',
                '*.social_links.*.link.required' => 'الرابط مطلوب',
            ],
            'attributes' => [
                '*.id' => 'معرف عضو الفريق',
                '*.name' => 'الاسم',
                '*.locales' => 'الترجمات',
                '*.locales.*.locale' => 'رمز اللغة',
                '*.locales.*.job' => 'الوظيفة',
                '*.locales.*.short_content' => 'المحتوى المختصر',
                '*.social_links' => 'الروابط الاجتماعية',
                '*.social_links.*.link' => 'الرابط',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'عضو الفريق المحدد غير موجود',
            ],
            'attributes' => [
                'id' => 'معرف عضو الفريق',
            ],
        ],
    ],
];
