# إضافة علاقات مخصصة على Models الحزمة

## المشكلة
```
Call to undefined method HMsoft\Cms\Models\Shared\Category::properties()
```

## الحل
يمكنك الآن إضافة أي علاقة تريدها على أي Model في الحزمة دون تعديل الملفات الأصلية!

## الاستخدام السريع

### 1. نشر ملف الإعدادات
```bash
php artisan vendor:publish --tag=cms-config
```

### 2. إضافة العلاقة في `config/cms_custom_relations.php`
```php
return [
    \HMsoft\Cms\Models\Shared\Category::class => [
        'properties' => [
            'type' => 'hasMany',
            'related' => \App\Models\Property::class,
            'foreign_key' => 'category_id',
            'local_key' => 'id',
        ],
    ],
];
```

### 3. الاستخدام
```php
$category = Category::find(1);
$properties = $category->properties; // سيعمل الآن! 🎉
```

## أوامر Artisan

### إضافة علاقة جديدة
```bash
php artisan cms:add-relation Category properties hasMany "App\Models\Property"
```

### إضافة علاقة many-to-many
```bash
php artisan cms:add-relation Category tags belongsToMany "App\Models\Tag" \
    --table=category_tag \
    --foreign-key=category_id \
    --related-foreign-key=tag_id
```

## أنواع العلاقات المدعومة

- ✅ `hasMany` - واحد إلى كثير
- ✅ `belongsTo` - كثير إلى واحد  
- ✅ `hasOne` - واحد إلى واحد
- ✅ `belongsToMany` - كثير إلى كثير
- ✅ `morphMany` - Polymorphic واحد إلى كثير
- ✅ `morphOne` - Polymorphic واحد إلى واحد
- ✅ `morphTo` - Polymorphic عكسي

## Models المدعومة

- `Category` - التصنيفات
- `Post` - المنشورات
- `Sector` - القطاعات
- `Organization` - المنظمات
- `Team` - الفريق
- `Statistics` - الإحصائيات
- وأي Model آخر في الحزمة

## أمثلة عملية

### إضافة علاقة properties على Category
```php
// في config/cms_custom_relations.php
'properties' => [
    'type' => 'hasMany',
    'related' => \App\Models\Property::class,
    'foreign_key' => 'category_id',
    'local_key' => 'id',
],

// الاستخدام
$category = Category::with('properties')->find(1);
$properties = $category->properties;
```

### إضافة علاقة polymorphic reviews
```php
// في config/cms_custom_relations.php
'reviews' => [
    'type' => 'morphMany',
    'related' => \App\Models\Review::class,
    'morph_name' => 'reviewable',
    'morph_type' => 'reviewable_type',
    'morph_id' => 'reviewable_id',
],

// الاستخدام
$post = Post::find(1);
$reviews = $post->reviews()->with('user')->get();
```

## Helper Functions

```php
// إضافة علاقة برمجياً
addCustomRelation(Category::class, 'properties', $config);

// التحقق من وجود علاقة
if (hasCustomRelation(Category::class, 'properties')) {
    // العلاقة موجودة
}

// الحصول على جميع العلاقات المخصصة
$relations = getCustomRelations(Category::class);
```

## نصائح مهمة

1. **تأكد من وجود الجداول** قبل إضافة العلاقات
2. **استخدم Migration** لإنشاء الجداول المطلوبة
3. **اختبر العلاقات** بعد إضافتها
4. **امسح cache** بعد التعديل: `php artisan config:clear`

## استكشاف الأخطاء

### الخطأ: "Call to undefined method"
- تأكد من إضافة العلاقة في ملف الإعدادات
- تأكد من نشر ملف الإعدادات
- تأكد من مسح cache

### العلاقة لا تعمل
- تأكد من صحة أسماء الأعمدة
- تأكد من وجود البيانات في الجداول
- تأكد من صحة أسماء الـ Models

---

**الآن يمكنك إضافة أي علاقة تريدها على أي Model في الحزمة! 🚀**
