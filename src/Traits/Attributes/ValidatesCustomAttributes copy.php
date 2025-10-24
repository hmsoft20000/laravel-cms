<?php

namespace HMsoft\Cms\Traits\Attributes;

use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Validation\Rule;

trait ValidatesCustomAttributes
{
    /**
     * @var \Illuminate\Support\Collection|null
     * سنخزن هنا الـ required attributes ليستعملها withValidator
     */
    protected $requiredAttributes = null;

    /**
     * @var array|null
     * سنخزن هنا الأخطاء التفصيلية للـ attributes المفقودة
     */
    protected $missingAttributeErrors = null;

    /**
     * Get the dynamic validation rules for attribute values.
     *
     * @return array
     */
    protected function getAttributeValidationRules(string $type, string $prefix = null): array
    {
        $rules = [];

        // نطلب أن تكون attribute_values مصفوفة إذا وُجدت، لكن لا نُجبر وجودها هنا
        // لأن حتى لو لم تُرسل المصفوفة سنتحقق بعدها من الـ required attributes.
        $rules['attribute_values'] = ['nullable', 'array'];

        // بنية كل عنصر إذا وُوجد
        $rules['attribute_values.*.attribute_id'] = [
            'required',
            'integer',
            Rule::exists('attributes', 'id')->where('scope', $type),
        ];
        $rules['attribute_values.*.locale'] = ['nullable', 'string'];
        $rules['attribute_values.*.value'] = ['nullable'];

        // **هنا نجلب كل الـ attributes المطلوبة من قاعدة البيانات**
        $this->requiredAttributes = Attribute::where('scope', $type)
            ->where('is_required', true)
            ->get();

        // بناء قواعد للقيم المرسلة (لو وُجدت) بنفس المنطق السابق
        $attributeIds = collect($this->attribute_values ?? [])->pluck('attribute_id')->unique()->all();

        if (!empty($attributeIds)) {
            $attributes = Attribute::whereIn('id', $attributeIds)
                ->where('scope', $type)
                ->with('translations')
                ->get()
                ->keyBy('id');

            foreach ($this->attribute_values ?? [] as $index => $attrValue) {
                if (isset($attributes[$attrValue['attribute_id']])) {
                    $attribute = $attributes[$attrValue['attribute_id']];
                    $rulePath = "attribute_values.$index.value";

                    $ruleList = [];
                    $ruleList[] = $attribute->is_required ? 'required' : 'nullable';

                    switch ($attribute->type) {
                        case 'url':
                            $ruleList[] = 'url';
                            break;
                        case 'select':
                        case 'radio':
                            $ruleList[] = 'integer';
                            $ruleList[] = Rule::exists('attribute_options', 'id')
                                ->where('attribute_id', $attribute->id);
                            break;
                        case 'checkbox':
                            $ruleList[] = 'array';
                            $rules["$rulePath.*"] = [
                                'integer',
                                Rule::exists('attribute_options', 'id')->where('attribute_id', $attribute->id),
                            ];
                            break;
                        case 'number':
                            $ruleList[] = 'numeric';
                            break;
                        case 'date':
                            $ruleList[] = 'date';
                            break;
                        case 'boolean':
                            $ruleList[] = 'boolean';
                            break;
                        default:
                            $ruleList[] = 'string';
                            break;
                    }

                    $rules[$rulePath] = $ruleList;
                }
            }
        }

        return $rules;
    }
}
