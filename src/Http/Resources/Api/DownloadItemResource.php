<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class DownloadItemResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {
        $defaultImageFromConfig = config("app.web_config.default_portfolio_image");

        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'file_size' => $this->file_size,
            'file_path' => $this->file_path,
            'sort_number' => $this->sort_number,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return collect($this->categories)->map(function ($category) {
                    return CategoryResource::make($category);
                });
            }),
            'download_links' => $this->whenLoaded('links', function () {
                return $this->links;
            }),
            'category_ids' => $this->whenLoaded('categories', function () {
                return $this->categories->pluck('id');
            }),
            'links_count' => $this->whenCounted('links', $this->links_count),
            'image_url' => $this->whenLoaded('media', function () use ($defaultImageFromConfig) {
                return $this->media ? $this->media->file_url : $defaultImageFromConfig;
            }),
            'image' => $this->whenLoaded('media', function () {
                return [
                    'id' => $this->media->id,
                    'image_url' => $this->media->file_url,
                    'is_default' => $this->media->is_default,
                    'sort_number' => $this->media->sort_number,
                ];
            }),
        ];
    }
}
