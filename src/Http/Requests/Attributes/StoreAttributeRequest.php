<?php

namespace HMsoft\Cms\Http\Requests\Attributes;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Attributes\AttributeValidationRules;

class StoreAttributeRequest extends MyRequest
{

    use AttributeValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (isset($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        
        // Add scope from route to the request data
        $scope = $this->route('type');
        if ($scope) {
            $this->merge(['scope' => $scope]);
        }
    }

    public function rules(): array
    {
        $scope = $this->route('type');
        return $this->getAttributeRules($scope, 'create');
    }
}
