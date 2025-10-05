<?php

namespace HMsoft\Cms\Http\Requests\Testimonial;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Testimonial\TestimonialValidationRule;

class Update extends MyRequest
{
    use TestimonialValidationRule;
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
        $booleanFields = ['is_active'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function messages()
    {
        return trans('cms::testimonials.validation.update.messages');
    }

    public function attributes()
    {
        return trans('cms::testimonials.validation.update.attributes');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = $this->getTestimonialRules('update');

        return  $rules;
    }
}
