<?php

namespace HMsoft\Cms\Http\Requests\Shop\Addons;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Shop\ItemAddonValidationRules;

class StoreItemAddonRequest extends MyRequest
{
    use ItemAddonValidationRules;

    public function rules(): array
    {
        return $this->storeRules();
    }
}
