<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\Api\Shop\ItemResource;
use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {

        return [
            'id' => $this->id,
            'sector_id' => $this->sector_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sector' => $this->whenLoaded('sector', function () {
                return resolve(SectorResource::class, ['resource' => $this->sector]);
            }),
            
            // 'posts' => $this->whenLoaded('posts', function () {
            //     return  resolve(PostResource::class, ['resource' => $this->posts]);
            // }),
            'portfolios' => $this->whenLoaded('portfolios', function () {
                return $this->portfolios->map(function ($portfolio) {
                    return resolve(PortfolioResource::class, ['resource' => $portfolio]);
                });
            }),
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(function ($service) {
                    return resolve(ServiceResource::class, ['resource' => $service]);
                });
            }),
            'blogs' => $this->whenLoaded('blogs', function () {
                return $this->blogs->map(function ($blog) {
                    return resolve(BlogResource::class, ['resource' => $blog]);
                });
            }),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return resolve(ItemResource::class, ['resource' => $item]);
                });
            }),
            'posts_count' => $this->posts_count,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
