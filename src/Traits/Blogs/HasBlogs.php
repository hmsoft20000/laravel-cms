<?php

namespace HMsoft\Cms\Traits\Blogs;

use HMsoft\Cms\Models\Content\Blog;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasBlogs
{
    /**
     * تعريف العلاقة: الموديل يمتلك العديد من المدونات.
     */
    public function blogs(): MorphToMany
    {
        return $this->morphToMany(
            Blog::class,
            'bloggable',      // اسم العلاقة (سيطابق الجدول bloggables)
            'bloggables',     // اسم الجدول الصريح
            'bloggable_id',   // المفتاح الأجنبي للموديل الحالي
            'blog_id'         // المفتاح الأجنبي للمدونة
        )->withPivot(['sort_number', 'is_active'])
            ->orderByPivot('sort_number', 'asc');
    }

    /**
     * دالة مساعدة لمزامنة المدونات بسهولة.
     * يمكن استخدامها داخل الـ Repository.
     * * @param array|null $blogIds مصفوفة من الـ IDs
     */
    public function syncBlogs(?array $blogIds): void
    {
        if (is_array($blogIds)) {
            $this->blogs()->sync($blogIds);
        }
    }
}
