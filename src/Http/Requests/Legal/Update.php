<?php

namespace HMsoft\Cms\Http\Requests\Legal;

use HMsoft\Cms\Http\Requests\MyRequest;

class Update extends MyRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return trans("cms::legal.validation.update.messages");
    }

    public function attributes()
    {
        return trans("cms::legal.validation.update.attributes");
    }


    public function rules(): array
    {
        $rules = [
            'locales' => ['sometimes', 'array', 'min:1'],
            'locales.*.locale' => ['required', 'string'],
            'locales.*.slug' => ['sometimes', 'filled', 'string', 'max:255'],
            'locales.*.short_content' => ['sometimes', 'nullable', 'string'],
            'locales.*.content' => ['sometimes', 'nullable', 'string'],
            'locales.*.meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'locales.*.meta_description' => ['sometimes', 'nullable', 'string'],
            'locales.*.meta_keywords' => ['sometimes', 'nullable', 'string'],
        ];

        return $rules;
    }
}
