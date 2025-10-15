<?php

namespace HMsoft\Cms\Http\Requests\Attributes;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Attributes\AttributeValidationRules;
use Illuminate\Validation\Rule;

class UpdateAllAttributeRequest extends MyRequest
{
    // 1. use the same Trait
    use AttributeValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * The preparation logic is now simpler, only handling boolean conversions.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();
        $scope = $this->route('type');
        
        foreach ($data as $index => $item) {
            $booleanFields = ['is_active', 'delete_image'];
            foreach ($booleanFields as $field) {
                if (isset($item[$field])) {
                    $data[$index][$field] = filter_var($item[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }
            
            // Add scope to each item if not already present
            if ($scope && !isset($item['scope'])) {
                $data[$index]['scope'] = $scope;
            }
        }
        $this->merge($data);
    }

    /**
     * The rules are now DRY and fetched from the trait.
     */
    public function rules(): array
    {
        // 2. Get the scope from the URL to ensure all operations are secure
        $scope = $this->route('type');

        // 3. Get the base rules for a single attribute in the 'update' context
        $singleAttributeRules = $this->getAttributeRules($scope, 'update');

        $rulesForAll = [
            '*' => ['required', 'array'],
            // Ensure the IDs being updated belong to the correct scope
            '*.id' => ['required', 'integer', Rule::exists('attributes', 'id')->where('scope', $scope)],
        ];

        // 4. Prepend '*' to each rule from the trait
        foreach ($singleAttributeRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }

    public function messages()
    {
        return trans('cms::attributes.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::attributes.validation.update_all.attributes');
    }
}
