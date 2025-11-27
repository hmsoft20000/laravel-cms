<?php

namespace HMsoft\Cms\Traits\Shop;

use HMsoft\Cms\Models\Shop\Item;
use Illuminate\Support\Arr;

trait HandlesItemJoinSyncing
{
    protected function syncJoins(Item $item, ?array $joinsData): void
    {
        if ($joinsData === null) return;

        $existingIds = [];

        foreach ($joinsData as $joinData) {
            $joinId = $joinData['id'] ?? null;

            // الربط يتم بـ child_item_id
            $join = $item->childItems()->updateOrCreate(
                ['id' => $joinId],
                Arr::except($joinData, ['id'])
            );
            $existingIds[] = $join->id;
        }

        // حذف المنتجات المربوطة (Joined) القديمة
        $item->childItems()->whereNotIn('id', $existingIds)->delete();
    }
}
