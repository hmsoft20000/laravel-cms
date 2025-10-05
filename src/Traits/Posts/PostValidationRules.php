<?php

namespace HMsoft\Cms\Traits\Posts;

use Illuminate\Validation\Rule;

/**
 * Trait PostValidationRules
 *
 * Provides centralized validation rules for Posts (Blog, Service, Portfolio).
 */
trait PostValidationRules
{
    /**
     * Get the shared validation rules for a Post based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getPostRules(string $context = 'update'): array
    {
        $rules = [];

        if ($context === 'create') {
            // --- In case of creation, fields are required ---
            $rules['type'] = ['required', 'string', Rule::in(['portfolio', 'blog', 'service'])];
            $rules['show_in_footer'] = ['sometimes', 'boolean'];
            $rules['show_in_header'] = ['sometimes', 'boolean'];
            $rules['is_active'] = ['sometimes', 'boolean'];
            $rules['locales'] = ['required', 'array', 'min:1'];
            $rules['locales.*.locale'] = ['required', 'string'];
            $rules['locales.*.title'] = ['required', 'string', 'max:255'];
            $rules['locales.*.slug'] = ['sometimes', 'filled', 'string', 'max:255'];
            $rules['locales.*.short_content'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.content'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.meta_title'] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules['locales.*.meta_description'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.meta_keywords'] = ['sometimes', 'nullable', 'string'];

            $rules['keywords'] = ['sometimes', 'array'];
            $rules['keywords.*'] = ['required', 'string', 'max:255'];
        } else { // $context === 'update'
            // --- In case of update, fields are optional ---
            $rules['type'] = ['sometimes', 'string', Rule::in(['portfolio', 'blog', 'service'])];
            $rules['show_in_footer'] = ['sometimes', 'boolean'];
            $rules['show_in_header'] = ['sometimes', 'boolean'];
            $rules['is_active'] = ['sometimes', 'boolean'];
            $rules['locales'] = ['sometimes', 'array', 'min:1'];
            $rules['locales.*.locale'] = ['sometimes', 'string'];
            $rules['locales.*.title'] = ['sometimes', 'filled', 'string', 'max:255'];
            $rules['locales.*.slug'] = ['sometimes', 'filled', 'string', 'max:255'];
            $rules['locales.*.short_content'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.content'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.meta_title'] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules['locales.*.meta_description'] = ['sometimes', 'nullable', 'string'];
            $rules['locales.*.meta_keywords'] = ['sometimes', 'nullable', 'string'];

            $rules['keywords'] = ['sometimes', 'array'];
            $rules['keywords.*'] = ['sometimes', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of posts.
     * This is the helper method for bulk operations.
     */
    protected function getNestedPostRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getPostRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
