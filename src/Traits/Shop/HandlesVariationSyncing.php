<?php

namespace HMsoft\Cms\Traits\Shop;

use HMsoft\Cms\Models\Shop\Item;
use Illuminate\Support\Arr;

trait HandlesVariationSyncing
{
    protected function syncVariations(Item $item, ?array $variationsData): void
    {
        if ($variationsData === null) return;

        $existingIds = [];

        foreach ($variationsData as $variationData) {
            $variationId = $variationData['id'] ?? null;

            $variation = $item->variations()->updateOrCreate(
                ['id' => $variationId],
                Arr::except($variationData, ['id', 'attribute_options'])
            );

            // مزامنة خيارات الخصائص (Attribute Options)
            if (isset($variationData['attribute_options'])) {
                $variation->attributeOptions()->sync($variationData['attribute_options']);
            }

            $existingIds[] = $variation->id;
        }

        // حذف التوليفات القديمة
        $item->variations()->whereNotIn('id', $existingIds)->delete();
    }
}
