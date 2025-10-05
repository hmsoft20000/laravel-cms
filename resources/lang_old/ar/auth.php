<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    // General Messages
    'failed' => 'بيانات الاعتماد هذه لا تتطابق مع سجلاتنا.',
    'password' => 'كلمة المرور المقدمة غير صحيحة.',
    'throttle' => 'عدد كبير جدًا من محاولات تسجيل الدخول. يرجى المحاولة مرة أخرى بعد :seconds ثانية.',

    // Authentication Messages
    'unauthenticated' => 'يجب تسجيل الدخول',
    'logged_in_successfully' => 'تم تسجيل الدخول بنجاح',
    'login_failed' => 'فشل تسجيل الدخول. يرجى المحاولة مرة أخرى.',
    'registered' => 'تم إنشاء الحساب بنجاح',
    'credentials_do_not_match' => 'بيانات الاعتماد المقدمة لا تتطابق مع سجلاتنا.',
    'registration_successful_logged_in' => 'تم إنشاء الحساب وتسجيل الدخول بنجاح',
    'logged_out_successfully' => 'تم تسجيل الخروج بنجاح',
    'old_password_not_match' => 'كلمة المرور القديمة غير متطابقة',
    'password_reset_code_sent' => 'تم إرسال رمز إعادة تعيين كلمة المرور بنجاح',
    'password_reset_successful' => 'تم إعادة تعيين كلمة المرور بنجاح',
    'password_reset_failed' => 'فشل في إعادة تعيين كلمة المرور',
    'registration_failed' => 'فشل تسجيل المستخدم.',
    'logout_failed' => 'فشل تسجيل الخروج.',
    'user_retrieved' => 'تم استرجاع المستخدم بنجاح.',
    'user_retrieval_failed' => 'فشل في استرجاع المستخدم.',
    'profile_retrieved' => 'تم استرجاع الملف الشخصي بنجاح.',
    'profile_retrieval_failed' => 'فشل في استرجاع الملف الشخصي.',
    'profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
    'profile_update_failed' => 'فشل في تحديث الملف الشخصي.',

    // Email Messages
    'password_reset_code_subject' => 'رمز إعادة تعيين كلمة المرور',
    'password_reset_code_greeting' => 'مرحبًا،',
    'password_reset_code_message' => 'رمز إعادة تعيين كلمة المرور الخاص بك هو',
    'password_reset_code_footer' => 'إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.',

    // OTP Messages
    'otp_verified_successfully' => 'تم التحقق من رمز التحقق بنجاح.',
    'otp_verification_failed' => 'فشل في التحقق من رمز التحقق.',
    'otp_invalid_or_expired' => 'رمز التحقق غير صالح أو منتهي الصلاحية.',
    'too_many_wrong_otp_attempts' => 'عدد كبير جدًا من محاولات رمز التحقق الخاطئة. يرجى المحاولة مرة أخرى لاحقًا.',
    'too_many_otp_requests_email' => 'عدد كبير جدًا من طلبات رمز التحقق من هذا البريد الإلكتروني. يرجى المحاولة مرة أخرى لاحقًا.',
    'too_many_otp_requests_ip' => 'عدد كبير جدًا من طلبات رمز التحقق من عنوان IP هذا. يرجى المحاولة مرة أخرى لاحقًا.',

    // Login Request
    'login' => [
        'messages' => [
            'login_identifier.required' => 'حقل البريد الإلكتروني/الجوال مطلوب.',
            'login_identifier.string' => 'يجب أن يكون البريد الإلكتروني/الجوال نصًا.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string' => 'يجب أن تكون كلمة المرور نصًا.',
        ],
        'attributes' => [
            'login_identifier' => 'البريد الإلكتروني/الجوال',
            'password' => 'كلمة المرور',
        ],
    ],

    // Register Request
    'register' => [
        'messages' => [
            'first_name.required' => 'حقل الاسم الأول مطلوب.',
            'first_name.string' => 'يجب أن يكون الاسم الأول نصًا.',
            'first_name.max' => 'لا يجوز أن يكون الاسم الأول أطول من 255 حرفًا.',
            'first_name.regex' => 'يجب أن يحتوي الاسم الأول على أحرف فقط.',
            'last_name.required' => 'حقل الاسم الأخير مطلوب.',
            'last_name.string' => 'يجب أن يكون الاسم الأخير نصًا.',
            'last_name.max' => 'لا يجوز أن يكون الاسم الأخير أطول من 255 حرفًا.',
            'last_name.regex' => 'يجب أن يحتوي الاسم الأخير على أحرف فقط.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
            'email.email' => 'يجب أن يكون البريد الإلكتروني عنوان بريد إلكتروني صالحًا.',
            'email.max' => 'لا يجوز أن يكون البريد الإلكتروني أطول من 255 حرفًا.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'mobile.required' => 'حقل الجوال مطلوب.',
            'mobile.string' => 'يجب أن يكون الجوال نصًا.',
            'mobile.unique' => 'رقم الجوال مستخدم بالفعل.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.uncompromised' => 'كلمة المرور هذه ظهرت في تسريب بيانات سابق. يرجى اختيار كلمة مرور أخرى.',
            'password_confirmation.required' => 'حقل تأكيد كلمة المرور مطلوب.',
            'password_confirmation.same' => 'تأكيد كلمة المرور غير متطابق.',
            'agreeTerms.accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ],
        'attributes' => [
            'first_name' => 'الاسم الأول',
            'last_name' => 'الاسم الأخير',
            'email' => 'البريد الإلكتروني',
            'mobile' => 'الجوال',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
            'agreeTerms' => 'الشروط والأحكام',
        ],
    ],

    // Forgot Password Request
    'forgot_password' => [
        'messages' => [
            'login_identifier.required' => 'حقل البريد الإلكتروني/الجوال مطلوب.',
            'login_identifier.string' => 'يجب أن يكون البريد الإلكتروني/الجوال نصًا.',
        ],
        'attributes' => [
            'login_identifier' => 'البريد الإلكتروني/الجوال',
        ],
    ],

    // Reset Password Request
    'reset_password' => [
        'messages' => [
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.string' => 'يجب أن يكون البريد الإلكتروني نصًا.',
            'token.required' => 'حقل الرمز المميز مطلوب.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.uncompromised' => 'كلمة المرور هذه ظهرت في تسريب بيانات سابق. يرجى اختيار كلمة مرور أخرى.',
            'password_confirmation.required' => 'حقل تأكيد كلمة المرور مطلوب.',
            'password_confirmation.same' => 'تأكيد كلمة المرور غير متطابق.',
        ],
        'attributes' => [
            'email' => 'البريد الإلكتروني',
            'token' => 'الرمز المميز',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
        ],
    ],

    // Update Password Request
    'update_password' => [
        'messages' => [
            'old_password.required' => 'حقل كلمة المرور القديمة مطلوب.',
            'old_password.string' => 'يجب أن تكون كلمة المرور القديمة نصًا.',
            'new_password.required' => 'حقل كلمة المرور الجديدة مطلوب.',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'new_password.different' => 'يجب أن تكون كلمة المرور الجديدة مختلفة عن القديمة.',
            'new_password.min' => 'يجب أن تحتوي كلمة المرور على الأقل على 8 أحرف.',
            'new_password.regex' => 'يجب أن تحتوي كلمة المرور على أحرف كبيرة وصغيرة وأرقام ورموز.',
        ],
        'attributes' => [
            'old_password' => 'كلمة المرور القديمة',
            'new_password' => 'كلمة المرور الجديدة',
        ],
    ],

    // Update Profile Request
    'update_profile' => [
        'messages' => [
            'first_name.string' => 'يجب أن يكون الاسم الأول نصًا.',
            'first_name.max' => 'لا يجوز أن يكون الاسم الأول أطول من 255 حرفًا.',
            'first_name.regex' => 'يجب أن يحتوي الاسم الأول على أحرف فقط.',
            'last_name.string' => 'يجب أن يكون الاسم الأخير نصًا.',
            'last_name.max' => 'لا يجوز أن يكون الاسم الأخير أطول من 255 حرفًا.',
            'last_name.regex' => 'يجب أن يحتوي الاسم الأخير على أحرف فقط.',
            'mobile.unique' => 'رقم الجوال مستخدم بالفعل.',
            'mobile.phone' => 'رقم الجوال غير صالح.',
            'image.image' => 'يجب أن يكون الملف صورة.',
            'image.mimes' => 'يجب أن يكون الملف من نوع: jpeg, png, jpg.',
            'image.max' => 'يجب ألا يتجاوز حجم الملف 2048 كيلوبايت.',
            'password_confirmation.same' => 'تأكيد كلمة المرور غير متطابق.',
        ],
        'attributes' => [
            'first_name' => 'الاسم الأول',
            'last_name' => 'الاسم الأخير',
            'mobile' => 'الجوال',
            'image' => 'الصورة',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
        ],
    ],

    // Verify OTP Request
    'verify_otp' => [
        'messages' => [
            'otp.required' => 'حقل رمز التحقق مطلوب.',
            'otp.string' => 'يجب أن يكون رمز التحقق نصًا.',
            'login_identifier.required' => 'حقل البريد الإلكتروني مطلوب.',
            'login_identifier.email' => 'يجب أن يكون البريد الإلكتروني عنوان بريد إلكتروني صالحًا.',
            'login_identifier.exists' => 'البريد الإلكتروني غير موجود.',
        ],
        'attributes' => [
            'otp' => 'رمز التحقق',
            'login_identifier' => 'البريد الإلكتروني',
        ],
    ],
];