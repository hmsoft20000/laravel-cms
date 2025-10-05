<?php

namespace HMsoft\Cms\Traits\Blogs;

use HMsoft\Cms\Models\Content\Post;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBlogs
{

    /**
     * Get all of the model's blogs (Polymorphic).
     */
    public function blogs(): MorphMany
    {
        return $this->morphMany(Post::class, 'owner')->where('type', 'blog');
    }
}
