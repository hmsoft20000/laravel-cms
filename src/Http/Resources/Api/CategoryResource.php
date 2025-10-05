<?php

namespace HMsoft\Cms\Http\Resources\Api;

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
                return new SectorResource($this->sector);
            }),
            'posts' => $this->whenLoaded('posts', function () {
                return  PostResource::collection($this->posts);
            }),
            'portfolios' => $this->whenLoaded('portfolios', function () {
                return  PostResource::collection($this->portfolios);
            }),
            'posts_count' => $this->posts_count,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
