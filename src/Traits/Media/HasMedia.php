<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Models\Shared\Medium;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    /**
     * Get all of the model's media files (Polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Medium::class, 'owner');
    }
}
