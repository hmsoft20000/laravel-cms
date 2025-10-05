<?php

namespace HMsoft\Cms\Traits\Faqs;

/**
 * Trait FaqValidationRules
 *
 * يوفر مجموعة مركزية من قواعد التحقق للأسئلة الشائعة (FAQs).
 */
trait FaqValidationRules
{
    /**
     * Get the shared validation rules for an FAQ based on the context.
     *
     * @param string $context The context of the validation ('create' or 'update').
     * @return array
     */
    protected function getFaqRules(string $context = 'update'): array
    {
        $rules = [];

        if ($context === 'create') {
            // --- في حالة الإنشاء، الحقول إلزامية ---
            $rules['is_active']   = ['sometimes', 'boolean'];
            $rules['sort_number'] = ['sometimes', 'integer'];
            $rules['locales']     = ['required', 'array', 'min:1'];
            $rules['locales.*.locale']      = ['required', 'string'];
            $rules['locales.*.question']    = ['required', 'string', 'max:255'];
            $rules['locales.*.answer']      = ['required', 'string'];
        } else { // $context === 'update'
            // --- في حالة التحديث، الحقول اختيارية ---
            $rules['is_active']      = ['sometimes', 'boolean'];
            $rules['sort_number']    = ['sometimes', 'integer'];
            $rules['locales']        = ['sometimes', 'array', 'min:1'];
            $rules['locales.*.locale']      = ['sometimes', 'string'];
            $rules['locales.*.question']    = ['sometimes', 'string', 'max:255'];
            $rules['locales.*.answer']      = ['sometimes', 'string'];
        }

        return $rules;
    }


    /**
     * Get the validation rules for a nested array of faqs.
     * This is the new helper method.
     */
    protected function getNestedFaqRules(string $prefix, string $context = 'update'): array
    {
        $baseRules = $this->getFaqRules($context);
        $nestedRules = [];

        foreach ($baseRules as $field => $rule) {
            $nestedRules[$prefix . $field] = $rule;
        }

        return $nestedRules;
    }
}
