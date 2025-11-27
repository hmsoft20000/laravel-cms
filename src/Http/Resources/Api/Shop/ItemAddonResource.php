<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ItemAddonResource extends BaseJsonResource
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
            'price' => $this->price,
            'is_required' => $this->is_required,
            'sort_number' => $this->sort_number,

            // سيتم تحميل الترجمات الخاصة بهذه الإضافة
            // وسيقوم 'BaseJsonResource' تلقائيًا بمعالجتها
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),

            // سيستخدم هذا 'ItemAddonOptionResource'
            // لتنسيق كل "خيار" داخل هذه الإضافة
            'options' => collect($this->whenLoaded('options'))->map(function ($option) use ($request) {
                return resolve(ItemAddonOptionResource::class, ['resource' => $option])->toArray($request);
            }),
        ];
    }
}
