<?php

namespace HMsoft\Cms\Traits\Organizations;

use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;
use Illuminate\Validation\Rule;

trait OrganizationValidationRules
{

    use ValidatesSingleMedia;

    /**
     * Get the shared validation rules for an organization.
     *
     * @param string $context The context ('create' or 'update').
     * @return array
     */
    protected function getOrganizationRules(string $context = 'update'): array
    {
        $validLocales = Lang::pluck('locale')->toArray();

        // Base rules applicable to both create and update
        $rules = [
            'website_url'           => ['sometimes', 'nullable', 'url'],
            'latitude'           => ['sometimes', 'nullable'],
            'longitude'           => ['sometimes', 'nullable'],
            'address'           => ['sometimes', 'nullable'],
            'mobile'           => ['sometimes', 'nullable'],
            'locales'           => ['array', 'min:1'],
            'locales.*.locale'  => ['required', 'string', Rule::in($validLocales)],
            'locales.*.name'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            'locales.*.content' => ['sometimes', 'nullable', 'string'],
        ];

        if ($context === 'create') {
            $rules['is_active'] = ['sometimes', 'boolean'];
            $rules['locales'][] = 'required'; // Ensure locales array is present on create
            $rules['type'] = 'required';
        } else { // update context
            $rules['is_active'] = ['sometimes', 'boolean'];
        }

        return array_merge(
            $rules,
            $this->getSingleImageValidationRules()
        );
    }


    /**
     * Get the validation rules for a nested array of organizations.
     * This is the new helper method.
     */
    protected function getNestedOrganizationRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getOrganizationRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }

    /**
     * Get the validation rules for an array of organization IDs,
     * ensuring they have a specific role (e.g., 'partner', 'sponsor').
     */
    protected function getOrganizationIdsValidationRules(string|null $type, string $prefix): array
    {
        return [
            $prefix => ['sometimes', 'array'],
            $prefix . '.*' => [
                'integer',
                Rule::exists('organizations', 'id')->when($type, fn($q) => $q->where('type', $type))
            ],
        ];
    }
}
