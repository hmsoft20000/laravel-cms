# مقارنة طرق تمديد Models في CMS

## الطريقتان المتاحتان

### 1. Custom Relations (الطريقة الأولى)
- إضافة علاقات فقط عبر ملف الإعدادات
- محدود في الوظائف
- مناسب للعلاقات البسيطة

### 2. Extended Models (الطريقة الجديدة - الأفضل)
- إنشاء Model كامل يرث من الـ Model الأصلي
- تحكم كامل في جميع الوظائف
- مشابه لطريقة Laravel

## مقارنة مفصلة

| الميزة | Custom Relations | Extended Models |
|--------|------------------|-----------------|
| **التحكم** | محدود | كامل |
| **إضافة العلاقات** | عبر config | مباشرة في الـ Model |
| **إضافة Methods** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Scopes** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Accessors** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Mutators** | ❌ غير ممكن | ✅ ممكن |
| **تجاوز Methods** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Events** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Validation** | ❌ غير ممكن | ✅ ممكن |
| **إضافة Casts** | ❌ غير ممكن | ✅ ممكن |
| **IDE Support** | محدود | كامل |
| **Type Safety** | محدود | كامل |
| **الصيانة** | معقدة | سهلة |
| **المرونة** | محدودة | كاملة |

## متى تستخدم كل طريقة؟

### استخدم Custom Relations عندما:
- تريد إضافة علاقات بسيطة فقط
- لا تحتاج لتعديل الـ Model نفسه
- تريد حل سريع ومؤقت

### استخدم Extended Models عندما:
- تريد تحكم كامل في الـ Model
- تريد إضافة وظائف معقدة
- تريد حل دائم ومهني
- تريد اتباع أفضل الممارسات

## مثال عملي

### المشكلة الأصلية
```
Call to undefined method HMsoft\Cms\Models\Shared\Category::properties()
```

### الحل باستخدام Custom Relations
```php
// في config/cms_custom_relations.php
'properties' => [
    'type' => 'hasMany',
    'related' => \App\Models\Property::class,
    'foreign_key' => 'category_id',
    'local_key' => 'id',
],

// الاستخدام
$category = Category::find(1);
$properties = $category->properties; // يعمل
```

### الحل باستخدام Extended Models (الأفضل)
```php
// 1. إنشاء الـ Model
php artisan cms:make-extended-model Category

// 2. إضافة العلاقة في app/Models/Category.php
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    public function properties()
    {
        return $this->hasMany(Property::class, 'category_id');
    }
    
    // يمكنك إضافة المزيد!
    public function getActiveProperties()
    {
        return $this->properties()->where('is_active', true)->get();
    }
    
    public function scopeWithProperties($query)
    {
        return $query->whereHas('properties');
    }
}

// 3. تسجيل الـ Model في config/cms_extended_models.php
\HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,

// 4. الاستخدام
$category = Category::find(1);
$properties = $category->properties; // يعمل
$activeProperties = $category->getActiveProperties(); // يعمل
$categoriesWithProperties = Category::withProperties()->get(); // يعمل
```

## التوصية

**استخدم Extended Models** - إنها الطريقة الأفضل والأكثر مرونة!

### المميزات:
- ✅ تحكم كامل في الـ Model
- ✅ إضافة أي وظيفة تريدها
- ✅ اتباع أفضل الممارسات
- ✅ مشابه لطريقة Laravel
- ✅ IDE support كامل
- ✅ Type safety كامل
- ✅ سهولة الصيانة

### كيفية البدء:
```bash
# 1. نشر ملفات الإعدادات
php artisan vendor:publish --tag=cms-config

# 2. إنشاء Extended Model
php artisan cms:make-extended-model Category

# 3. إضافة العلاقات والوظائف المطلوبة
# 4. تسجيل الـ Model في الإعدادات
# 5. الاستخدام!
```

**Extended Models هي الحل الأمثل لمشكلتك! 🚀**
