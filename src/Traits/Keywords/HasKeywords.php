<?php

namespace HMsoft\Cms\Traits\Keywords;

use HMsoft\Cms\Models\Shared\Keyword;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasKeywords
{
    /**
     * Get all of the model's keywords (Polymorphic).
     */
    public function keywords(): MorphMany
    {
        return $this->morphMany(Keyword::class, 'owner');
    }
}
