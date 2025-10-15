<?php

namespace HMsoft\Cms\Traits\Blogs;

use HMsoft\Cms\Models\Content\Blog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    public function blogs(): MorphMany
    {
        return $this->morphMany(Blog::class, 'owner');
    }
}
