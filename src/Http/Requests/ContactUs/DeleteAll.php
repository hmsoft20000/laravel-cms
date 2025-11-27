<?php

namespace HMsoft\Cms\Http\Requests\ContactUs;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class DeleteAll extends MyRequest
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
        $file = 'cms.contact.validation.delete.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.contact.validation.delete.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:contact_us_messages,id'
        ];
    }
}
