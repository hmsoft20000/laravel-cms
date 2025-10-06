# دليل Extended Models في CMS

هذا الدليل يوضح كيفية إنشاء Extended Models التي ترث من Models الحزمة، مشابه لطريقة Laravel في تمديد الـ Models.

## المميزات

✅ **تحكم كامل** في الـ Model  
✅ **إضافة علاقات مخصصة** بسهولة  
✅ **إضافة methods و scopes** مخصصة  
✅ **إضافة accessors و mutators** مخصصة  
✅ **تجاوز الـ methods** الموجودة إذا لزم الأمر  
✅ **الحفاظ على وظائف الحزمة** الأصلية  
✅ **عدم تعديل ملفات الحزمة**  
✅ **دعم IDE** و autocomplete  
✅ **Type safety** و validation  
✅ **سهولة الصيانة** والتحديث  

## الطريقة الأولى: استخدام Artisan Command (الأفضل)

### 1. إنشاء Extended Model
```bash
php artisan cms:make-extended-model Category
```

هذا سينشئ ملف `app/Models/Category.php` مع المحتوى التالي:

```php
<?php

namespace App\Models;

use HMsoft\Cms\Models\Shared\Category;

class Category extends \HMsoft\Cms\Models\Shared\Category
{
    // يمكنك إضافة العلاقات والـ methods المخصصة هنا
}
```

### 2. تسجيل الـ Extended Model
```bash
# في config/cms_extended_models.php
return [
    \HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
];
```

### 3. الاستخدام
```php
// الآن يمكنك استخدام الـ Extended Model!
$category = Category::find(1);
$properties = $category->properties; // سيعمل!
```

## الطريقة الثانية: إنشاء الـ Model يدوياً

### 1. إنشاء الملف
```bash
# إنشاء app/Models/Category.php
```

### 2. كتابة المحتوى
```php
<?php

namespace App\Models;

use HMsoft\Cms\Models\Shared\Category;

class Category extends \HMsoft\Cms\Models\Shared\Category
{
    // إضافة العلاقات والـ methods المخصصة
}
```

### 3. تسجيل الـ Model
```php
// في config/cms_extended_models.php
return [
    \HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
];
```

## أمثلة عملية

### مثال 1: إضافة علاقة properties على Category

```php
<?php

namespace App\Models;

use HMsoft\Cms\Models\Shared\Category;

class Category extends \HMsoft\Cms\Models\Shared\Category
{
    /**
     * Add properties relationship
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'category_id');
    }
    
    /**
     * Add tags relationship
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'category_tag', 'category_id', 'tag_id');
    }
}
```

### مثال 2: إضافة methods مخصصة

```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    /**
     * Get active properties
     */
    public function getActiveProperties()
    {
        return $this->properties()->where('is_active', true)->get();
    }
    
    /**
     * Get category with all relationships
     */
    public function getFullCategory()
    {
        return $this->with(['properties', 'tags', 'products'])->first();
    }
}
```

### مثال 3: إضافة scopes مخصصة

```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    /**
     * Scope for categories with properties
     */
    public function scopeWithProperties($query)
    {
        return $query->whereHas('properties');
    }
    
    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### مثال 4: إضافة accessors و mutators

```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    /**
     * Custom accessor for full name
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->id . ')';
    }
    
    /**
     * Custom mutator for name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
```

## أنواع العلاقات المدعومة

### hasMany (واحد إلى كثير)
```php
public function properties()
{
    return $this->hasMany(Property::class, 'category_id');
}
```

### belongsTo (كثير إلى واحد)
```php
public function parentCategory()
{
    return $this->belongsTo(Category::class, 'parent_id');
}
```

### hasOne (واحد إلى واحد)
```php
public function settings()
{
    return $this->hasOne(CategorySettings::class, 'category_id');
}
```

### belongsToMany (كثير إلى كثير)
```php
public function tags()
{
    return $this->belongsToMany(Tag::class, 'category_tag', 'category_id', 'tag_id');
}
```

### morphMany (Polymorphic)
```php
public function reviews()
{
    return $this->morphMany(Review::class, 'reviewable');
}
```

## Artisan Commands

### إنشاء Extended Model
```bash
php artisan cms:make-extended-model Category
```

### خيارات إضافية
```bash
php artisan cms:make-extended-model Category --name=CustomCategory --namespace=App\\Custom\\Models
```

## Models المدعومة

- `Category` - التصنيفات
- `Post` - المنشورات  
- `Sector` - القطاعات
- `Organization` - المنظمات
- `Team` - الفريق
- `Statistics` - الإحصائيات
- وأي Model آخر في الحزمة

## الاستخدام المتقدم

### استخدام العلاقات مع Eager Loading
```php
$categories = Category::with(['properties', 'tags', 'products'])->get();
```

### استخدام الـ Scopes المخصصة
```php
$activeCategories = Category::active()->withProperties()->get();
```

### استخدام الـ Methods المخصصة
```php
$category = Category::find(1);
$activeProperties = $category->getActiveProperties();
$fullCategory = $category->getFullCategory();
```

### استخدام الـ Accessors المخصصة
```php
$category = Category::find(1);
$fullName = $category->full_name; // Custom accessor
```

## مقارنة مع الطريقة السابقة

| الميزة | الطريقة السابقة | Extended Models |
|--------|------------------|-----------------|
| التحكم | محدود | كامل |
| إضافة العلاقات | عبر config | مباشرة في الـ Model |
| إضافة Methods | غير ممكن | ممكن |
| إضافة Scopes | غير ممكن | ممكن |
| إضافة Accessors | غير ممكن | ممكن |
| تجاوز Methods | غير ممكن | ممكن |
| IDE Support | محدود | كامل |
| Type Safety | محدود | كامل |
| الصيانة | معقدة | سهلة |

## نصائح مهمة

1. **استخدم Artisan Command** لإنشاء الـ Models
2. **سجل الـ Model** في ملف الإعدادات
3. **اختبر الـ Model** بعد إنشائه
4. **احفظ نسخة احتياطية** من ملفاتك
5. **استخدم IDE** للحصول على أفضل تجربة

## استكشاف الأخطاء

### الخطأ: "Class not found"
- تأكد من تسجيل الـ Model في ملف الإعدادات
- تأكد من مسح cache: `php artisan config:clear`

### العلاقة لا تعمل
- تأكد من صحة أسماء الأعمدة
- تأكد من وجود البيانات في الجداول
- تأكد من صحة أسماء الـ Models

### الـ Method لا يعمل
- تأكد من كتابة الـ Method بشكل صحيح
- تأكد من تسجيل الـ Model في الإعدادات
- تأكد من مسح cache

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
        
        static::updated(function ($category) {
            // Custom logic when category is updated
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
            'description' => 'nullable|string',
            'custom_field' => 'required|unique:categories',
        ];
    }
}
```

### إضافة Custom Casts
```php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    protected $casts = [
        'custom_data' => 'array',
        'is_featured' => 'boolean',
        'featured_at' => 'datetime',
    ];
}
```

هذا النظام يوفر مرونة كاملة للمطورين لإضافة أي وظيفة يريدونها على الـ Models! 🚀
