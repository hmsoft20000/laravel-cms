<?php

namespace HMsoft\Cms\Http\Requests\Organizations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;

class Delete extends MyRequest
{
    use OrganizationValidationRules;
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
            'id' =>  $this->route()?->originalParameter('organization'),
        ]);
    }

    public function messages()
    {
        return trans('cms::organizations.validation.delete.messages');
    }

    public function attributes()
    {
        return trans('cms::organizations.validation.delete.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->getOrganizationRules('delete');
    }
}
