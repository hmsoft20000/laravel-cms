<?php

namespace HMsoft\Cms\Http\Requests\Faqs;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Models\Shared\Faq;
use HMsoft\Cms\Traits\Faqs\FaqValidationRules;

class UpdateAllFaqRequest extends MyRequest
{
    use FaqValidationRules;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $singleRules = $this->getFaqRules('update');
        $tableName = (new Faq())->getTable();
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
