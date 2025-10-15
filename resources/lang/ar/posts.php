<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'type.required' => 'حقل النوع مطلوب.',
                'show_in_footer.boolean' => 'يجب أن يكون حقل الإظهار في التذييل صحيحًا أو خطأ.',
                'show_in_header.boolean' => 'يجب أن يكون حقل الإظهار في الرأسية صحيحًا أو خطأ.',
                'is_active.boolean' => 'يجب أن يكون حقل نشط صحيحًا أو خطأ.',
            ],
            'attributes' => [
                'type' => 'النوع',
                'show_in_footer' => 'إظهار في التذييل',
                'show_in_header' => 'إظهار في الرأسية',
                'is_active' => 'نشط',
                'category_ids' => 'معرفات الفئة',
                'partner_ids' => 'معرفات الشريك',
                'sponsor_ids' => 'معرفات الراعي',
                'features' => 'الميزات',
                'downloads' => 'التنزيلات',
                'attributes' => 'السمات',
            ],
        ],
        'update' => [
            'messages' => [
                'show_in_footer.boolean' => 'يجب أن يكون حقل الإظهار في التذييل صحيحًا أو خطأ.',
                'show_in_header.boolean' => 'يجب أن يكون حقل الإظهار في الرأسية صحيحًا أو خطأ.',
                'is_active.boolean' => 'يجب أن يكون حقل نشط صحيحًا أو خطأ.',
            ],
            'attributes' => [
                'type' => 'النوع',
                'show_in_footer' => 'إظهار في التذييل',
                'show_in_header' => 'إظهار في الرأسية',
                'is_active' => 'نشط',
                'category_ids' => 'معرفات الفئة',
                'partner_ids' => 'معرفات الشريك',
                'sponsor_ids' => 'معرفات الراعي',
                'features' => 'الميزات',
                'downloads' => 'التنزيلات',
                'attributes' => 'السمات',
            ],
        ],
        'updateAll' => [
            'messages' => [
                '*.id.required' => 'المعرف مطلوب.',
                '*.id.integer' => 'يجب أن يكون المعرف عددًا صحيحًا.',
                '*.show_in_footer.boolean' => 'يجب أن يكون حقل الإظهار في التذييل صحيحًا أو خطأ.',
                '*.show_in_header.boolean' => 'يجب أن يكون حقل الإظهار في الرأسية صحيحًا أو خطأ.',
                '*.is_active.boolean' => 'يجب أن يكون حقل نشط صحيحًا أو خطأ.',
            ],
            'attributes' => [
                '*.id' => 'المعرف',
                '*.show_in_footer' => 'إظهار في التذييل',
                '*.show_in_header' => 'إظهار في الرأسية',
                '*.is_active' => 'نشط',
                '*.category_ids' => 'معرفات الفئة',
                '*.partner_ids' => 'معرفات الشريك',
                '*.sponsor_ids' => 'معرفات الراعي',
                '*.features' => 'الميزات',
                '*.downloads' => 'التنزيلات',
                '*.attributes' => 'السمات',
            ],
        ],
    ],
];
