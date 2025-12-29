<?php

namespace HMsoft\Cms\Traits\Shop;

trait ItemVariationValidationRules
{
    /**
     * القواعد المشتركة للإنشاء والتعديل
     */
    protected function commonRules(): array
    {
        return [
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer',
            'manage_stock' => 'boolean',
            'is_active' => 'boolean',
            'sku' => 'nullable|string|max:255', // إذا كان لديك SKU

            // التحقق من خيارات الخصائص (Attribute Options)
            // هذه المصفوفة تحتوي على IDs لخيارات مثل: أحمر، كبير، قطن
            'attribute_options' => 'required|array|min:1',
            'attribute_options.*' => 'required|integer|exists:attribute_options,id',
        ];
    }

    public function storeRules(): array
    {
        return $this->commonRules();
    }

    public function updateRules(): array
    {
        // في التعديل القواعد هي نفسها
        return $this->commonRules();
    }
}
