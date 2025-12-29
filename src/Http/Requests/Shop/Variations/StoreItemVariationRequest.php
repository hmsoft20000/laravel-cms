<?php

namespace HMsoft\Cms\Http\Requests\Shop\Variations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Shop\ItemVariationValidationRules;

class StoreItemVariationRequest extends MyRequest
{
    // نستخدم الـ Trait لنتمكن من إعادة استخدام القواعد في الـ Update أيضاً
    use ItemVariationValidationRules;

    /**
     * تحديد ما إذا كان المستخدم مصرحاً له بعمل هذا الطلب.
     */
    public function authorize(): bool
    {
        // يمكنك إضافة منطق الصلاحيات هنا (مثلاً التحقق من permission)
        return true;
    }

    /**
     * قواعد التحقق الخاصة بإنشاء تنويع جديد.
     */
    public function rules(): array
    {
        return $this->storeRules();
    }
}
