<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class SectorResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {

        $data = [
            'id' => $this->id,
            'work_ratio' => $this->work_ratio,
            'sort_number' => $this->sort_number,
            'image' => $this->whenLoaded('image', $this->image),
            'image_url' => $this->image_url,
            'posts' => $this->whenLoaded('posts', function () {
                return  resolve(PostResource::class, ['resource' => $this->posts]);
            }),
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->whenLoaded('translations')) {
            $data['translations'] = collect($data['translations'])->mapWithKeys(function ($translation) {
                return [$translation['locale'] => [
                    'name' => $translation['name'],
                    'short_content' => $translation['short_content'],
                    'slug' => $translation['slug'],
                ]];
            })->all();
        }

        return $data;
    }
}
