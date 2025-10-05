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

class StoreNestedPostRequest extends MyRequest
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

        // Set the post type from route parameter
        $this->merge([
            'type' => $this->route('type'),
        ]);

        // Convert boolean fields
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

        $rules = $this->getPostRules('create');
        
        // Add owner validation rules
        $rules['owner_id'] = ['required', 'integer'];
        $rules['owner_type'] = ['required', 'string'];

        // Add category, organization, feature, download, and attribute rules
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
