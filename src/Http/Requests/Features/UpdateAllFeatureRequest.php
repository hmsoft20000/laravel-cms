<?php

namespace HMsoft\Cms\Http\Requests\Features;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Shared\Feature;
use HMsoft\Cms\Traits\Features\FeatureValidationRules;

class UpdateAllFeatureRequest extends MyRequest
{
    use FeatureValidationRules;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $singleRules = $this->getFeatureRules('update');
        $tableName = (new Feature())->getTable();
        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:' . $tableName . ',id'],
        ];

        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }
}
