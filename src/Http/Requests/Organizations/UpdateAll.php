<?php

namespace HMsoft\Cms\Http\Requests\Organizations;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Organizations\Organization;
use HMsoft\Cms\Traits\Organizations\OrganizationValidationRules;
use Illuminate\Validation\Rule;

class UpdateAll extends MyRequest
{
    use OrganizationValidationRules;


    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $singleRules = $this->getOrganizationRules('update');
        $tableName = resolve(Organization::class)->getTable();
        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => [
                'required',
                'integer',
                Rule::exists($tableName, 'id')->where('type', $this->route('type'))
            ],
        ];

        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }
        return $rulesForAll;
    }
}
