<?php

namespace HMsoft\Cms\Http\Requests\PageMeta;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

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
        $file = 'cms.pages_meta.validation.store.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.pages_meta.validation.store.attributes';
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
                'required',
                Rule::unique('pages_meta', 'name'),
            ],
            'locales' => [
                'required',
                'array',
            ],
            'locales.*.locale' => [
                'required',
            ],
            'locales.*.title' => [],
            'locales.*.description' => [],
            'locales.*.keywords' => [],
        ];
        return  $rules;
    }
}
