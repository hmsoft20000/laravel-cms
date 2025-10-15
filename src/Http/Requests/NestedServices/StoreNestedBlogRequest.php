<?php

namespace HMsoft\Cms\Http\Requests\NestedServices;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Services\ServiceValidationRules;

class StoreNestedServiceRequest extends MyRequest
{
    use ServiceValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here later based on the owner model.
        // For example: return $this->user()->can('createServicesFor', $this->route('owner'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * We only need the core service rules here. Nested relationships like
     * features or downloads are handled by their own dedicated endpoints.
     */
    public function rules(): array
    {
        return $this->getServiceRules('create');
    }
}
