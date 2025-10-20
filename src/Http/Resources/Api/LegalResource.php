<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class LegalResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {

        $type = $this->type;
        $defaultImageFromConfig = config("app.web_config.default_{$type}_image");

        return [
            'id' => $this->id,
            'type' => $this->type, // Added for clarity, can be removed if not needed by frontend
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            // Single default image URL
            'image_url' => $this->whenLoaded('media', function () use ($defaultImageFromConfig) {
                $defaultImage = collect($this->media)->where('is_default', true)->first();
                return $defaultImage ? $defaultImage->file_url : $defaultImageFromConfig;
            }),
            // Array of all image URLs sorted
            'images_urls' => $this->whenLoaded('media', function () {
                return collect($this->media)
                    ->sortBy('sort_number')
                    ->pluck('file_url')
                    ->all();
            }),
            // The key remains 'images' to not break the frontend
            'images' => $this->whenLoaded('media', function () {
                return collect($this->media)->sortBy('sort_number')->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'image_url' => $medium->file_url, // Use the accessor from the Medium model
                        'is_default' => $medium->is_default,
                        'sort_number' => $medium->sort_number,
                    ];
                })->all();
            }),
            'image' => $this->whenLoaded('media', function () {
                return collect($this->media)->where('is_default', true)->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'image_url' => $medium->file_url, // Use the accessor from the Medium model
                        'is_default' => $medium->is_default,
                        'sort_number' => $medium->sort_number,
                    ];
                })->first();
            }),
            'keywords' => $this->whenLoaded('keywords', function () {
                return collect($this->keywords)->pluck('keyword')->all();
            }),
            'features' => $this->whenLoaded('features', function () use ($request) {
                return collect($this->features)->map(function ($item) use ($request) {
                    return  resolve(FeatureResource::class, ['resource' => $item])->toArray($request);
                });
            }),
        ];
    }
}
