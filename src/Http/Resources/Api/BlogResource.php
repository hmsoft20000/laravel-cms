<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class BlogResource extends BaseJsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {

        $defaultImageFromConfig = config("app.web_config.default_blog_image");

        return [
            'id' => $this->id,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'show_in_footer' => $this->show_in_footer,
            'show_in_header' => $this->show_in_header,
            'partners' => $this->whenLoaded('partners', function () use ($request) {
                return collect($this->partners)->map(function ($item) use ($request) {
                    return  resolve(OrganizationResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'sponsors' => $this->whenLoaded('sponsors', function () use ($request) {
                return collect($this->sponsors)->map(function ($item) use ($request) {
                    return  resolve(OrganizationResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'meta_keywords' => $this->meta_keywords,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            'image_url' => $this->whenLoaded('media', function () use ($defaultImageFromConfig) {
                $defaultImage = collect($this->media)->where('is_default', true)->first();
                return $defaultImage ? $defaultImage->file_url : $defaultImageFromConfig;
            }),
            'images_urls' => $this->whenLoaded('media', function () {
                return collect($this->media)
                    ->sortBy('sort_number')
                    ->pluck('file_url')
                    ->all();
            }),
            'images' => $this->whenLoaded('media', function () {
                return collect($this->media)->sortBy('sort_number')->map(function ($medium) {
                    return [
                        'id' => $medium->id,
                        'image_url' => $medium->file_url,
                        'is_default' => $medium->is_default,
                        'sort_number' => $medium->sort_number,
                    ];
                })->all();
            }),
            'image' => $this->whenLoaded('media', function () {
                return collect($this->media)->where('is_default', true)->map(function ($medium) {
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
                return collect($this->categories)->map(function ($item) use ($request) {
                    return  resolve(CategoryResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'features' => $this->whenLoaded('features', function () use ($request) {
                return collect($this->features)->map(function ($item) use ($request) {
                    return  resolve(FeatureResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'downloads' => $this->whenLoaded('downloads', function () use ($request) {
                return collect($this->downloads)->map(function ($item) use ($request) {
                    return resolve(DownloadItemResource::class, ['resource' => $item])->toArray($request);
                });
            }),
            'plans' => $this->whenLoaded('plans', function () use ($request) {
                return collect($this->plans)->map(function ($item) use ($request) {
                    return  resolve(PlanResource::class, ['resource' => $item])->toArray($request);
                });
            }),
           

            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return $this->formatAndGroupAttributeValues($this->resource);
            }),
        ];
    }
}
