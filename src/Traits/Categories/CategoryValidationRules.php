<?php

namespace HMsoft\Cms\Traits\Categories;

use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Models\Sector\Sector;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;
use Illuminate\Validation\Rule;

trait CategoryValidationRules
{
    use ValidatesSingleMedia;

    protected function getCategoryRules(string $context = 'update'): array
    {

        $table = resolve(Category::class)->getTable();
        $tableSector = resolve(Sector::class)->getTable();

        $rules = [
            'parent_id' => ['sometimes', 'nullable', Rule::exists($table, 'id')],
            'sector_id' => ['sometimes', 'nullable', Rule::exists($tableSector, 'id')],
            'locales.*.locale' => ['sometimes', 'nullable', 'string'],
            'locales.*.title' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];

        switch ($context) {
            case 'create':
                $rules['type'] = ['required', 'string'];
                $rules['is_active'] = ['sometimes', 'boolean'];
                $rules['locales'] = ['required', 'array', 'min:1'];
                break;

            case 'update':
                $rules['is_active'] = ['sometimes', 'boolean'];
                $rules['delete_image'] = ['sometimes', 'boolean'];
                $rules['locales'] = ['sometimes', 'array', 'min:1'];
                break;
        }
        return array_merge(
            $rules,
            $this->getSingleImageValidationRules()
        );
    }


    /**
     * Get the validation rules for a nested array of categories.
     * This is the new helper method.
     */
    protected function getNestedCategoryRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getCategoryRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }

    /**
     * Get the validation rules for an array of category IDs,
     * ensuring they have a specific type (e.g., 'portfolio', 'blog', 'service').
     */
    protected function getCategoryIdsValidationRules(string|null $type, string $prefix): array
    {
        return [
            $prefix => ['sometimes', 'array'],
            $prefix . '.*' => ['integer', Rule::exists('categories', 'id')->when($type, fn($q) => $q->where('type', $type))],
        ];
    }
}
