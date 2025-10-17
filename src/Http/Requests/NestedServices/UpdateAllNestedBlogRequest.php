<?php

namespace HMsoft\Cms\Http\Requests\NestedServices;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Content\Service;
use HMsoft\Cms\Traits\Services\ServiceValidationRules;

class UpdateAllNestedServiceRequest extends MyRequest
{
    use ServiceValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request for a bulk update.
     */
    public function rules(): array
    {
        $singleRules = $this->getServiceRules('update');
        $tableName = resolve(Service::class)->getTable();

        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:' . $tableName . ',id'],
        ];

        // Apply the single service update rules to each item in the array.
        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }
}
