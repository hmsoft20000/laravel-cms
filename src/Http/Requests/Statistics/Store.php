<?php

namespace HMsoft\Cms\Http\Requests\Statistics;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Media\ValidatesSingleMedia;
use HMsoft\Cms\Traits\Statistics\StatisticsValidationRules;

class Store extends MyRequest
{

    use StatisticsValidationRules, ValidatesSingleMedia;

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
        foreach (['is_active'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function messages()
    {
        $file = 'cms.statistics.validation.store.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.statistics.validation.store.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return array_merge(
            $this->getStatisticsRules('create'),
            $this->getSingleImageValidationRules()
        );
    }
}
