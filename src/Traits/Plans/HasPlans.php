<?php

namespace HMsoft\Cms\Traits\Plans;

use HMsoft\Cms\Models\Shared\Plan;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPlans
{
    /**
     * Get all of the model's plans (Polymorphic).
     */
    public function plans(): MorphMany
    {
        return $this->morphMany(Plan::class, 'owner')->orderBy('sort_number');
    }
}
