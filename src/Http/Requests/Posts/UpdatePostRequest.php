<?php

namespace HMsoft\Cms\Http\Requests\Posts;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Lang; // Assuming you have this model
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class UpdatePostRequest extends MyRequest
{
    use ValidatesCustomAttributes,
        FeatureValidationRules,
        DownloadValidationRules,
        CategoryValidationRules,
        OrganizationValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        // This logic is preserved from your original file.

        $this->merge([
            'type' => $this->route('type'),
            'id' =>  $this->route()?->originalParameter('post'),
        ]);


        $booleanFields = ['show_in_footer', 'show_in_header', 'is_active'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        $validLocales = Lang::pluck('locale')->toArray();

        if ($this->has('locales')) {
            $this->merge(['locales' => collect($this->input('locales'))
                ->filter(fn($item) => in_array($item['locale'] ?? null, $validLocales))
                ->values()->all()]);
        }

        if ($this->has('attribute_values')) {
            $this->merge(['attribute_values' => collect($this->input('attribute_values'))
                ->filter(fn($item) => empty($item['locale']) || in_array($item['locale'], $validLocales))
                ->values()->all()]);
        }

        if ($this->has('features')) {
            $this->merge(['features' => collect($this->input('features'))
                ->map(function ($feature) use ($validLocales) {
                    $feature['locales'] = collect(Arr::get($feature, 'locales', []))
                        ->filter(fn($localeItem) => in_array($localeItem['locale'] ?? null, $validLocales))
                        ->values()->all();
                    return $feature;
                })->all()]);
        }

        if ($this->has('downloads')) {
            $this->merge(['downloads' => collect($this->input('downloads'))
                ->map(function ($download) use ($validLocales) {
                    $download['locales'] = collect(Arr::get($download, 'locales', []))
                        ->filter(fn($localeItem) => in_array($localeItem['locale'] ?? null, $validLocales))
                        ->values()->all();
                    return $download;
                })->all()]);
        }
    }

    public function messages()
    {
        return trans("cms::posts.validation.update.messages");
    }

    public function attributes()
    {
        return trans("cms::posts.validation.update.attributes");
    }


    public function rules(): array
    {
        $postType = $this->input('type');

        $rules = [
            'id' => [Rule::exists('posts', 'id')->where('type', $postType)],
            'show_in_footer' => ['sometimes', 'boolean'],
            'show_in_header' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],

            'locales' => ['sometimes', 'array', 'min:1'],
            'locales.*.locale' => ['required', 'string'],
            'locales.*.title' => ['sometimes', 'filled', 'string', 'max:255'],
            'locales.*.slug' => ['sometimes', 'filled', 'string', 'max:255'],
            'locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            'locales.*.content' => ['sometimes', 'nullable', 'string'],
            'locales.*.meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'locales.*.meta_description' => ['sometimes', 'nullable', 'string'],
            'locales.*.meta_keywords' => ['sometimes', 'nullable', 'string'],

            'keywords' => ['sometimes', 'array'],
            'keywords.*' => ['required', 'string', 'max:255'],
        ];

        // Use trait methods for validation rules
        $categoryRules = $this->getCategoryIdsValidationRules($postType, 'category_ids');
        $partnerRules = $this->getOrganizationIdsValidationRules('partner', 'partner_ids');
        $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', 'sponsor_ids');
        $featureRules = $this->getNestedFeatureRules('features.*.', 'update');
        $downloadRules = $this->getNestedDownloadRules('downloads.*.', 'update');
        $attributeRules = $this->getAttributeValidationRules();

        $rules = array_merge($rules, $categoryRules, $partnerRules, $sponsorRules, $featureRules, $downloadRules, $attributeRules);

        // Unique validation for title and slug
        foreach ($this->locales ?? [] as $index => $locale) {
            $localeCode = $locale['locale'] ?? null;
            if ($locale['title']) {
                $rules["locales.$index.title"][] = Rule::unique('post_translations', 'title')->where('locale', $localeCode)->ignore($this->id, 'post_id');
            }
            if ($locale['slug']) {
                $rules["locales.$index.slug"][] = Rule::unique('post_translations', 'slug')->where('locale', $localeCode)->ignore($this->id, 'post_id');
            }
        }

        return $rules;
    }
}
