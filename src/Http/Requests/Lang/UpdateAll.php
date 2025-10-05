<?php

namespace HMsoft\Cms\Http\Requests\Lang;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Lang;

class UpdateAll extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    public function messages()
    {
        return trans('cms::langs.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::langs.validation.update_all.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:sectors,id'],
            '*.name' => ['sometimes', 'nullable', 'string'],
            '*.direction' => ['sometimes', 'nullable', 'string'],
            '*.locale' => ['sometimes', 'nullable', 'string'],
            '*.is_active' => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
