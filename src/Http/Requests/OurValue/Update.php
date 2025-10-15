<?php

namespace HMsoft\Cms\Http\Requests\OurValue;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\OurValue\OurValueValidationRules;

class Update extends MyRequest
{
    use OurValueValidationRules;

    public function authorize(): bool
    {
        return true;
        // return $this->user()->can('update', $this->route('our_value'));
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge(
            $this->getOurValueRules('update'),
            $this->getSingleImageValidationRules()
        );
    }
}
