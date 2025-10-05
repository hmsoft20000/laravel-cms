<?php

namespace HMsoft\Cms\Http\Requests\Organizations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;

class Store extends MyRequest
{
    use OrganizationValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Add the 'type' from the route to be used by the repository
        $this->merge(['type' => $this->route('type')]);

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        // Call the trait with the 'create' context
        return $this->getOrganizationRules('create');
    }
}
