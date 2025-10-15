<?php

namespace HMsoft\Cms\Http\Requests\NestedServices;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Services\ServiceValidationRules;

class UpdateNestedServiceRequest extends MyRequest
{
    use ServiceValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here later.
        // For example: return $this->user()->can('update', $this->route('service'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->getServiceRules('update');
    }
}
