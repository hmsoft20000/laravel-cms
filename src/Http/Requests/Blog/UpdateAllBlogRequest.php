<?php

namespace HMsoft\Cms\Http\Requests\Blog;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use HMsoft\Cms\Models\Content\Blog;

class UpdateAllBlogRequest extends MyRequest
{
    use BlogValidationRules,
        ValidatesCustomAttributes,
        FeatureValidationRules,
        DownloadValidationRules,
        CategoryValidationRules,
        OrganizationValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $singleRules = $this->getBlogRules('update');
        $tableName = (new Blog())->getTable();
        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:' . $tableName . ',id'],
        ];

        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        $categoryRules = $this->getCategoryIdsValidationRules('blog', '*.category_ids');
        $partnerRules = $this->getOrganizationIdsValidationRules('partner', '*.partner_ids');
        $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', '*.sponsor_ids');
        $featureRules = $this->getNestedFeatureRules('*.features.*.', 'update');
        $downloadRules = $this->getNestedDownloadRules('*.downloads.*.', 'update');
        $attributeRules = $this->getAttributeValidationRules((new Blog)->getMorphClass(), '*.');
        $rulesForAll = array_merge($rulesForAll, $categoryRules, $partnerRules, $sponsorRules, $featureRules, $downloadRules, $attributeRules);

        return $rulesForAll;
    }
}
