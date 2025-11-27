<?php

namespace HMsoft\Cms\Traits\Shop;

use HMsoft\Cms\Models\Shop\Item;
use Illuminate\Support\Arr;

trait HandlesItemRelationshipSyncing
{
    protected function syncRelationships(Item $item, ?array $relationshipsData): void
    {
        if ($relationshipsData === null) return;

        $existingIds = [];

        foreach ($relationshipsData as $relData) {
            // $relId = $relData['id'] ?? null;

            // الربط يتم بـ related_item_id
            $relationship = $item->relationships()->updateOrCreate(
                [
                    'related_item_id' => $relData['related_item_id'],
                    'type' => $relData['type']
                ],
                Arr::except($relData, ['id'])
            );
            $existingIds[] = $relationship->id;
        }

        $item->relationships()->whereNotIn('id', $existingIds)->delete();
    }
}
