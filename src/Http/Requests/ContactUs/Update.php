<?php

namespace HMsoft\Cms\Http\Requests\ContactUs;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class Update extends MyRequest
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

    public function prepareForValidation()
    {
        $this->merge([
            'id' =>  $this->route()?->originalParameter('contact'),
        ]);
    }

    public function messages()
    {
        $file = 'cms.contact.validation.update.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.contact.validation.update.attributes';
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
            'status' => [
                'sometimes', // The field is optional
                'required',  // But if present, it must not be empty
                Rule::in(['read', 'unread']), // Must be one of these two values
            ],
            'is_starred' => [
                'sometimes',
                'required',
                'boolean', // Must be a boolean (true, false, 1, 0)
            ],
        ];
    }
}
