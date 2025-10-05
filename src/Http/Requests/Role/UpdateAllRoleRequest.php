<?php

namespace HMsoft\Cms\Http\Requests\Role;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class UpdateAllRoleRequest extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Process each role item in the array
        foreach ($data as $index => $item) {
            // Handle boolean fields for each item if any
            $booleanFields = [];
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
        return [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:roles,id'],
            '*.name' => ['sometimes', 'required', 'string', 'max:255'],
            '*.slug' => ['sometimes', 'required', 'string', 'max:255'],
            '*.description' => ['sometimes', 'nullable', 'string'],
            '*.level' => ['sometimes', 'required', 'integer', 'min:0'],
            '*.parent_id' => ['sometimes', 'nullable', 'integer', 'exists:roles,id'],
            '*.permission_ids' => ['sometimes', 'array'],
            '*.permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function messages()
    {
        return trans('cms::roles.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::roles.validation.update_all.attributes');
    }
}
