<?php

namespace HMsoft\Cms\Http\Requests\Lang;

use HMsoft\Cms\Http\Requests\MyRequest;

class Store extends MyRequest
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
        return trans('cms::langs.validation.store.messages');
    }

    public function attributes()
    {
        return trans('cms::langs.validation.store.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => ['required', 'string'],
            'direction' => ['required', 'string'],
            'locale' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ];

        return  $rules;
    }
}
