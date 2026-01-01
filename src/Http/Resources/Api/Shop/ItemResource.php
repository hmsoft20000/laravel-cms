<?php

namespace HMsoft\Cms\Http\Resources\Api\Shop;

use HMsoft\Cms\Http\Resources\Api\BlogResource;
use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

// --- ريسورسز موجودة مسبقًا في الحزمة ---
use HMsoft\Cms\Http\Resources\Api\CategoryResource;
use HMsoft\Cms\Http\Resources\Api\DownloadItemResource;
use HMsoft\Cms\Http\Resources\Api\FeatureResource;
use HMsoft\Cms\Http\Resources\Api\DownloadResource;
use HMsoft\Cms\Http\Resources\Api\FaqResource;
use HMsoft\Cms\Http\Resources\Api\PlanResource;
use HMsoft\Cms\Http\Resources\Api\OrganizationResource;

// --- ريسورسز جديدة خاصة بالمتجر ---
use HMsoft\Cms\Http\Resources\Api\Shop\ItemVariationResource;
use HMsoft\Cms\Http\Resources\Api\Shop\ItemAddonResource;
use HMsoft\Cms\Http\Resources\Api\Shop\ItemJoinResource;
use HMsoft\Cms\Http\Resources\Api\Shop\ItemRelationshipResource;
use HMsoft\Cms\Models\Shared\DownloadItem;

class ItemResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {
        // إعداد صورة افتراضية للمنتجات
        $defaultImageFromConfig = config("app.web_config.default_item_image");

        return [
            'id' => $this->id,
            'type' => $this->type,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'manage_stock' => $this->manage_stock,
            'is_virtual' => $this->is_virtual,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'discount'=>$this->discount,
            'discount_type'=>$this->discount_type,
            // --- علاقات الـ Traits (مثل BlogResource) ---
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
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
            'keywords' => $this->whenLoaded('keywords', function () {
                return collect($this->keywords)->pluck('keyword')->all();
            }),
            'categories' => $this->whenLoaded('categories', function () use ($request) {
                return $this->categories->map(function ($category) use ($request) {
                    return resolve(CategoryResource::class, ['resource' => $category])->toArray($request);
                });
            }),
            'features' => $this->whenLoaded('features', function () use ($request) {
                return collect($this->features)->map(function ($item) use ($request) {
                    return resolve(FeatureResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'downloads' => $this->whenLoaded('downloads', function () use ($request) {
                return collect($this->downloads)->map(function ($item) use ($request) {
                    return resolve(DownloadItemResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'faqs' => $this->whenLoaded('faqs', function () use ($request) {
                return collect($this->faqs)->map(function ($item) use ($request) {
                    return resolve(FaqResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'plans' => $this->whenLoaded('plans', function () use ($request) {
                return collect($this->plans)->map(function ($item) use ($request) {
                    return resolve(PlanResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'partners' => $this->whenLoaded('partners', function () use ($request) {
                return collect($this->partners)->map(function ($item) use ($request) {
                    return resolve(OrganizationResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'sponsors' => $this->whenLoaded('sponsors', function () use ($request) {
                return collect($this->sponsors)->map(function ($item) use ($request) {
                    return resolve(OrganizationResource::class, ['resource' => $item])->toArray($request);
                });
            }),

            // --- New E-commerce Relations ---
            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return $this->formatAndGroupAttributeValues($this->resource);
            }),

            'variations' => $this->whenLoaded('variations', function () use ($request) {
                // You'll want to create an ItemVariationResource for this
                return collect($this->variations)->map(function ($variation) use ($request) {
                    return resolve(ItemVariationResource::class, ['resource' => $variation])->toArray($request);
                });
            }),
            'addons' => $this->whenLoaded('addons', function () use ($request) {
                // You'll want to create an ItemAddonResource for this
                return collect($this->addons)->map(function ($addon) use ($request) {
                    return resolve(ItemAddonResource::class, ['resource' => $addon])->toArray($request);
                });
            }),
            'child_items' => $this->whenLoaded('childItems', function () use ($request) {
                // Format bundled items
                return collect($this->childItems)->map(function ($item) use ($request) {
                    return resolve(ItemJoinResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'blogs' => $this->whenLoaded('blogs', function () use ($request) {
                return collect($this->blogs)->map(function ($item) use ($request) {
                    return resolve(BlogResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'relationships' => $this->whenLoaded('relationships', function () use ($request) {
                // Format related items
                return collect($this->relationships)->map(function ($item) use ($request) {
                    return resolve(ItemRelationshipResource::class, ['resource' => $item])->toArray($request);
                });
            }),
        ];
    }
}
