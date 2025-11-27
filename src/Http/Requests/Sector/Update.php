<?php

namespace HMsoft\Cms\Http\Requests\Sector;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class Update extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        $file = 'cms.sectors.validation.update.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes(): array
    {
        $file = 'cms.sectors.validation.update.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function rules(): array
    {

        $rules = [
            'image' => ['nullable', 'file'],
            'work_ratio' => ['nullable'],
            'sort_number' => ['nullable', 'integer', 'min:0'],
            'delete_image' => ['sometimes', 'boolean'],
            'locales' => ['required', 'array', 'min:1'],
            'locales.*.locale' => ['required'],
        ];

        foreach ($this->locales ?? [] as $index => $locale) {
            $rules["locales.$index.short_content"] = ['nullable', 'string'];

            $rules["locales.$index.name"] = [
                'nullable',
                'string',
                // Rule::unique('sectors_translations', 'name')
                //     ->where('locale', $locale['locale'])
                //     ->ignore($sectorId, 'sector_id'),
            ];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasAtLeastOneName = collect($this->locales)
                ->contains(fn($locale) => !empty($locale['name']));

            if (! $hasAtLeastOneName) {
                $validator->errors()->add(
                    'locales.*.name',
                    trans('sectors.validation.update.at_least_one_name')
                );
            }
        });
    }
}
