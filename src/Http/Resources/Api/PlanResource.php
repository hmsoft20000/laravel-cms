<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PlanResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function resolveData(Request $request)
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id, // Replaces portfolio_id
            'owner_type' => class_basename($this->owner_type), // e.g., 'Post'
            'price' => (float) $this->price,
            'currency_code' => $this->currency_code,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_number' => $this->sort_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'features' => $this->whenLoaded('features', function () use ($request) {
                return collect($this->features)->map(function ($item) use ($request) {
                    return  resolve(PlanFeatureResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
