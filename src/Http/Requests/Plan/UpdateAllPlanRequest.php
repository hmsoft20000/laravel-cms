<?php

namespace HMsoft\Cms\Http\Requests\Plan;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Shared\Plan;
use HMsoft\Cms\Traits\Plans\PlanValidationRules;

class UpdateAllPlanRequest extends MyRequest
{
    use PlanValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $singleRules = $this->getPlanRules('update');
        $tableName = (new Plan())->getTable();
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
