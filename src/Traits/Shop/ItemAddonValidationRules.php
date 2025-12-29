<?php

namespace HMsoft\Cms\Traits\Shop;

use Illuminate\Validation\Rule;

trait ItemAddonValidationRules
{
    /**
     * القواعد المشتركة للإنشاء والتعديل
     */
    protected function commonRules(): array
    {
        return [
            'type' => ['required', Rule::in(['select', 'radio', 'checkbox', 'text', 'textarea', 'boolean'])],
            'price' => 'nullable|numeric|min:0',
            'is_required' => 'boolean',
            'sort_number' => 'integer',

            // تحقق الترجمات (للـ Addon نفسه)
            'locales' => 'required|array|min:1',
            'locales.*.locale' => 'required|string',
            'locales.*.title' => 'nullable',

            // تحقق الخيارات (Options) داخل الـ Addon
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|integer', // للتعديل
            'options.*.price' => 'nullable|numeric|min:0',
            'options.*.is_default' => 'boolean',
            'options.*.sort_number' => 'nullable|integer',

            // تحقق ترجمات الخيارات
            'options.*.locales' => 'required_with:options|array',
            'options.*.locales.*.locale' => 'required|string',
            'options.*.locales.*.title' => 'nullable',
        ];
    }

    public function storeRules(): array
    {
        return $this->commonRules();
    }

    public function updateRules(): array
    {
        // في التعديل، قد نحتاج لتجاهل بعض القواعد أو تعديلها إذا لزم الأمر
        // حالياً هي مطابقة للإنشاء
        return $this->commonRules();
    }
}
