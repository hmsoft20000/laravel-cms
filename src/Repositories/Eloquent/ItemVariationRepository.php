<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemVariation;
use HMsoft\Cms\Repositories\Contracts\ItemVariationRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ItemVariationRepository implements ItemVariationRepositoryInterface
{
    use FileManagerTrait;

    public function store(Item $item, array $data): Model
    {
        return DB::transaction(function () use ($item, $data) {
            // 1. إنشاء التوليفة (Variation)
            /** @var ItemVariation $variation */
            $variation = $item->variations()->create(
                Arr::except($data, ['attribute_options', 'images'])
            );

            // 2. ربط خيارات الخصائص (مثل: أحمر، كبير)
            if (!empty($data['attribute_options'])) {
                $variation->attributeOptions()->sync($data['attribute_options']);
            }
            return $variation->load(['attributeOptions.translations', 'attributeOptions.attribute.translations']);
        });
    }

    public function update(ItemVariation $variation, array $data): Model
    {
        return DB::transaction(function () use ($variation, $data) {
            // 1. تحديث البيانات
            $variation->update(
                Arr::except($data, ['attribute_options', 'images'])
            );

            // 2. تحديث الربط
            if (isset($data['attribute_options'])) {
                $variation->attributeOptions()->sync($data['attribute_options']);
            }

            return $variation->refresh()->load(['attributeOptions.translations', 'attributeOptions.attribute.translations']);
        });
    }

    public function delete(ItemVariation $variation): bool
    {
        return $variation->delete();
    }
}
