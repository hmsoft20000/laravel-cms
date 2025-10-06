# دليل العلاقات المخصصة في CMS

هذا الدليل يوضح كيفية إضافة علاقات مخصصة على الـ Models الموجودة في حزمة CMS دون تعديل الملفات الأصلية.

## المميزات

✅ **إضافة علاقات جديدة** على أي Model في الحزمة  
✅ **عدم تعديل الملفات الأصلية** للحزمة  
✅ **دعم جميع أنواع العلاقات** (hasMany, belongsTo, hasOne, belongsToMany, morphMany, etc.)  
✅ **إضافة العلاقات ديناميكياً** باستخدام Commands  
✅ **تكوين مرن** من خلال ملفات الإعدادات  
✅ **Helper Functions** سهلة الاستخدام  

## الطرق المختلفة لإضافة العلاقات

### الطريقة الأولى: استخدام ملف الإعدادات (الأفضل)

1. **نشر ملف الإعدادات:**
```bash
php artisan vendor:publish --tag=cms-config
```

2. **تعديل ملف `config/cms_custom_relations.php`:**
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

### الطريقة الثانية: استخدام Artisan Command

```bash
php artisan cms:add-relation Category properties hasMany "App\Models\Property" --foreign-key=category_id
```

### الطريقة الثالثة: استخدام Helper Functions

```php
// في AppServiceProvider أو أي مكان مناسب
addCustomRelation(
    \HMsoft\Cms\Models\Shared\Category::class,
    'properties',
    [
        'type' => 'hasMany',
        'related' => \App\Models\Property::class,
        'foreign_key' => 'category_id',
        'local_key' => 'id',
    ]
);
```

## أنواع العلاقات المدعومة

### 1. hasMany (واحد إلى كثير)

```php
'products' => [
    'type' => 'hasMany',
    'related' => \App\Models\Product::class,
    'foreign_key' => 'category_id',
    'local_key' => 'id',
],
```

### 2. belongsTo (كثير إلى واحد)

```php
'category' => [
    'type' => 'belongsTo',
    'related' => \App\Models\Category::class,
    'foreign_key' => 'category_id',
    'owner_key' => 'id',
],
```

### 3. hasOne (واحد إلى واحد)

```php
'profile' => [
    'type' => 'hasOne',
    'related' => \App\Models\Profile::class,
    'foreign_key' => 'user_id',
    'local_key' => 'id',
],
```

### 4. belongsToMany (كثير إلى كثير)

```php
'tags' => [
    'type' => 'belongsToMany',
    'related' => \App\Models\Tag::class,
    'table' => 'category_tag',
    'foreign_key' => 'category_id',
    'related_foreign_key' => 'tag_id',
    'local_key' => 'id',
    'owner_key' => 'id',
    'pivot_columns' => ['created_at', 'updated_at'],
],
```

### 5. morphMany (Polymorphic - واحد إلى كثير)

```php
'reviews' => [
    'type' => 'morphMany',
    'related' => \App\Models\Review::class,
    'morph_name' => 'reviewable',
    'morph_type' => 'reviewable_type',
    'morph_id' => 'reviewable_id',
],
```

### 6. morphOne (Polymorphic - واحد إلى واحد)

```php
'image' => [
    'type' => 'morphOne',
    'related' => \App\Models\Image::class,
    'morph_name' => 'imageable',
    'morph_type' => 'imageable_type',
    'morph_id' => 'imageable_id',
],
```

### 7. morphTo (Polymorphic - عكسي)

```php
'commentable' => [
    'type' => 'morphTo',
    'morph_name' => 'commentable',
    'morph_type' => 'commentable_type',
    'morph_id' => 'commentable_id',
],
```

## أمثلة عملية

### مثال 1: إضافة علاقة properties على Category

