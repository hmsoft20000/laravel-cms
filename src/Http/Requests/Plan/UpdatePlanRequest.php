<?php

namespace HMsoft\Cms\Http\Requests\Plan;

use HMsoft\Cms\Http\Requests\MyRequest;
use HMsoft\Cms\Traits\Plans\PlanValidationRules;

class UpdatePlanRequest extends MyRequest
{
    use PlanValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['is_active', 'is_featured', 'delete_image'] as $key) {
            if ($this->has($key)) {
                $this->merge([
                    $key => filter_var($this->{$key}, FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return $this->getPlanRules('update');

        // return [
        //     'is_active' => ['sometimes', 'boolean'],
        //     'sort_number' => ['sometimes', 'integer'],
        //     'price' => ['sometimes', 'numeric', 'min:0'],
        //     'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
        //     'is_featured' => ['sometimes', 'boolean'],
        //     'delete_image' => ['sometimes', 'boolean'],
        //     'locales' => ['sometimes', 'array', 'min:1'],
        //     'locales.*.locale' => ['required', 'string'],
        //     'locales.*.name' => ['sometimes', 'string', 'max:255'],
        //     'locales.*.description' => ['nullable', 'string'],

        //     // features
        //     'features' => ['sometimes', 'array'],
        //     'features.*.id' => ['sometimes', 'integer', 'exists:plan_features,id'],
        //     'features.*.price' => ['sometimes', 'numeric', 'min:0'],
        //     'features.*.locales' => ['sometimes', 'array'],
        //     'features.*.locales.*.locale' => ['required', 'string'],
        //     'features.*.locales.*.name' => ['sometimes', 'nullable', 'string'],
        // ];
    }
}
