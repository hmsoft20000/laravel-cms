<?php

namespace HMsoft\Cms\Traits\Shop;

use Illuminate\Validation\Rule;

trait ItemValidationRules
{
    /**
     * Get the base validation rules for an Item.
     *
     * @param string $method 'create' or 'update'
     * @return array
     */
    protected function getItemRules(string $method): array
    {
        // [تعديل] جلب اللغات المتاحة من ملف الإعدادات
        $localesConfig = config('cms.locales', ['en' => 'English']); //
        $availableLocales = implode(',', array_keys($localesConfig)); // سيصبح "en,ar"

        $rules = [
            // == Base Item Details ==
            'type' => 'required|in:simple,variable,digital,service,grouped,bundled',
            'price' => 'required_if:type,simple,digital,service,bundled|nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'is_virtual' => 'boolean',
            'is_active' => 'boolean',

            // == [تعديل] قواعد التحقق من الترجمات (لتطابق مصفوفة الكائنات) ==
            'locales' => 'required|array|min:1',
            'locales.*.locale' => "required|string",
            'locales.*.title' => 'required|string|max:255',
            'locales.*.short_content' => 'nullable|string',
            'locales.*.content' => 'nullable|string',
            'locales.*.meta_title' => 'nullable|string|max:255',
            'locales.*.meta_description' => 'nullable|string',
            'locales.*.meta_keywords' => 'nullable|string',
            // تم تبسيط قاعدة الـ slug، والاعتماد على قيد قاعدة البيانات
            'locales.*.slug' => 'nullable|string|max:255',
        ];

        // [تعديل] تم نقل قاعدة SKU خارج اللوب (Loop)
        $skuRule = ['nullable', 'string', 'max:100'];
        if ($method === 'create') {
            $skuRule[] = Rule::unique('items', 'sku');
        } else {
            $itemId = $this->route('item')?->id;
            if ($itemId) {
                $skuRule[] = Rule::unique('items', 'sku')->ignore($itemId);
            }
        }
        $rules["sku"] = $skuRule; // SKU موجود في الجدول الأساسي

        // == E-commerce Relations Validation ==
        $rules = array_merge($rules, $this->getECommerceValidationRules());

        return $rules;
    }

    /**
     * Get validation rules for complex e-commerce relationships.
     *
     * @return array
     */
    protected function getECommerceValidationRules(): array
    {
        // [تعديل] جلب اللغات المتاحة لاستخدامها في القواعد المتداخلة
        $localesConfig = config('cms.locales', ['en' => 'English']);
        $availableLocales = implode(',', array_keys($localesConfig));

        return [
            // Variations
            'variations' => 'nullable|array|required_if:type,variable',
            'variations.*.id' => 'nullable|integer', // For updates
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.sku' => 'nullable|string|max:100',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.manage_stock' => 'boolean',
            'variations.*.is_active' => 'boolean',
            'variations.*.attribute_options' => 'required|array|min:1',
            'variations.*.attribute_options.*' => 'required|integer|exists:attribute_options,id',

            // Addons
            'addons' => 'nullable|array',
            'addons.*.id' => 'nullable|integer', // For updates
            'addons.*.type' => 'required|in:select,radio,checkbox,text,textarea,boolean',
            'addons.*.price' => 'nullable|numeric|min:0', // For text/boolean types
            'addons.*.is_required' => 'boolean',

            // [تعديل] قواعد الترجمة للإضافات
            'addons.*.locales' => 'required|array|min:1',
            'addons.*.locales.*.locale' => "required|string",
            'addons.*.locales.*.title' => 'required|string|max:255',

            'addons.*.options' => 'nullable|array', // For addon options
            'addons.*.options.*.id' => 'nullable|integer', // For updates
            'addons.*.options.*.price' => 'nullable|numeric|min:0',
            'addons.*.options.*.is_default' => 'boolean',

            // [تعديل] قواعد الترجمة لخيارات الإضافات
            'addons.*.options.*.locales' => 'required|array|min:1',
            'addons.*.options.*.locales.*.locale' => "required|string",
            'addons.*.options.*.locales.*.title' => 'required|string|max:255',

            // Bundled / Grouped
            'joins' => 'nullable|array',
            'joins.*.id' => 'nullable|integer', // For updates
            'joins.*.child_item_id' => 'required|integer|exists:items,id',
            'joins.*.quantity' => 'required|integer|min:1',

            // Marketing Relationships
            'relationships' => 'nullable|array',
            'relationships.*.id' => 'nullable|integer', // For updates
            'relationships.*.related_item_id' => 'required|integer|exists:items,id',
            'relationships.*.type' => 'required|in:related,upsell,cross-sell',
        ];
    }
}
