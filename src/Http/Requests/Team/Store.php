<?php

namespace HMsoft\Cms\Http\Requests\Team;

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
        $file = 'cms.teams.validation.store.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.teams.validation.store.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => [
                'string',
                'required'
            ],
            'locales' => [
                'array',
                'nullable'
            ],
            'locales.*.locale' => [
                'string',
                'nullable'
            ],
            'locales.*.job' => [
                'string',
                'nullable'
            ],
            'locales.*.short_content' => [
                'sometimes',
                'string',
                'nullable'
            ],
            'social_links' => [
                'sometimes',
                'array',
            ],
            'social_links.*.link' => [
                'sometimes',
                'string',
                'required'
            ],
        ];
        return  $rules;
    }
}
