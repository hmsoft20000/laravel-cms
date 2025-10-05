<?php

namespace HMsoft\Cms\Http\Requests\ContactUs;

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
        return trans('cms::contact.validation.store.messages');
    }

    public function attributes()
    {
        return trans('cms::contact.validation.store.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:191'],
            'email' => ['sometimes', 'email', 'max:191'],
            'mobile' => ['sometimes', 'string', 'max:191'],
            'residence' => ['sometimes', 'string', 'max:191'],
            'nationality' => ['sometimes', 'string', 'max:191'],
            'description' => ['sometimes', 'string'],
            'message' => ['sometimes', 'string'],
            'subject' => ['sometimes', 'string', 'max:191'],
            'file-upload' => ['sometimes', 'array'],
            'file-upload.*' => ['file', 'mimes:pdf,png,jpg,jpeg,doc,docx', 'max:10240'], // 10MB each
        ];
    }
}
