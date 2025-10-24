<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class AttributeResource extends BaseJsonResource
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
            'type' => $this->type,
            // 'scope' => $this->scope, 
            'is_active' => $this->is_active,
            'is_required' => $this->is_required,
            'sort_number' => $this->sort_number,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'category_ids' => $this->whenLoaded('categories', function () {
                return $this->categories->pluck('id')->all();
            }),
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            'options' => $this->whenLoaded('options', function () {
                return collect($this->options)->map(function ($option) {
                    $filteredOption = collect($option)->only(['id', 'is_active', 'sort_number'])->all();
                    $filteredOption['translations'] = collect($option->translations)->mapWithKeys(function ($translation) {
                        return [$translation['locale'] => [
                            'id' => $translation['id'],
                            'title' => $translation['title'],
                        ]];
                    })->all();
                    return $filteredOption;
                })->all();
            }),
        ];
    }
}
