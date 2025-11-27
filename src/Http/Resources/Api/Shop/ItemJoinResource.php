<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ItemJoinResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,

            // عرض بيانات المنتج "الابن" المربوط
            // (سيستخدم ItemResource تلقائيًا)
            'child_item' => new ItemResource($this->whenLoaded('childItem')),
        ];
    }
}
