<?php

namespace HMsoft\Cms\Http\Requests\Sector;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Lang;
use Illuminate\Validation\Rule;

class UpdateAll extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $validLocales = Lang::pluck('locale')->toArray();
        $data = $this->all();

        // Process each sector item in the array
        foreach ($data as $index => $item) {
            // Handle locales for each item
            if (isset($item['locales'])) {
                $data[$index]['locales'] = collect($item['locales'])
                    ->filter(fn($locale) => in_array($locale['locale'] ?? null, $validLocales))
                    ->values()->all();
            }
        }

        $this->merge($data);
    }

    public function messages()
    {
        $file = 'cms.download_items.validation.update_all.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.download_items.validation.update_all.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:download_items,id'],

            '*.download_links' => ['required', 'array', 'min:1'],
            '*.download_links.*.file_path' =>  ['sometimes', 'nullable', 'url'],
            '*.download_links.*.is_active' => ['sometimes'],


            '*.file_size' => ['sometimes', 'nullable'],
            '*.is_active' => ['sometimes', 'nullable', 'boolean'],
            '*.sort_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            '*.delete_image' => ['sometimes', 'boolean'],
            '*.locales' => ['sometimes', 'required', 'array', 'min:1'],
            '*.locales.*.locale' => ['sometimes', 'required'],
            '*.locales.*.title' => ['sometimes', 'nullable'],
            '*.locales.*.short_content' => ['sometimes', 'nullable'],
            '*.locales.*.content' => ['sometimes', 'nullable'],
        ];

        $categoryRules = $this->getCategoryIdsValidationRules('blog', '*.category_ids');

        return array_merge($rulesForAll, $categoryRules);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            foreach ($data as $index => $item) {
                if (isset($item['locales'])) {
                    $hasAtLeastOneName = collect($item['locales'])
                        ->contains(fn($locale) => !empty($locale['title']));

                    if (! $hasAtLeastOneName) {
                        $validator->errors()->add(
                            "{$index}.locales.*.title",
                            trans('cms.download_items.validation.update_all.at_least_one_title')
                        );
                    }
                }
            }
        });
    }
}
