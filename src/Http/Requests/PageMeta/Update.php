<?php

namespace HMsoft\Cms\Http\Requests\PageMeta;

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
            'id' =>  $this->route()?->originalParameter('pageMeta'),
        ]);
    }

    public function messages()
    {
        return trans('cms::pages_meta.validation.update.messages');
    }

    public function attributes()
    {
        return trans('cms::pages_meta.validation.update.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = [
            'id' => [Rule::exists('pages_meta', 'id')],
            'name' => [
                'sometimes',
                'required',
                Rule::unique('pages_meta', 'name')->ignore($this->id),
            ],
            'locales' => [
                'sometimes',
                'required',
                'array',
            ],
            'locales.*.locale' => [
                'required',
            ],
            'locales.*.locale' => ['required', 'string'],
            'locales.*.title' => ['nullable', 'string', 'max:255'],
            'locales.*.description' => ['nullable', 'string'],
            'locales.*.keywords' => ['nullable', 'string', 'max:255'],
        ];

        return  $rules;
    }
}
