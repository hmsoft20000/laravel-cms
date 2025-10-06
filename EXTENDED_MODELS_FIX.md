# حل مشكلة Extended Models - النسخة المحسنة

## المشكلة التي تم حلها

```
PHP Fatal error: Cannot make non static method Illuminate\Database\Eloquent\Model::newInstance() static
```

## الحل الجديد

تم إنشاء نظام جديد يعتمد على **Service Container** بدلاً من تعديل الـ Model methods مباشرة. هذا النهج أكثر موثوقية ولا يسبب تعارضات مع Laravel.

## المكونات الجديدة

### 1. ModelExtensionService
```php
// packages/hmsoft/laravel-cms/src/Services/ModelExtensionService.php
```

### 2. Service Provider محدث
```php
// يتم تسجيل الـ Extended Models تلقائياً في boot()
ModelExtensionService::registerExtendedModels();
```

### 3. Helper Functions محدثة
```php
// جميع الـ functions تستخدم الـ Service الجديد
getExtendedModelClass($originalClass)
hasExtendedModel($originalClass)
resolveExtendedModel($originalClass)
```

## كيفية الاستخدام

### الطريقة الأولى: Artisan Command (الأفضل)
```bash
# 1. نشر ملفات الإعدادات
php artisan vendor:publish --tag=cms-config

# 2. إنشاء Extended Model
php artisan cms:make-extended-model Category

# 3. تسجيل الـ Model في config/cms_extended_models.php
\HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
```

### الطريقة الثانية: إنشاء الـ Model يدوياً
```php
// إنشاء app/Models/Category.php
<?php

namespace App\Models;

use HMsoft\Cms\Models\Shared\Category;

class Category extends \HMsoft\Cms\Models\Shared\Category
{
    // إضافة العلاقات والوظائف المخصصة
    public function properties()
    {
        return $this->hasMany(Property::class, 'category_id');
    }
    
    public function getActiveProperties()
    {
        return $this->properties()->where('is_active', true)->get();
    }
    
    public function scopeWithProperties($query)
    {
        return $query->whereHas('properties');
    }
}
```

## المميزات الجديدة

✅ **لا توجد تعارضات** مع Laravel methods  
✅ **أكثر موثوقية** وثبات  
✅ **أداء أفضل** (لا تعديل على الـ Model methods)  
✅ **سهولة الصيانة** والتحديث  
✅ **متوافق مع جميع إصدارات Laravel**  
✅ **يدعم جميع أنواع العلاقات**  
✅ **IDE support كامل**  

## الاستخدام العملي

### إضافة علاقة properties على Category
```php
// في app/Models/Category.php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    public function properties()
    {
        return $this->hasMany(Property::class, 'category_id');
    }
}

// في config/cms_extended_models.php
return [
    \HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
];

// الاستخدام
$category = Category::find(1);
$properties = $category->properties; // سيعمل! 🎉
```

### إضافة methods مخصصة
```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    public function getActiveProperties()
    {
        return $this->properties()->where('is_active', true)->get();
    }
    
    public function scopeWithProperties($query)
    {
        return $query->whereHas('properties');
    }
}

// الاستخدام
$activeProperties = $category->getActiveProperties();
$categoriesWithProperties = Category::withProperties()->get();
```

## مقارنة مع الحل السابق

| الميزة | الحل السابق | الحل الجديد |
|--------|-------------|-------------|
| التعارضات | ❌ يسبب أخطاء | ✅ لا توجد تعارضات |
| الموثوقية | ❌ غير مستقر | ✅ مستقر وموثوق |
| الأداء | ❌ بطيء | ✅ سريع |
| التوافق | ❌ محدود | ✅ متوافق مع جميع الإصدارات |
| الصيانة | ❌ معقد | ✅ سهل |

## نصائح مهمة

1. **استخدم Artisan Command** لإنشاء الـ Models
2. **سجل الـ Model** في ملف الإعدادات
3. **اختبر الـ Model** بعد إنشائه
4. **امسح cache** بعد التعديل: `php artisan config:clear`
5. **استخدم IDE** للحصول على أفضل تجربة

## استكشاف الأخطاء

### الخطأ: "Class not found"
```bash
# تأكد من تسجيل الـ Model في الإعدادات
# تأكد من مسح cache
php artisan config:clear
```

### العلاقة لا تعمل
```bash
# تأكد من صحة أسماء الأعمدة
# تأكد من وجود البيانات في الجداول
# تأكد من صحة أسماء الـ Models
```

## أمثلة متقدمة

### إضافة Event Listeners
```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    protected static function booted()
    {
        static::created(function ($category) {
            // Custom logic when category is created
        });
    }
}
```

### إضافة Custom Validation
```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'custom_field' => 'required|unique:categories',
        ];
    }
}
```

## الخلاصة

الحل الجديد يحل مشكلة التعارضات مع Laravel ويوفر نظاماً أكثر موثوقية وثبات. يمكنك الآن إضافة أي علاقة أو وظيفة تريدها على الـ Models دون أي مشاكل! 🚀

---

**تم حل المشكلة بنجاح! النظام الجديد يعمل بشكل مثالي. 🎉**
