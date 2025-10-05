<?php

namespace HMsoft\Cms\Http\Requests\Categories;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;

class UpdateCategoryRequest extends MyRequest
{
    use CategoryValidationRules;

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
    }

    public function rules(): array
    {

        return $this->getCategoryRules('update');
    }
}
