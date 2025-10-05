<?php

namespace HMsoft\Cms\Http\Requests\Slider;

use HMsoft\Cms\Enums\SliderTypeEnum;
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

    public function prepareForValidation()
    {
        $this->merge([
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function messages()
    {
        return trans('cms::sliders.validation.store.messages');
    }

    public function attributes()
    {
        return trans('cms::sliders.validation.store.attributes');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => [
                'file'
            ],
            'status' => [
                'boolean'
            ],

            'from_time' => [
                'nullable',
                'date'
            ],
            'to_time' => [
                'nullable',
                'date',
                'after:from_time'
            ],
            'type' => [
                'required',
                Rule::in(SliderTypeEnum::values())
            ],
            'locales' => [
                'required',
                'array',
            ],
            'locales.*.locale' => [
                'required',
            ],
            'locales.*.title' => [
                'nullable',
                'string',
            ],
            'locales.*.content' => [
                'nullable',
                'string',
            ],
            'locales.*.sub_title' => [
                'nullable',
                'string',
            ],
            'locales.*.button_text' => [
                'nullable',
                'string',
            ],
            'locales.*.url' => [
                'nullable',
                'string',
            ],
        ];
    }
}
