<?php

namespace HMsoft\Cms\Http\Requests\Sector;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;

class Store extends MyRequest
{

    use ValidatesSingleMedia;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return trans('cms::sectors.validation.store.messages');
    }

    public function attributes()
    {
        return trans('cms::sectors.validation.store.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'work_ratio' => ['nullable'],
            'locales' => ['required', 'array', 'min:1'],
            'locales.*.locale' => ['required'],
        ];

        foreach ($this->locales ?? [] as $index => $locale) {

            $rules["locales.$index.short_content"] = ['nullable', 'string'];

            $rules["locales.$index.name"] = [
                'nullable',
                'string',
                // Rule::unique('sectors_translations', 'name')
                //     ->where('locale', $locale['locale']),
            ];
        }

        return  array_merge(
            $rules,
            $this->getSingleImageValidationRules()
        );
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasAtLeastOneName = collect($this->locales)
                ->contains(fn($locale) => !empty($locale['name']));

            if (! $hasAtLeastOneName) {
                $validator->errors()->add(
                    'locales.*.name',
                    __('cms::sectors.validation.store.at_least_one_name')
                );
            }
        });
    }
}
