<?php

return [
    'validation' => [
        'store' => [
            'messages' => [
                'name.string' => 'الاسم يجب أن يكون نص',
                'name.max' => 'الاسم يجب أن يكون أقل من 191 حرف',
                'email.email' => 'البريد الإلكتروني يجب أن يكون صحيح',
                'email.max' => 'البريد الإلكتروني يجب أن يكون أقل من 191 حرف',
                'mobile.string' => 'رقم الهاتف يجب أن يكون نص',
                'mobile.max' => 'رقم الهاتف يجب أن يكون أقل من 191 حرف',
                'residence.string' => 'مكان الإقامة يجب أن يكون نص',
                'residence.max' => 'مكان الإقامة يجب أن يكون أقل من 191 حرف',
                'nationality.string' => 'الجنسية يجب أن تكون نص',
                'nationality.max' => 'الجنسية يجب أن تكون أقل من 191 حرف',
                'description.string' => 'الوصف يجب أن يكون نص',
                'message.string' => 'الرسالة يجب أن تكون نص',
                'subject.string' => 'الموضوع يجب أن يكون نص',
                'subject.max' => 'الموضوع يجب أن يكون أقل من 191 حرف',
                'file-upload.array' => 'الملفات المرفقة يجب أن تكون مصفوفة',
                'file-upload.*.file' => 'الملف المرفق يجب أن يكون ملف',
                'file-upload.*.mimes' => 'نوع الملف غير مدعوم',
                'file-upload.*.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
            ],
            'attributes' => [
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'mobile' => 'رقم الهاتف',
                'residence' => 'مكان الإقامة',
                'nationality' => 'الجنسية',
                'description' => 'الوصف',
                'message' => 'الرسالة',
                'subject' => 'الموضوع',
                'file-upload' => 'الملفات المرفقة',
                'file-upload.*' => 'الملف المرفق',
            ],
        ],
        'update' => [
            'messages' => [
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة يجب أن تكون read أو unread',
                'is_starred.required' => 'حالة التمييز مطلوبة',
                'is_starred.boolean' => 'حالة التمييز يجب أن تكون true أو false',
            ],
            'attributes' => [
                'status' => 'الحالة',
                'is_starred' => 'مميز',
            ],
        ],
        'delete' => [
            'messages' => [
                'id.exists' => 'رسالة الاتصال المحددة غير موجودة',
            ],
            'attributes' => [
                'id' => 'معرف رسالة الاتصال',
            ],
        ],
        'reply' => [
            'messages' => [
                'reply_message.required' => 'رسالة الرد مطلوبة',
                'reply_message.string' => 'رسالة الرد يجب أن تكون نص',
                'reply_message.min' => 'رسالة الرد يجب أن تكون 10 أحرف على الأقل',
            ],
            'attributes' => [
                'reply_message' => 'رسالة الرد',
            ],
        ],
    ],
];
