<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'owner_id.required' => 'معرف المالك مطلوب',
                'owner_id.integer' => 'معرف المالك يجب أن يكون رقم صحيح',
                'owner_id.exists' => 'المالك المحدد غير موجود',
                'owner_type.required' => 'نوع المالك مطلوب',
                'owner_type.string' => 'نوع المالك يجب أن يكون نص',
                'owner_type.in' => 'نوع المالك يجب أن يكون post',
                'is_active.boolean' => 'حالة التفعيل يجب أن تكون true أو false',
                'sort_number.required' => 'رقم الترتيب مطلوب',
                'sort_number.integer' => 'رقم الترتيب يجب أن يكون رقم صحيح',
                'locales.required' => 'الترجمات مطلوبة',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.min' => 'يجب إضافة ترجمة واحدة على الأقل',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.question.string' => 'السؤال يجب أن يكون نص',
                'locales.*.question.max' => 'السؤال يجب أن يكون أقل من 255 حرف',
                'locales.*.answer.string' => 'الإجابة يجب أن تكون نص',
            ],
            'attributes' => [
                'owner_id' => 'معرف المالك',
                'owner_type' => 'نوع المالك',
                'is_active' => 'حالة التفعيل',
                'sort_number' => 'رقم الترتيب',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.question' => 'السؤال',
                'locales.*.answer' => 'الإجابة',
            ],
        ],
        'update' => [
            'messages' => [
                'is_active.boolean' => 'حالة التفعيل يجب أن تكون true أو false',
                'sort_number.integer' => 'رقم الترتيب يجب أن يكون رقم صحيح',
                'locales.array' => 'الترجمات يجب أن تكون مصفوفة',
                'locales.min' => 'يجب إضافة ترجمة واحدة على الأقل',
                'locales.*.locale.required' => 'رمز اللغة مطلوب',
                'locales.*.locale.string' => 'رمز اللغة يجب أن يكون نص',
                'locales.*.question.string' => 'السؤال يجب أن يكون نص',
                'locales.*.question.max' => 'السؤال يجب أن يكون أقل من 255 حرف',
                'locales.*.answer.string' => 'الإجابة يجب أن تكون نص',
            ],
            'attributes' => [
                'is_active' => 'حالة التفعيل',
                'sort_number' => 'رقم الترتيب',
                'locales' => 'الترجمات',
                'locales.*.locale' => 'رمز اللغة',
                'locales.*.question' => 'السؤال',
                'locales.*.answer' => 'الإجابة',
            ],
        ],
    ],
];
