<?php

namespace HMsoft\Cms\Traits\Categories;

use HMsoft\Cms\Models\Shared\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Categorizable
{
    /**
     * Get all of the categories for the model.
     */
    public function categories(): MorphToMany
    {
        // we use the custom definition that we agreed on
        return $this->morphToMany(
            Category::class,
            'owner',          // custom relationship name
            'categorizables', // intermediate table name
            'owner_id',       // foreign key for the owner
            'category_id'     // foreign key for the category
        );
    }
}
