<?php

namespace HMsoft\Cms\Http\Requests\Slider;

use HMsoft\Cms\Enums\SliderTypeEnum;
use HMsoft\Cms\Models\Slider\SliderTranslation;
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
            'id' =>  $this->route()?->originalParameter('slider'),
            'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function messages()
    {
        $file = 'cms.sliders.validation.update.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.sliders.validation.update.attributes';
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
            'id' => [Rule::exists('sliders', 'id')],
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
                Rule::in(SliderTypeEnum::values())
            ],
            'locales' => [
                'array',
            ],
            'locales.*.locale' => [
                'required',
            ],
            'locales.*.title' => [
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

        foreach (($this->locales ?? []) as $index => $locale) {
            $translate = SliderTranslation::where('slider_id', $this->id)->where('locale', $locale['locale'])->first();
            // locale
            $rules["locales.$index.locale"] = [];
            $rules["locales.$index.locale"][] = 'string';
            $rules["locales.$index.locale"][] =  Rule::unique('sliders_translations', 'locale')->where('slider_id', $this->id)->ignore($translate->id);
        }
        return  $rules;
    }
}
