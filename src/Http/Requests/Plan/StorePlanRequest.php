<?php

namespace HMsoft\Cms\Http\Requests\Plan;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Plans\PlanValidationRules;

class StorePlanRequest extends MyRequest
{
    use PlanValidationRules;

    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {

        foreach (['is_active', 'is_featured'] as $key) {
            if (isset($this->{$key})) {
                $this->merge([
                    "{$key}" => filter_var($this->{$key}, FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function rules(): array
    {

        $rules = $this->getPlanRules('create');

        return $rules;

        // $rules = [
        //     'is_active' => ['required', 'boolean'],
        //     'sort_number' => ['required', 'integer'],
        //     'price' => ['sometimes', 'min:0'],
        //     'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
        //     'is_featured' => ['required', 'boolean'],
        //     'locales' => ['required', 'array', 'min:1'],
        //     'locales.*.locale' => ['required', 'string'],
        //     'locales.*.name' => ['nullable', 'string', 'max:255'],
        //     'locales.*.description' => ['nullable', 'string'],

        //     // features
        //     'features' => ['sometimes', 'array'],
        //     'features.*.locales.*.locale' => ['required', 'string'],
        //     'features.*.price' => ['required', 'min:0'],
        //     'features.*.locales.*.name' => ['sometimes', 'nullable', 'required', 'string'],
        // ];

        // return $rules;
    }
}
