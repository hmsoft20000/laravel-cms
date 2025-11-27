<?php

namespace HMsoft\Cms\Http\Requests\DownloadItem;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;

class Store extends MyRequest
{

    use ValidatesSingleMedia, CategoryValidationRules;


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
        $file = 'cms.download_items.validation.store.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.download_items.validation.store.attributes';
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'file_size' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],

            'download_links' => ['required', 'array', 'min:1'],
            'download_links.*.file_path' => ['required', 'nullable', 'url'],
            'download_links.*.is_active' => ['sometimes'],
            'locales' => ['required', 'array', 'min:1'],
            'locales.*.locale' => ['required'],
            'locales.*.title' => ['sometimes', 'nullable', 'string'],
            'locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            'locales.*.content' => ['sometimes', 'nullable', 'string'],
        ];

        // foreach ($this->locales ?? [] as $index => $locale) {

        //     $rules["locales.$index.short_content"] = ['nullable', 'string'];

        //     $rules["locales.$index.title"] = [
        //         'nullable',
        //         'string',
        //         // Rule::unique('download_items_translations', 'title')
        //         //     ->where('locale', $locale['locale']),
        //     ];
        // }

        $categoryRules = $this->getCategoryIdsValidationRules('downloadItem', 'category_ids');

        return  array_merge(
            $rules,
            $this->getSingleImageValidationRules(),
            $categoryRules
        );
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasAtLeastOneName = collect($this->locales)
                ->contains(fn($locale) => !empty($locale['title']));

            if (! $hasAtLeastOneName) {
                $validator->errors()->add(
                    'locales.*.title',
                    trans('cms.download_items.validation.store.at_least_one_name')
                );
            }
        });
    }
}
