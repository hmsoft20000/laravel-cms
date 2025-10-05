<?php

namespace HMsoft\Cms\Traits\Plans;

/**
 * Trait PlanValidationRules
 *
 * يوفر هذا الـ Trait مجموعة مركزية من قواعد التحقق من الصحة (validation rules)
 * التي يتم مشاركتها بين StorePlanRequest و UpdatePlanRequest.
 */
trait PlanValidationRules
{
    /**
     * Get the shared validation rules for a plan based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getPlanRules(string $context = 'update'): array
    {
        // =================================================================
        // القواعد الأساسية المشتركة بين الإنشاء والتحديث
        // =================================================================
        $rules = [
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'image'         => ['sometimes', 'nullable', 'image', 'max:2048'],

            // قواعد الترجمات
            'locales.*.locale'      => ['required', 'string'],
            'locales.*.name'        => ['required', 'string', 'max:255'],
            'locales.*.description' => ['nullable', 'string'],

            // قواعد الميزات (features) الخاصة بالخطة
            'features'                          => ['sometimes', 'array'],
            'features.*.price'                  => ['required_with:features', 'numeric', 'min:0'],
            'features.*.locales'                => ['required_with:features', 'array'],
            'features.*.locales.*.locale'       => ['required_with:features', 'string'],
            'features.*.locales.*.name'         => ['required_with:features', 'string', 'max:255'],
        ];

        // =================================================================
        // القواعد التي تتغير بناءً على السياق (إنشاء أو تحديث)
        // =================================================================
        if ($context === 'create') {
            // --- في حالة الإنشاء، الحقول التالية إلزامية ---
            $rules['is_active']   = ['sometimes', 'boolean'];
            $rules['is_featured'] = ['sometimes', 'boolean'];
            $rules['sort_number'] = ['sometimes', 'integer'];
            $rules['locales']     = ['required', 'array', 'min:1'];
        } else { // $context === 'update'
            // --- في حالة التحديث، الحقول التالية اختيارية ---
            $rules['is_active']      = ['sometimes', 'boolean'];
            $rules['is_featured']    = ['sometimes', 'boolean'];
            $rules['sort_number']    = ['sometimes', 'integer'];
            $rules['locales']        = ['sometimes', 'array', 'min:1'];

            // --- قواعد خاصة بالتحديث فقط ---
            $rules['delete_image']   = ['sometimes', 'boolean'];
            $rules['features.*.id']  = ['sometimes', 'integer', 'exists:plan_features,id']; // للسماح بتحديث الميزات الموجودة
        }

        return $rules;
    }

    /**
     * Get the validation rules for a nested array of plans.
     * This is the new helper method.
     */
    protected function getNestedPlanRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getPlanRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
