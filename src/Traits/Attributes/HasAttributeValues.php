<?php

namespace HMsoft\Cms\Traits\Attributes;

use HMsoft\Cms\Models\Shared\AttributeValue;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttributeValues
{
    /**
     * Get all of the attribute values for the model (Polymorphic).
     */
    public function attributeValues(): MorphMany
    {
        return $this->morphMany(AttributeValue::class, 'owner');
    }
}
