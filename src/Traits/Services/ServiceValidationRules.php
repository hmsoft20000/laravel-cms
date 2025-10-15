<?php

namespace HMsoft\Cms\Traits\Services;


/**
 * Trait ServiceValidationRules
 *
 * Provides centralized validation rules for Services.
 */
trait ServiceValidationRules
{
    /**
     * Get the shared validation rules for a Service based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getServiceRules(string $context = 'update'): array
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
     * Get the validation rules for a nested array of services.
     * This is the helper method for bulk operations.
     */
    protected function getNestedServiceRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getServiceRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
