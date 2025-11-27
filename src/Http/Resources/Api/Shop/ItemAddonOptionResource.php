<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ItemAddonOptionResource extends BaseJsonResource
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
            'price' => $this->price,
            'is_default' => $this->is_default,
            'sort_number' => $this->sort_number,

            // سيتم تحميل ومعالجة الترجمات تلقائيًا
            'translations' => $this->whenLoaded('translations'),
        ];
    }
}
