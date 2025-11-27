<?php

namespace HMsoft\Cms\Http\Requests\Statistics;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Statistics\StatisticsValidationRules;

class UpdateAll extends MyRequest
{
    use StatisticsValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $data = $this->all();
        foreach ($data as $index => $item) {
            if (isset($item['is_active'])) {
                $data[$index]['is_active'] = filter_var($item['is_active'], FILTER_VALIDATE_BOOLEAN);
            }
        }
        $this->merge($data);
    }

    public function messages()
    {
        $file = 'cms.statistics.validation.update_all.messages';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function attributes()
    {
        $file = 'cms.statistics.validation.update_all.attributes';
        return is_array(trans($file)) ? trans($file) : [];
    }

    public function rules(): array
    {
        $singleRules = array_merge(
            $this->getStatisticsRules('update'),
            $this->getSingleImageValidationRules()
        );

        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:statistics,id'],
        ];

        foreach ($singleRules as $field => $rule) {
            $rulesForAll['*.' . $field] = $rule;
        }

        return $rulesForAll;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            foreach ($data as $index => $item) {
                if (isset($item['locales'])) {
                    $hasAtLeastOneName = collect($item['locales'])
                        ->contains(fn($locale) => !empty($locale['title']));

                    if (! $hasAtLeastOneName) {
                        $validator->errors()->add(
                            "{$index}.locales.*.title",
                            trans('statistics.validation.update_all.at_least_one_title')
                        );
                    }
                }
            }
        });
    }
}
