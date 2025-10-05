<?php

namespace HMsoft\Cms\Traits\Downloads;

/**
 * Trait DownloadValidationRules
 *
 * يوفر مجموعة مركزية من قواعد التحقق للميزات (Downloads).
 */
trait DownloadValidationRules
{
    /**
     * Get the shared validation rules for a download based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getDownloadRules(string $context = 'update'): array
    {
        $rules = [
            'file'         => ['sometimes', 'nullable', 'file', 'max:10240'],

            // قواعد الترجمات
            'locales.*.locale'      => ['required', 'string'],
            'locales.*.title'       => ['required', 'string', 'max:255'],
            'locales.*.description' => ['nullable', 'string'],
        ];

        switch ($context) {
            case 'create':
                $rules['is_active']   = ['sometimes', 'boolean'];
                $rules['sort_number'] = ['sometimes', 'integer'];
                $rules['locales']     = ['required', 'array', 'min:1'];
                $rules['file_path']    = ['required', 'nullable', 'string'];

                break;
            case 'update':
                $rules['is_active']      = ['sometimes', 'boolean'];
                $rules['sort_number']    = ['sometimes', 'integer'];
                $rules['locales']        = ['sometimes', 'array', 'min:1'];
                $rules['delete_file']   = ['sometimes', 'boolean'];
                $rules['file_path']    = ['sometimes', 'nullable', 'string'];
                break;
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of downloads.
     * This is the new helper method.
     */
    protected function getNestedDownloadRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getDownloadRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
