<?php

namespace HMsoft\Cms\Http\Requests\Attributes;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Attributes\AttributeValidationRules;

class UpdateAttributeRequest extends MyRequest
{

    use AttributeValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {

        $booleanFields = ['is_active', 'delete_image'];

        foreach ($booleanFields as $field) {
            if (isset($this->$field)) {
                $this->merge([
                    $field => filter_var($this->$field, FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function rules(): array
    {
        $attribute = $this->route('attribute');
        $scope = $attribute->scope;
        return $this->getAttributeRules($scope, 'update');
    }
}
