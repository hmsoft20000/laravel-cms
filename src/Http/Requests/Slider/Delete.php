<?php

namespace HMsoft\Cms\Http\Requests\Slider;

use HMsoft\Cms\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class Delete extends MyRequest
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
            'id' =>  $this->route()?->originalParameter('slider'),
        ]);
    }

    public function messages()
    {
        return trans('cms::sliders.validation.delete.messages');
    }

    public function attributes()
    {
        return trans('cms::sliders.validation.delete.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => [Rule::exists('sliders', 'id')],
        ];
    }
}
