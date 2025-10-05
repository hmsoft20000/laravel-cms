<?php

namespace HMsoft\Cms\Http\Requests\Posts;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;

class StorePostRequest extends MyRequest
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
        $this->merge([
            'type' => $this->route('type'),
        ]);
        $booleanFields = ['show_in_footer', 'show_in_header', 'is_active'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function messages()
    {
        return trans("cms::posts.validation.store.messages");
    }
    public function attributes()
    {
        return trans("cms::posts.validation.store.attributes");
    }
    public function rules(): array
    {
        $postType = $this->input('type');

        $rules = [
            'type' => ['required', 'string', Rule::in(['portfolio', 'blog', 'service'])],
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

        $categoryRules = $this->getCategoryIdsValidationRules($postType, 'category_ids');
        $partnerRules = $this->getOrganizationIdsValidationRules('partner', 'partner_ids');
        $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', 'sponsor_ids');
        $featureRules = $this->getNestedFeatureRules('features.*.', 'create');
        $downloadRules = $this->getNestedDownloadRules('downloads.*.', 'create');
        $attributeRules = $this->getAttributeValidationRules();


        $rules = array_merge($rules, $categoryRules, $partnerRules, $sponsorRules, $featureRules, $downloadRules, $attributeRules);

        return $rules;
    }
}
