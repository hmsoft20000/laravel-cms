<?php

namespace HMsoft\Cms\Traits\Services;

use HMsoft\Cms\Models\Content\Service;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasNestedServices
 * Add this trait to any model that can have nested services.
 * It provides the 'services()' relationship method.
 */
trait HasNestedServices
{
    /**
     * Get all of the model's services.
     */
    public function services(): MorphMany
    {
        return $this->morphMany(Service::class, 'owner');
    }
}
