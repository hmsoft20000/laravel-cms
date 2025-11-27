<?php

namespace HMsoft\Cms\Traits\OurValue;

use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;
use Illuminate\Validation\Rule;

trait OurValueValidationRules
{

    use ValidatesSingleMedia;

    protected function getOurValueRules(string $context = 'update'): array
    {
        $validLocales = Lang::pluck('locale')->toArray();

        $rules = [
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'delete_image' => ['sometimes', 'boolean'],
            'sort_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'locales.*.locale' => ['required', 'string', Rule::in($validLocales)],
            'locales.*.title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'locales.*.description' => ['sometimes', 'nullable', 'string'],
        ];

        if ($context === 'create') {
            $rules['locales'] = ['required', 'array', 'min:1'];
        } else { // update context
            $rules['locales'] = ['sometimes', 'array', 'min:1'];
        }

        return array_merge(
            $rules,
            $this->getSingleImageValidationRules()
        );
    }

    // Custom validation logic to ensure at least one title and value is provided
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('locales')) {
                $hasAtLeastOneTitle = collect($this->locales)
                    ->contains(fn($locale) => !empty($locale['title']));

                if (!$hasAtLeastOneTitle) {
                    $validator->errors()->add(
                        'locales.0.title', // Point error to the first title field
                        __('cms.statistics.validation.at_least_one_title')
                    );
                }
            }
        });
    }
}
