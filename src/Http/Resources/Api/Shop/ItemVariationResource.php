<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
// سنقوم بإعادة استخدام الريسورس الخاص بخيارات الخصائص الموجود مسبقًا
use HMsoft\Cms\Http\Resources\Api\AttributeOptionResource;
use Illuminate\Http\Request;

class ItemVariationResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {
        $defaultImageFromConfig = config("app.web_config.default_item_image");

        return [
            'id' => $this->id,
            'price' => $this->price,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'manage_stock' => $this->manage_stock,
            'is_active' => $this->is_active,

            // عرض الخصائص التي تكوّن هذا التوليف (مثل: أحمر، كبير)
            // 'attribute_options' => AttributeOptionResource::collection($this->whenLoaded('attributeOptions')),
            'attribute_options' => $this->whenLoaded('attributeOptions', function () use ($request) {
                return $this->attributeOptions->map(function ($item) use ($request) {
                    return resolve(AttributeOptionResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'image_url' => $this->whenLoaded('media', function () use ($defaultImageFromConfig) {
                $defaultImage = collect($this->media)->where('is_default', true)->first();
                return $defaultImage ? $defaultImage->file_url : $defaultImageFromConfig;
            }),
            'images' => $this->whenLoaded('media', function () {
                return collect($this->media)->sortBy('sort_number')?->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'image_url' => $medium->file_url,
                        'is_default' => $medium->is_default,
                        'sort_number' => $medium->sort_number,
                    ];
                })->all();
            }),
            'image_urls' => $this->whenLoaded('media', function () {
                return collect($this->media)->sortBy('sort_number')?->map(function ($medium) {
                    return $medium->file_url;
                })->all();
            }),
            'image' => $this->whenLoaded('media', function () {
                return collect($this->media)->where('is_default', true)?->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'image_url' => $medium->file_url,
                        'is_default' => $medium->is_default,
                        'sort_number' => $medium->sort_number,
                    ];
                })->first();
            }),
        ];
    }
}
