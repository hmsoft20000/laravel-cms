<?php

namespace HMsoft\Cms\Traits\Attributes;

use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Validation\Rule;

trait ValidatesCustomAttributes
{
    /**
     * Get the dynamic validation rules for attribute values.
     *
     * @return array
     */
    protected function getAttributeValidationRules(): array
    {
        $rules = [];
        $postType = $this->input('type'); // Assumes 'type' is present

        // Validate the top-level attribute_values array
        $rules['attribute_values'] = ['sometimes', 'array'];
        $rules['attribute_values.*.attribute_id'] = [
            'required',
            'integer',
            Rule::exists('attributes', 'id')->where('scope', $postType)
        ];
        $rules['attribute_values.*.locale'] = ['nullable', 'string'];
        $rules['attribute_values.*.value'] = ['nullable'];

        // Dynamically add rules based on each attribute's type
        $attributeIds = collect($this->attribute_values ?? [])->pluck('attribute_id')->unique()->all();

        if (empty($attributeIds)) {
            return $rules;
        }

        $attributes = Attribute::whereIn('id', $attributeIds)->where('scope', $postType)->get()->keyBy('id');

        foreach ($this->attribute_values ?? [] as $index => $attrValue) {
            if (isset($attributes[$attrValue['attribute_id']])) {
                $attribute = $attributes[$attrValue['attribute_id']];
                $rulePath = "attribute_values.$index.value";

                switch ($attribute->type) {
                    case 'url':
                        $rules[$rulePath] = ['required', 'url'];
                        break;
                    case 'select':
                    case 'radio':
                        $rules[$rulePath] = ['required', 'integer', Rule::exists('attribute_options', 'id')
                            ->where('attribute_id', $attribute->id)];
                        break;
                    case 'checkbox':
                        $rules[$rulePath] = ['required', 'array'];
                        $rules["$rulePath.*"] = ['required', 'integer', Rule::exists('attribute_options', 'id')
                            ->where('attribute_id', $attribute->id)];
                        break;
                    case 'number':
                        $rules[$rulePath] = ['required', 'numeric'];
                        break;
                    case 'date':
                        // $rules[$rulePath] = ['required', 'date_format:Y-m-d'];
                        $rules[$rulePath] = ['required', 'date'];
                        break;
                    case 'boolean':
                        $rules[$rulePath] = ['required', 'boolean'];
                        break;
                    default: // text, textarea, wysiwyg, color, etc.
                        $rules[$rulePath] = ['required', 'string'];
                        break;
                }
            }
        }

        return $rules;
    }
}
