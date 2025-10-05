<?php

namespace HMsoft\Cms\Traits\Services;

use HMsoft\Cms\Models\Content\Post;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasServices
{

    /**
     * Get all of the model's services (Polymorphic).
     */
    public function services(): MorphMany
    {
        return $this->morphMany(Post::class, 'owner')->where('type', 'service');
    }
}
