<?php

namespace HMsoft\Cms\Traits\Features;

use HMsoft\Cms\Models\Shared\Feature;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFeatures
{
    /**
     * Get all of the model's features (Polymorphic).
     */
    public function features(): MorphMany
    {
        return $this->morphMany(Feature::class, 'owner')->orderBy('sort_number');
    }
}
