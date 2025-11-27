<?php

namespace HMsoft\Cms\Http\Requests\ContactUs;

use HMsoft\Cms\Http\Requests\MyRequest;

class ReplyRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        $file = 'cms.contact.validation.reply.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.contact.validation.reply.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'reply_message' => ['required', 'string', 'min:10'],
        ];
    }
}
