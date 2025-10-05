<?php

namespace HMsoft\Cms\Http\Requests\Downloads;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Traits\Downloads\DownloadValidationRules;

class UpdateAllDownloadRequest extends MyRequest
{
    use DownloadValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $singleRules = $this->getDownloadRules('update');
        $tableName = (new Download())->getTable();
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
