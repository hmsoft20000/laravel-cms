<?php

namespace HMsoft\Cms\Traits\Blogs;

use HMsoft\Cms\Models\Content\Blog;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait HasNestedBlogs
 * Add this trait to any model that can have nested blogs.
 * It provides the 'blogs()' relationship method.
 */
trait HasNestedBlogs
{
    /**
     * Get all of the model's blogs.
     */
    public function blogs(): MorphToMany
    {
        return $this->morphToMany(
            Blog::class,
            'bloggable',      // اسم العلاقة Morph
            'bloggables',     // اسم الجدول الوسيط
            'bloggable_id',   // FK للموديل الحالي (Item, Service)
            'blog_id'         // FK للمودونة
        )->withPivot(['sort_number', 'is_active'])
            ->orderByPivot('sort_number', 'asc');
    }
}
