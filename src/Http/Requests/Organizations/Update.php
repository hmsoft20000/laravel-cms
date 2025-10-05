<?php

namespace HMsoft\Cms\Http\Requests\Organizations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;

class Update extends MyRequest
{
    use OrganizationValidationRules;

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
        // Call the trait with the 'update' context
        return $this->getOrganizationRules('update');
    }
}
