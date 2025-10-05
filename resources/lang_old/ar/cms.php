<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Core Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the core CMS functionality
    | and are not related to specific resources or frontend content.
    |
    */

    // General CMS Messages
    'cms' => [
        'title' => 'نظام إدارة المحتوى',
        'version' => 'الإصدار',
        'last_updated' => 'آخر تحديث',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'deleted_at' => 'تاريخ الحذف',
        'created_by' => 'أنشئ بواسطة',
        'updated_by' => 'حدث بواسطة',
        'deleted_by' => 'حذف بواسطة',
    ],

    // Status Messages
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'published' => 'منشور',
        'draft' => 'مسودة',
        'archived' => 'مؤرشف',
        'pending' => 'في الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
    ],

    // Common Actions
    'actions' => [
        'create' => 'إنشاء',
        'edit' => 'تعديل',
        'update' => 'تحديث',
        'delete' => 'حذف',
        'view' => 'عرض',
        'show' => 'إظهار',
        'hide' => 'إخفاء',
        'activate' => 'تفعيل',
        'deactivate' => 'إلغاء التفعيل',
        'publish' => 'نشر',
        'unpublish' => 'إلغاء النشر',
        'duplicate' => 'نسخ',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'refresh' => 'تحديث',
        'reset' => 'إعادة تعيين',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'sort' => 'ترتيب',
        'select_all' => 'تحديد الكل',
        'deselect_all' => 'إلغاء تحديد الكل',
    ],

    // Pagination
    'pagination' => [
        'previous' => 'السابق',
        'next' => 'التالي',
        'first' => 'الأول',
        'last' => 'الأخير',
        'showing' => 'عرض',
        'to' => 'إلى',
        'of' => 'من',
        'results' => 'نتيجة',
        'per_page' => 'لكل صفحة',
        'go_to_page' => 'انتقل إلى الصفحة',
        'page' => 'صفحة',
        'pages' => 'صفحات',
    ],

    // Validation Messages
    'validation' => [
        'required' => 'هذا الحقل مطلوب',
        'invalid' => 'القيمة غير صحيحة',
        'not_found' => 'العنصر غير موجود',
        'already_exists' => 'هذا العنصر موجود بالفعل',
        'cannot_delete' => 'لا يمكن حذف هذا العنصر',
        'permission_denied' => 'ليس لديك صلاحية للقيام بهذا الإجراء',
        'unauthorized' => 'غير مصرح لك بالوصول',
        'forbidden' => 'ممنوع الوصول',
    ],

    // File Management
    'files' => [
        'upload' => 'رفع ملف',
        'upload_success' => 'تم رفع الملف بنجاح',
        'upload_failed' => 'فشل في رفع الملف',
        'delete_success' => 'تم حذف الملف بنجاح',
        'delete_failed' => 'فشل في حذف الملف',
        'file_too_large' => 'حجم الملف كبير جداً',
        'invalid_file_type' => 'نوع الملف غير مدعوم',
        'file_not_found' => 'الملف غير موجود',
    ],

    // Media Management
    'media' => [
        'image' => 'صورة',
        'video' => 'فيديو',
        'audio' => 'صوت',
        'document' => 'مستند',
        'archive' => 'أرشيف',
        'other' => 'أخرى',
        'set_as_default' => 'تعيين كافتراضي',
        'remove_default' => 'إلغاء الافتراضي',
        'sort_order' => 'ترتيب',
    ],

    // Categories and Tags
    'taxonomy' => [
        'category' => 'فئة',
        'categories' => 'فئات',
        'tag' => 'وسم',
        'tags' => 'وسوم',
        'keyword' => 'كلمة مفتاحية',
        'keywords' => 'كلمات مفتاحية',
        'feature' => 'ميزة',
        'features' => 'ميزات',
        'attribute' => 'خاصية',
        'attributes' => 'خصائص',
    ],

    // Organizations
    'organizations' => [
        'organization' => 'منظمة',
        'organizations' => 'منظمات',
        'partner' => 'شريك',
        'partners' => 'شركاء',
        'sponsor' => 'راعي',
        'sponsors' => 'رعاة',
        'role' => 'دور',
        'roles' => 'أدوار',
    ],

    // Permissions and Roles
    'permissions' => [
        'permission' => 'صلاحية',
        'permissions' => 'صلاحيات',
        'role' => 'دور',
        'roles' => 'أدوار',
        'assign' => 'تعيين',
        'revoke' => 'سحب',
        'grant' => 'منح',
        'deny' => 'رفض',
    ],

    // Statistics
    'statistics' => [
        'statistic' => 'إحصائية',
        'statistics' => 'إحصائيات',
        'total' => 'المجموع',
        'count' => 'العدد',
        'percentage' => 'النسبة المئوية',
        'growth' => 'النمو',
        'decline' => 'الانخفاض',
    ],

    // Settings
    'settings' => [
        'setting' => 'إعداد',
        'settings' => 'إعدادات',
        'general' => 'عام',
        'advanced' => 'متقدم',
        'security' => 'الأمان',
        'performance' => 'الأداء',
        'maintenance' => 'الصيانة',
        'backup' => 'نسخ احتياطي',
        'restore' => 'استعادة',
    ],

    // Notifications
    'notifications' => [
        'success' => 'تم بنجاح',
        'error' => 'حدث خطأ',
        'warning' => 'تحذير',
        'info' => 'معلومات',
        'loading' => 'جاري التحميل...',
        'saving' => 'جاري الحفظ...',
        'processing' => 'جاري المعالجة...',
    ],

    // Time and Date
    'time' => [
        'now' => 'الآن',
        'today' => 'اليوم',
        'yesterday' => 'أمس',
        'tomorrow' => 'غداً',
        'this_week' => 'هذا الأسبوع',
        'last_week' => 'الأسبوع الماضي',
        'next_week' => 'الأسبوع القادم',
        'this_month' => 'هذا الشهر',
        'last_month' => 'الشهر الماضي',
        'next_month' => 'الشهر القادم',
        'this_year' => 'هذا العام',
        'last_year' => 'العام الماضي',
        'next_year' => 'العام القادم',
    ],
];



