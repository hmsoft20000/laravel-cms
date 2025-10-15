<?php

namespace HMsoft\Cms\Http\Requests\OurValue;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;
use HMsoft\Cms\Traits\OurValue\OurValueValidationRules;

class Store extends MyRequest
{
    use  OurValueValidationRules, ValidatesSingleMedia;

    public function authorize(): bool
    {
        return true;
        // return $this->user()->can('create', \HMsoft\Cms\Models\OurValue\OurValue::class);
    }

    public function prepareForValidation()
    {
        foreach (['is_active'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge(
            $this->getOurValueRules('create'),
            $this->getSingleImageValidationRules()
        );
    }
}
