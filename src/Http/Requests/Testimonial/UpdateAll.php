<?php

namespace HMsoft\Cms\Http\Requests\Testimonial;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Testimonial\Testimonial;
use HMsoft\Cms\Traits\Testimonial\TestimonialValidationRule;

class UpdateAll extends MyRequest
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
        $data = $this->all();

        // Process each testimonial item in the array
        foreach ($data as $index => $item) {
            // Handle boolean fields for each item if any
            $booleanFields = ['is_active'];
            foreach ($booleanFields as $field) {
                if (isset($item[$field])) {
                    $data[$index][$field] = filter_var($item[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        $this->merge($data);
    }

    public function messages()
    {
        return trans('cms::testimonials.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::testimonials.validation.update_all.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $singleRules = $this->getTestimonialRules('update');
        $tableName = (new Testimonial())->getTable();
        $rulesForAll = [
            // '' => ['required', 'array', 'max:50'],
            '*.id' => ['required', 'integer', 'exists:' . $tableName . ',id'],
        ];

        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }
}
