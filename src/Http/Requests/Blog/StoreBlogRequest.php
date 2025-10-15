<?php

namespace HMsoft\Cms\Http\Requests\Blog;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Content\Blog;
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;

class StoreBlogRequest extends MyRequest
{

    use BlogValidationRules,
        ValidatesCustomAttributes,
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
        return trans("cms::blogs.validation.store.messages");
    }
    public function attributes()
    {
        return trans("cms::blogs.validation.store.attributes");
    }
    public function rules(): array
    {
        $rules = $this->getBlogRules('create');

        $categoryRules = $this->getCategoryIdsValidationRules('blog', 'category_ids');
        $partnerRules = $this->getOrganizationIdsValidationRules('partner', 'partner_ids');
        $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', 'sponsor_ids');
        $featureRules = $this->getNestedFeatureRules('features.*.', 'create');
        $downloadRules = $this->getNestedDownloadRules('downloads.*.', 'create');
        $attributeRules = $this->getAttributeValidationRules((new Blog)->getMorphClass());

        $rules = array_merge($rules, $categoryRules, $partnerRules, $sponsorRules, $featureRules, $downloadRules, $attributeRules);

        return $rules;
    }
}
