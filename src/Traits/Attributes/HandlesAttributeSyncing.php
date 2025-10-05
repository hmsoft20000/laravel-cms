<?php

namespace HMsoft\Cms\Traits\Attributes;

use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Database\Eloquent\Model;

trait HandlesAttributeSyncing
{
    /**
     * Sync attribute values for a given model.
     *
     * @param Model $model The model to sync attribute values for.
     * @param array $valuesData The attribute values to sync.
     * @return void
     */
    protected function syncAttributeValues(Model $model, ?array $valuesData = null  ): void
    {
        if (!method_exists($model, 'attributeValues')) return;

        $model->attributeValues()->delete();
        if ($valuesData === null) return;

        // Assuming the model has a 'type' or 'scope' property
        $scope = $model->type ?? $model->scope ?? null;
        if (!$scope) return;

        $attributeIds = collect($valuesData)->pluck('attribute_id')->unique()->all();
        $attributes = Attribute::whereIn('id', $attributeIds)
            ->where('scope', $scope)
            ->get()->keyBy('id');

        foreach ($valuesData as $data) {
            if (!isset($attributes[$data['attribute_id']])) continue;

            $attribute = $attributes[$data['attribute_id']];
            $value = $data['value'];

            if ($attribute->type === 'checkbox') {
                if (is_array($value) && !empty($value)) {
                    $valueContainer = $model->attributeValues()->create(['attribute_id' => $attribute->id, 'value' => null]);
                    $selectedOptions = collect($value)->map(fn($id) => ['attribute_option_id' => $id]);
                    $valueContainer->selectedOptions()->createMany($selectedOptions->all());
                }
            } else {
                $model->attributeValues()->create([
                    'attribute_id' => $attribute->id,
                    'locale' => in_array($attribute->type, ['text', 'textarea']) ? ($data['locale'] ?? null) : null,
                    'value' => $value,
                ]);
            }
        }
    }
}
