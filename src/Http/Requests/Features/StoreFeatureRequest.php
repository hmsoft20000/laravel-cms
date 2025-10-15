<?php

namespace HMsoft\Cms\Http\Requests\Features;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;

class StoreFeatureRequest extends MyRequest
{
    use FeatureValidationRules;

    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        $rules = $this->getFeatureRules('create');
        return $rules;
    }
}
