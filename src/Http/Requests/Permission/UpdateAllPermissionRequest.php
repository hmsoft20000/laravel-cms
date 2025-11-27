<?php

namespace HMsoft\Cms\Http\Requests\Permission;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class UpdateAllPermissionRequest extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Process each permission item in the array
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
            '*.id' => ['required', 'integer', 'exists:permissions,id'],
            '*.name' => ['sometimes', 'required', 'string', 'max:255'],
            '*.slug' => ['sometimes', 'required', 'string', 'max:255'],
            '*.description' => ['sometimes', 'nullable', 'string'],
            '*.module' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        $file = 'cms.permissions.validation.update_all.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.permissions.validation.update_all.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }
}
