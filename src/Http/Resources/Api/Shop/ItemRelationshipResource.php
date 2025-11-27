<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ItemRelationshipResource extends BaseJsonResource
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
            'type' => $this->type,
            'sort_number' => $this->sort_number,
            
            // عرض بيانات المنتج "ذو الصلة"
            // (سيستخدم ItemResource تلقائيًا)
            'related_item' =>$this->whenLoaded('relatedItem', function () use ($request) {
                return resolve(ItemResource::class, ['resource' => $this->relatedItem])->toArray($request);
            }),
        ];
    }
}