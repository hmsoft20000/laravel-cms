<?php

namespace HMsoft\Cms\Http\Requests\PageMeta;

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
            'id' =>  $this->route()?->originalParameter('pageMeta'),
        ]);
    }

    public function messages()
    {
        return trans('cms::pages_meta.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::pages_meta.validation.update_all.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = [
            'pages' => ['required', 'array', 'min:1'],
            'pages.*.id' => ['required', Rule::exists('pages_meta', 'id')],

            'pages.*.translations' => ['required', 'array'],
            'pages.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'pages.*.translations.*.description' => ['nullable', 'string'],
            'pages.*.translations.*.keywords' => ['nullable', 'string', 'max:255'],
        ];

        return  $rules;
    }
}
