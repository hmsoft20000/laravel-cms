<?php

namespace HMsoft\Cms\Http\Requests\Posts;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Lang;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class UpdateAllPostRequest extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $validLocales = Lang::pluck('locale')->toArray();
        $data = $this->all();

        // Process each post item in the array
        foreach ($data as $index => $item) {
            // Handle boolean fields for each item
            $booleanFields = ['show_in_footer', 'show_in_header', 'is_active'];
            foreach ($booleanFields as $field) {
                if (isset($item[$field])) {
                    $data[$index][$field] = filter_var($item[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            // Handle locales for each item
            if (isset($item['locales'])) {
                $data[$index]['locales'] = collect($item['locales'])
                    ->filter(fn($locale) => in_array($locale['locale'] ?? null, $validLocales))
                    ->values()->all();
            }

            // Handle attribute_values for each item
            if (isset($item['attribute_values'])) {
                $data[$index]['attribute_values'] = collect($item['attribute_values'])
                    ->filter(fn($attr) => empty($attr['locale']) || in_array($attr['locale'], $validLocales))
                    ->values()->all();
            }

            // Handle features for each item
            if (isset($item['features'])) {
                $data[$index]['features'] = collect($item['features'])
                    ->map(function ($feature) use ($validLocales) {
                        $feature['locales'] = collect(Arr::get($feature, 'locales', []))
                            ->filter(fn($localeItem) => in_array($localeItem['locale'] ?? null, $validLocales))
                            ->values()->all();
                        return $feature;
                    })->all();
            }

            // Handle downloads for each item
            if (isset($item['downloads'])) {
                $data[$index]['downloads'] = collect($item['downloads'])
                    ->map(function ($download) use ($validLocales) {
                        $download['locales'] = collect(Arr::get($download, 'locales', []))
                            ->filter(fn($localeItem) => in_array($localeItem['locale'] ?? null, $validLocales))
                            ->values()->all();
                        return $download;
                    })->all();
            }
        }

        $this->merge($data);
    }

    public function messages()
    {
        return trans('cms::posts.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::posts.validation.update_all.attributes');
    }

    public function rules(): array
    {
        $rules = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:posts,id'],
            '*.show_in_footer' => ['sometimes', 'boolean'],
            '*.show_in_header' => ['sometimes', 'boolean'],
            '*.is_active' => ['sometimes', 'boolean'],

            '*.locales' => ['sometimes', 'array', 'min:1'],
            '*.locales.*.locale' => ['sometimes', 'required', 'string'],
            '*.locales.*.title' => ['sometimes', 'filled', 'string', 'max:255'],
            '*.locales.*.slug' => ['sometimes', 'filled', 'string', 'max:255'],
            '*.locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            '*.locales.*.content' => ['sometimes', 'nullable', 'string'],
            '*.locales.*.meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            '*.locales.*.meta_description' => ['sometimes', 'nullable', 'string'],
            '*.locales.*.meta_keywords' => ['sometimes', 'nullable', 'string'],

            '*.category_ids' => ['sometimes', 'array'],
            '*.category_ids.*' => ['sometimes', 'integer', Rule::exists('categories', 'id')],

            '*.partner_ids' => ['sometimes', 'array'],
            '*.partner_ids.*' => ['sometimes', 'integer', Rule::exists('organizations', 'id')],
            '*.sponsor_ids' => ['sometimes', 'array'],
            '*.sponsor_ids.*' => ['sometimes', 'integer', Rule::exists('organizations', 'id')],

            '*.features' => ['sometimes', 'array'],
            '*.features.*.id' => ['sometimes', 'nullable', 'integer', Rule::exists('features', 'id')],
            '*.features.*.file' => ['sometimes', 'nullable', 'file', 'max:2048'],
            '*.features.*.sort_number' => ['sometimes', 'integer'],
            '*.features.*.locales' => ['sometimes', 'array', 'min:1'],
            '*.features.*.locales.*.locale' => ['sometimes', 'required', 'string'],
            '*.features.*.locales.*.title' => ['sometimes', 'required', 'string', 'max:255'],
            '*.features.*.locales.*.description' => ['sometimes', 'nullable', 'string'],

            '*.downloads' => ['sometimes', 'array'],
            '*.downloads.*.id' => ['sometimes', 'nullable', 'integer', Rule::exists('downloads', 'id')],
            '*.downloads.*.file' => ['sometimes', 'nullable', 'file', 'max:10240'],
            '*.downloads.*.sort_number' => ['sometimes', 'integer'],
            '*.downloads.*.locales' => ['sometimes', 'array', 'min:1'],
            '*.downloads.*.locales.*.locale' => ['sometimes', 'required', 'string'],
            '*.downloads.*.locales.*.title' => ['sometimes', 'required', 'string', 'max:255'],
            '*.downloads.*.locales.*.description' => ['sometimes', 'nullable', 'string'],

            '*.keywords' => ['sometimes', 'array'],
            '*.keywords.*' => ['sometimes', 'string', 'max:255'],

            '*.attribute_values' => ['sometimes', 'array'],
            '*.attribute_values.*.attribute_id' => ['sometimes', 'integer', Rule::exists('attributes', 'id')],
            '*.attribute_values.*.locale' => ['sometimes', 'nullable', 'string'],
            '*.attribute_values.*.value' => ['sometimes', 'nullable'],
        ];

        return $rules;
    }
}
