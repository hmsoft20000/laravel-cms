<?php

namespace HMsoft\Cms\Http\Requests\Shop\Variations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Shop\ItemVariationValidationRules;

class StoreItemVariationRequest extends MyRequest
{
    use ItemVariationValidationRules;

    public function rules(): array
    {
        return $this->storeRules();
    }
}
