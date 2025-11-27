<?php

namespace HMsoft\Cms\Traits\Blogs;

use HMsoft\Cms\Models\Content\Blog;

/**
 * Trait BlogValidationRules
 *
 * Provides centralized validation rules for Services.
 */
trait BlogValidationRules
{
    /**
     * Get the shared validation rules for a Blog based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getBlogRules(string $context = 'update'): array
    {
        $rules = [];

        switch ($context) {
            case 'create':
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
                break;
            case 'update':
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
                break;
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of blogs.
     * This is the helper method for bulk operations.
     */
    protected function getNestedBlogRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getBlogRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }

    protected function getAttachedBlogsRules(string $inputName = 'attached_blogs_ids'): array
    {
        $blog = resolve(Blog::class);
        $tableName = $blog->getTable();
        return [
            $inputName => 'sometimes|array',
            $inputName . '.*' => 'required|integer|exists:' . $tableName . ',id',
        ];
    }
}
