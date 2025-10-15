<?php

namespace HMsoft\Cms\Traits\Features;

/**
 * Trait FeatureValidationRules
 *
 * يوفر مجموعة مركزية من قواعد التحقق للميزات (Features).
 */
trait FeatureValidationRules
{
    /**
     * Get the shared validation rules for a feature based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getFeatureRules(string $context = 'update'): array
    {
        $rules = [
            'image'         => ['sometimes', 'nullable', 'image', 'max:2048'],

            // قواعد الترجمات
            'locales.*.locale'      => ['required', 'string'],
            'locales.*.title'       => ['nullable', 'string', 'max:255'],
            'locales.*.description' => ['nullable', 'string'],
        ];

        switch ($context) {
            case 'create':
                $rules['is_active']   = ['sometimes', 'boolean'];
                $rules['sort_number'] = ['sometimes', 'integer'];
                $rules['locales']     = ['required', 'array', 'min:1'];
                break;
            case 'update':
                $rules['is_active']      = ['sometimes', 'boolean'];
                $rules['sort_number']    = ['sometimes', 'integer'];
                $rules['locales']        = ['sometimes', 'array', 'min:1'];
                $rules['delete_image']   = ['sometimes', 'boolean'];
                break;
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of features.
     * This is the new helper method.
     */
    protected function getNestedFeatureRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getFeatureRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
