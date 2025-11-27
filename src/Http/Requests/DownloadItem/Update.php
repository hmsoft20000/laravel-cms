<?php

namespace HMsoft\Cms\Http\Requests\DownloadItem;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use Illuminate\Validation\Rule;

class Update extends MyRequest
{

    use CategoryValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        $file = 'cms.download_items.validation.update.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes(): array
    {
        $file = 'cms.download_items.validation.update.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function prepareForValidation()
    {
        foreach (['is_active', 'delete_image'] as $key) {
            if ($this->has($key)) {
                $this->merge([$key => $this->boolean($key)]);
            }
        }
    }

    public function rules(): array
    {

        $rules = [
            'image' => ['sometimes', 'nullable', 'file'],

            'download_links' => ['required', 'array', 'min:1'],
            'download_links.*.file_path' =>  ['sometimes', 'nullable', 'url'],
            'download_links.*.is_active' => ['sometimes'],


            'file_size' => ['sometimes', 'nullable'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'sort_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'delete_image' => ['sometimes', 'boolean'],
            'locales' => ['sometimes', 'required', 'array', 'min:1'],
            'locales.*.locale' => ['sometimes', 'required'],
            'locales.*.title' => ['sometimes', 'nullable'],
            'locales.*.short_content' => ['sometimes', 'nullable'],
            'locales.*.content' => ['sometimes', 'nullable'],
        ];

        // foreach ($this->locales ?? [] as $index => $locale) {
        //     $rules["locales.$index.short_content"] = ['nullable', 'string'];

        //     $rules["locales.$index.title"] = [
        //         'nullable',
        //         'string',
        //         // Rule::unique('sectors_translations', 'name')
        //         //     ->where('locale', $locale['locale'])
        //         //     ->ignore($sectorId, 'sector_id'),
        //     ];
        // }

        $categoryRules = $this->getCategoryIdsValidationRules('downloadItem', 'category_ids');

        return array_merge($rules, $categoryRules);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasAtLeastOneName = collect($this->locales)
                ->contains(fn($locale) => !empty($locale['title']));

            if (! $hasAtLeastOneName) {
                $validator->errors()->add(
                    'locales.*.title',
                    trans('cms.download_items.validation.update.at_least_one_name')
                );
            }
        });
    }
}
