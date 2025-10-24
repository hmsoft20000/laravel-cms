<?php

namespace HMsoft\Cms\Traits\Attributes;

use Illuminate\Validation\Rule;

trait AttributeValidationRules
{

    /**
     * Get the shared validation rules for an attribute.
     *
     * @param string $scope The scope of the attribute (e.g., 'portfolio', 'product').
     * @param string $context The context ('create' or 'update').
     * @return array
     */

    protected function getAttributeRules(string $scope, string $context = 'update'): array
    {
        $acceptedTypes = ['text', 'textarea', 'select', 'radio', 'checkbox', 'number', 'date', 'datetime', 'year', 'boolean', 'color', 'wysiwyg', 'url'];

        $rules = [
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'category_ids' => ['sometimes', 'array'],
            // This rule ensures categories are from the same scope as the attribute
            'category_ids.*' => ['sometimes', 'integer', Rule::exists('categories', 'id')->where('type', $scope)],

            // Locales Rules
            'locales.*.locale' => ['required', 'string'],
            'locales.*.title' => ['sometimes', 'nullable', 'max:255'],

            // Options Rules
            'options' => ['sometimes', 'array'],
            'options.*.sort_number' => ['sometimes', 'integer'],
            'options.*.locales' => ['required_with:options', 'array'],
            'options.*.locales.*.locale' => ['required', 'string'],
            'options.*.locales.*.title' => ['sometimes', 'nullable', 'max:255'],
        ];

        switch ($context) {
            case 'create':
                $rules['scope'] = ['required', 'string'];
                $rules['type'] = ['required', 'string', Rule::in($acceptedTypes)];
                $rules['is_active'] = ['sometimes', 'boolean'];
                $rules['locales'] = ['required', 'array', 'min:1'];
                break;
            case 'update':
                $rules['type'] = ['sometimes', 'string', Rule::in($acceptedTypes)];
                $rules['is_active'] = ['sometimes', 'boolean'];
                $rules['delete_image'] = ['sometimes', 'boolean'];
                $rules['locales'] = ['sometimes', 'array', 'min:1'];
                $rules['options.*.id'] = ['sometimes', 'integer', 'exists:attribute_options,id'];
                break;
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of attributes.
     * This is the new helper method.
     */
    protected function getNestedAttributeRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getAttributeRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
