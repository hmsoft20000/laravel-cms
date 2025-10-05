<?php

namespace HMsoft\Cms\Http\Requests\Team;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

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

    public function prepareForValidation()
    {
        $this->merge([
            'id' =>  $this->route()?->originalParameter('team'),
        ]);
    }

    public function messages()
    {
        return trans('cms::teams.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::teams.validation.update_all.attributes');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = [
            '*.id' => [Rule::exists('teams', 'id')],
            '*.name' => [
                'sometimes',
                'string',
                'required'
            ],
            '*.locales' => [
                'sometimes',
                'array',
                'nullable'
            ],
            '*.locales.*.locale' => [
                'sometimes',
                'string',
            ],
            '*.locales.*.job' => [
                'sometimes',
                'string',
            ],
            '*.locales.*.short_content' => [
                'sometimes',
                'string',
                'nullable'
            ],
            '*.social_links' => [
                'sometimes',
                'array',
                'nullable'
            ],
            '*.social_links.*.link' => [
                'sometimes',
                'string',
                'required'
            ],
        ];

        return  $rules;
    }
}