```php
// في config/cms_custom_relations.php
\HMsoft\Cms\Models\Shared\Category::class => [
    'properties' => [
        'type' => 'hasMany',
        'related' => \App\Models\Property::class,
        'foreign_key' => 'category_id',
        'local_key' => 'id',
    ],
],

// الاستخدام
$category = Category::find(1);
$properties = $category->properties; // سيعمل الآن!
```

### مثال 2: إضافة علاقة comments على Post

```php
// في config/cms_custom_relations.php
\HMsoft\Cms\Models\Content\Post::class => [
    'comments' => [
        'type' => 'hasMany',
        'related' => \App\Models\Comment::class,
        'foreign_key' => 'post_id',
        'local_key' => 'id',
    ],
],

// الاستخدام
$post = Post::find(1);
$comments = $post->comments()->where('approved', true)->get();
```

### مثال 3: إضافة علاقة polymorphic reviews

```php
// في config/cms_custom_relations.php
\HMsoft\Cms\Models\Content\Post::class => [
    'reviews' => [
        'type' => 'morphMany',
        'related' => \App\Models\Review::class,
        'morph_name' => 'reviewable',
        'morph_type' => 'reviewable_type',
        'morph_id' => 'reviewable_id',
    ],
],

// الاستخدام
$post = Post::find(1);
$reviews = $post->reviews()->with('user')->get();
```

## Helper Functions

### addCustomRelation()
```php
addCustomRelation($modelClass, $relationName, $config);
```

### hasCustomRelation()
```php
if (hasCustomRelation(Category::class, 'properties')) {
    // العلاقة موجودة
}
```

### getCustomRelations()
```php
$relations = getCustomRelations(Category::class);
// ['properties' => [...], 'products' => [...]]
```

## Artisan Commands

### إضافة علاقة جديدة
```bash
php artisan cms:add-relation Category properties hasMany "App\Models\Property"
```

### خيارات إضافية
```bash
php artisan cms:add-relation Category tags belongsToMany "App\Models\Tag" \
    --table=category_tag \
    --foreign-key=category_id \
    --related-foreign-key=tag_id \
    --pivot-columns=created_at,updated_at
```

## Models المدعومة

- `\HMsoft\Cms\Models\Shared\Category`
- `\HMsoft\Cms\Models\Content\Post`
- `\HMsoft\Cms\Models\Sector\Sector`
- `\HMsoft\Cms\Models\Organizations\Organization`
- `\HMsoft\Cms\Models\Team\Team`
- `\HMsoft\Cms\Models\Statistics\Statistics`
- وأي Model آخر يرث من `GeneralModel`

## نصائح مهمة

1. **تأكد من وجود الجداول** قبل إضافة العلاقات
2. **استخدم Migration** لإنشاء الجداول المطلوبة
3. **اختبر العلاقات** بعد إضافتها
4. **احفظ نسخة احتياطية** من ملف الإعدادات
5. **استخدم IDE** للحصول على autocomplete أفضل

## استكشاف الأخطاء

### الخطأ: "Call to undefined method"
- تأكد من إضافة العلاقة في ملف الإعدادات
- تأكد من نشر ملف الإعدادات
- تأكد من مسح cache: `php artisan config:clear`

### العلاقة لا تعمل
- تأكد من صحة أسماء الأعمدة
- تأكد من وجود البيانات في الجداول
- تأكد من صحة أسماء الـ Models

## أمثلة متقدمة

### إضافة علاقة مع Eager Loading
```php
$categories = Category::with(['properties', 'products'])->get();
```

### إضافة علاقة مع Constraints
```php
$category = Category::find(1);
$activeProperties = $category->properties()->where('is_active', true)->get();
```

### إضافة علاقة مع Pivot Data
```php
$category = Category::find(1);
$tagsWithPivot = $category->tags()->withPivot('created_at')->get();
```

هذا النظام يوفر مرونة كاملة للمطورين لإضافة العلاقات التي يحتاجونها دون تعديل ملفات الحزمة الأصلية! 🎉
