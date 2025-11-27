<?php

namespace HMsoft\Cms\Http\Requests\Shop;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Shop\Item; // <-- نموذج المنتج
use HMsoft\Cms\Traits\Shop\ItemValidationRules; // <-- الـ Trait الجديد
use HMsoft\Cms\Traits\Attributes\ValidatesCustomAttributes;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use HMsoft\Cms\Traits\Plans\PlanValidationRules;
use HMsoft\Cms\Traits\Faqs\FaqValidationRules;
use HMsoft\Cms\Traits\Blogs\BlogValidationRules;

class UpdateItemRequest extends MyRequest
{
    use ItemValidationRules,
        ValidatesCustomAttributes,
        FeatureValidationRules,
        DownloadValidationRules,
        CategoryValidationRules,
        OrganizationValidationRules,
        PlanValidationRules,
        FaqValidationRules,
        BlogValidationRules;

    public function authorize(): bool
    {
        return true; // أضف منطق الصلاحيات (Permissions) هنا
    }

    public function prepareForValidation()
    {
        $booleanFields = ['manage_stock', 'is_virtual', 'is_active'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function rules(): array
    {
        // الاختلاف الرئيسي هنا هو تمرير 'update'
        $rules = $this->getItemRules('update');

        $categoryRules = $this->getCategoryIdsValidationRules('item', 'category_ids');
        $partnerRules = $this->getOrganizationIdsValidationRules('partner', 'partner_ids');
        $sponsorRules = $this->getOrganizationIdsValidationRules('sponsor', 'sponsor_ids');
        $featureRules = $this->getNestedFeatureRules('features.*.', 'update'); // <-- 'update'
        $downloadRules = $this->getNestedDownloadRules('downloads.*.', 'update'); // <-- 'update'
        $planRules = $this->getNestedPlanRules('plans.*.', 'update'); // <-- 'update'
        $faqRules = $this->getNestedFaqRules('faqs.*.', 'update'); // <-- 'update'
        $attachedDownloadRules = $this->getAttachedDownloadRules('attached_download_ids'); //
        $attachedBlogsRules = $this->getAttachedBlogsRules('attached_blogs_ids');
        $attributeRules = $this->getAttributeValidationRules(resolve(Item::class)->getMorphClass());

        return array_merge(
            $rules,
            $categoryRules,
            $partnerRules,
            $sponsorRules,
            $featureRules,
            $downloadRules,
            $planRules,
            $faqRules,
            $attributeRules,
            $attachedDownloadRules,
            $attachedBlogsRules
        );
    }
}
