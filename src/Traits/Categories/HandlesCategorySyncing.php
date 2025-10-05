<?php

namespace HMsoft\Cms\Traits\Categories;

use Illuminate\Database\Eloquent\Model;

trait HandlesCategorySyncing
{
    /**
     * Sync categories for a given model if they are present in the data array.
     *
     * @param Model $model The model instance (e.g., Post, Product).
     * @param array|null $categoryIds The category ids array from the request.
     * @return void
     */
    protected function syncCategories(Model $model, ?array $categoryIds = null): void
    {
        // Check if the model uses the Categorizable trait and has the categories relationship
        // and if the category_ids are present
        if (!method_exists($model, 'categories')) return;
        if ($categoryIds === null) return;
        if (method_exists($model, 'categories')) {
            $model->categories()->sync($categoryIds);
        }
    }
}
