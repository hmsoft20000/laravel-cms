<?php

namespace HMsoft\Cms\Http\Requests\Categories;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Categories\CategoryValidationRules;

class UpdateAllCategoryRequest extends MyRequest
{

    use CategoryValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Process each category item in the array
        foreach ($data as $index => $item) {
            // Handle boolean fields for each item
            $booleanFields = ['is_active', 'delete_image'];
            foreach ($booleanFields as $field) {
                if (isset($item[$field])) {
                    $data[$index][$field] = filter_var($item[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }
        $this->merge($data);
    }

    public function rules(): array
    {
        $singleCategoryRules = $this->getCategoryRules('update');

        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:categories,id'],
        ];

        foreach ($singleCategoryRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }

    public function messages()
    {
        return trans('cms::categories.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::categories.validation.update_all.attributes');
    }
}
