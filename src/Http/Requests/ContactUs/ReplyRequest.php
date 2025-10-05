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

    // FIX: Point to the new lang file keys
    public function messages()
    {
        return trans('cms::contact.validation.reply.messages');
    }

    public function attributes()
    {
        return trans('cms::contact.validation.reply.attributes');
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
