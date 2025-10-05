<?php

namespace HMsoft\Cms\Http\Requests\NestedPosts;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Posts\PostValidationRules;
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use Illuminate\Database\Eloquent\Model;

class UpdateAllNestedPostRequest extends MyRequest
{
    use PostValidationRules,
        ValidatesCustomAttributes,
        FeatureValidationRules,
        DownloadValidationRules,
        CategoryValidationRules,
        OrganizationValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * We will automatically add the owner_type and map owner field here.
     */
    protected function prepareForValidation(): void
    {
        $owner = $this->route('owner');
        if ($owner instanceof Model) {
            $this->merge([
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => $owner->id,
            ]);
        }

        // Convert boolean fields for all posts
        $booleanFields = ['show_in_footer', 'show_in_header', 'is_active'];
        foreach ($this->all() as $index => $postData) {
            foreach ($booleanFields as $field) {
                if (isset($postData[$field])) {
                    $this->merge([
                        "{$index}.{$field}" => filter_var($postData[$field], FILTER_VALIDATE_BOOLEAN),
                    ]);
                }
            }
        }
    }

    public function messages()
    {
        return trans("cms::posts.validation.updateAll.messages");
    }

    public function attributes()
    {
        return trans("cms::posts.validation.updateAll.attributes");
    }

    public function rules(): array
    {
        $rules = [];

        // Get the post type from route parameter
        $postType = $this->route('type');

        // Validate each post in the array
        foreach ($this->all() as $index => $postData) {
            $postRules = $this->getPostRules('update');

            // Add index prefix to each rule
            foreach ($postRules as $field => $rule) {
                $rules["{$index}.{$field}"] = $rule;
            }

            // Add ID validation for updates
            $rules["{$index}.id"] = ['required', 'integer'];

            // Add category, organization, feature, download, and attribute rules with index prefix
            $categoryRules = $this->getCategoryIdsValidationRules($postType, "{$index}.category_ids");
            $partnerRules = $this->getOrganizationIdsValidationRules('partner', "{$index}.partner_ids");
            $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', "{$index}.sponsor_ids");
            $featureRules = $this->getNestedFeatureRules("{$index}.features.*.", 'update');
            $downloadRules = $this->getNestedDownloadRules("{$index}.downloads.*.", 'update');
            $attributeRules = $this->getAttributeValidationRules("{$index}.");

            $rules = array_merge($rules, $categoryRules, $partnerRules, $sponsorRules, $featureRules, $downloadRules, $attributeRules);
        }

        return $rules;
    }
}
