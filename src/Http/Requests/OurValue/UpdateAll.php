<?php

namespace HMsoft\Cms\Http\Requests\OurValue;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\OurValue\OurValueValidationRules;

class UpdateAll extends MyRequest
{
    use OurValueValidationRules;

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
        return trans('cms::statistics.validation.update_all.messages');
    }

    public function attributes()
    {
        return trans('cms::statistics.validation.update_all.attributes');
    }

    public function rules(): array
    {
        $singleRules = array_merge(
            $this->getOurValueRules('update'),
            $this->getSingleImageValidationRules()
        );

        $rulesForAll = [
            '*' => ['required', 'array'],
            '*.id' => ['required', 'integer', 'exists:our_values,id'],
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
                            __('cms::statistics.validation.update_all.at_least_one_title')
                        );
                    }
                }
            }
        });
    }
}
