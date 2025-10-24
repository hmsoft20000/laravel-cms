<?php

namespace HMsoft\Cms\Traits\Attributes;

use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatesCustomAttributes
{
    /**
     * @var \Illuminate\Support\Collection|null
     */
    protected $requiredAttributes = null;

    /**
     * @var array
     */
    protected $failedAttributeMap = [];

    /**
     * قواعد التحقق من الـ attribute values
     */
    protected function getAttributeValidationRules(string $type, string $prefix = null): array
    {
        $rules = [];

        $rules['attribute_values'] = ['nullable', 'array'];
        $rules['attribute_values.*.attribute_id'] = [
            'required',
            'integer',
            Rule::exists('attributes', 'id')->where('scope', $type),
        ];
        $rules['attribute_values.*.locale'] = ['nullable', 'string'];
        $rules['attribute_values.*.value'] = ['nullable'];

        $this->requiredAttributes = Attribute::where('scope', $type)
            ->where('is_required', true)
            ->get();

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
                    $ruleList[] = $attribute->isRequired() ? 'required' : 'nullable';

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
                        case 'datetime':
                            $ruleList[] = 'date';
                            break;
                        case 'year':
                            $ruleList[] = 'digits:4';
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

    /**
     * يربط مع الـ Validator للتأكد من وجود الـ required attributes
     */
    public function withCustomAttributesValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = collect($this->input('attribute_values', []));
            $missing = [];

            foreach ($this->requiredAttributes ?? [] as $attr) {
                $found = $items->contains(function ($item) use ($attr) {
                    return isset($item['attribute_id']) && (int)$item['attribute_id'] === (int)$attr->id;
                });

                if (!$found) {
                    // dd($attr->id);
                    // $missing[$attr->id][] = __("The field is required.");
                    $missing[$attr->id][] = __("The field is required.");
                    // $missing[$attr->id][] = __("The field is required.");
                    // $missing[$attr->id][] = __("The field ':label' is required.", [
                    //     'label' => $attr->label ?? $attr->name ?? "attribute_{$attr->id}",
                    // ]);
                }
            }

            if (!empty($missing)) {
                $this->failedAttributeMap = $missing;
                $validator->errors()->add('attribute_values', 'Some required custom attributes are missing.');
            }
        });
    }

    /**
     * يدمج الأخطاء الخاصة بالـ attributes المفقودة ويعيدها بطريقة منظمة
     */
    protected function failedCustomAttributesValidation(Validator $validator)
    {
        $allErrors = $validator->errors()->toArray();
        $attributeErrors = [];

        $values = collect($this->input('attribute_values', []));
        $indexToId = [];
        foreach ($values as $i => $val) {
            if (isset($val['attribute_id'])) {
                $indexToId[$i] = (int)$val['attribute_id'];
            }
        }

        foreach ($allErrors as $key => $messages) {
            if (preg_match('/attribute_values\.(\d+)\./', $key, $matches)) {
                $index = (int)$matches[1];
                $attrId = $indexToId[$index] ?? null;
                if ($attrId) {
                    foreach ((array)$messages as $msg) {
                        /** @var string $msg */
                        $msg = $msg;
                        // remove key from the msg
                        $msg = str_replace($key, '', $msg);
                        // remove 'لـ from the msg
                        $msg = str_replace(' لـ ', '', $msg);
                        $attributeErrors[$attrId][] = $msg;
                    }
                }
            }
        }

        if (!empty($this->failedAttributeMap)) {
            foreach ($this->failedAttributeMap as $attrId => $msgs) {
                foreach ((array)$msgs as $msg) {
                    $attributeErrors[$attrId][] = $msg;
                }
            }
        }

        $cleanErrors = [];
        foreach ($allErrors as $key => $messages) {
            if (!str_starts_with($key, 'attribute_values.')) {
                $cleanErrors[$key] = $messages;
            }
        }

        $cleanErrors['attribute_values_map'] = (object) $attributeErrors;

        $response = response()->json([
            'message' => 'Validation failed',
            'errors' => $cleanErrors,
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
