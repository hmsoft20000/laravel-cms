<?php

namespace HMsoft\Cms\Http\Requests\Lang;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class Update extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return trans('cms::langs.validation.update.messages');
    }

    public function attributes(): array
    {
        return trans('cms::langs.validation.update.attributes');
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['nullable', 'string'],
            'direction' => ['nullable', 'string'],
            'locale' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
        return $rules;
    }
}
