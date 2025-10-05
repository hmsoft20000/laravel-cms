<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PostResource extends BaseJsonResource
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
            'is_active' => $this->is_active,
            'show_in_footer' => $this->show_in_footer,
            'show_in_header' => $this->show_in_header,
            'partners' => $this->whenLoaded('partners', function () {
                return  OrganizationResource::collection($this->partners);
            }),
            'sponsors' => $this->whenLoaded('sponsors', function () {
                return  OrganizationResource::collection($this->sponsors);
            }),
            'meta_keywords' => $this->meta_keywords,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            // Single default image URL
            'image_url' => $this->whenLoaded('media', function () use ($defaultImageFromConfig) {
                $defaultImage = collect($this->media)->where('is_default', true)->first();
                if ($defaultImage) {
                    return $defaultImage->file_url;
                }

                $firstImage = collect($this->media)->sortBy('sort_number')->first();
                if ($firstImage) {
                    return $firstImage->file_url;
                }

                return $defaultImageFromConfig;
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

            'categories' => $this->whenLoaded('categories', function () {
                return  CategoryResource::collection($this->categories);
            }),
            'features' => $this->whenLoaded('features', function () use ($request) {
                return  FeatureResource::collection($this->features)->toArray($request);
            }),
            'downloads' => $this->whenLoaded('downloads', function () use ($request) {
                return  DownloadResource::collection($this->downloads)->toArray($request);
            }),
            'plans' => $this->whenLoaded('plans', function () use ($request) {
                return  DownloadResource::collection($this->plans)->toArray($request);
            }),

            // The core logic to rebuild the exact same attribute structure
            // 'attribute_values' => $this->attributeValues,
            // 'attribute_values' => $this->attributeValues,
            'attribute_values' => $this->formatAndGroupAttributeValues($this->resource),
        ];
    }

    private function formatAndGroupAttributeValues($resource): array
    {
        if (!$resource->relationLoaded('attributeValues')) {
            return [];
        }

        $formattedAttributes = [];
        $groupedValues = $resource->attributeValues->groupBy('attribute_id');

        foreach ($groupedValues as $attributeId => $values) {
            $firstValue = $values->first();
            if (!$firstValue || !$firstValue->relationLoaded('attribute')) {
                continue;
            }

            $attributeData = $firstValue->attribute;
            if (!$attributeData) {
                continue;
            }

            $attributeTranslations = $this->formatTranslations($attributeData->translations);

            $processedAttribute = $this->processTranslations(['translations' => $attributeTranslations]);

            $finalValue = null;
            $translatedValues = [];

            switch ($attributeData->type) {
                case 'text':
                case 'textarea':
                    $finalValue = $values->mapWithKeys(fn($val) => [$val['locale'] => $val['value']])->all();
                    break;

                case 'checkbox':
                    $valueContainer = $values->first();
                    if ($valueContainer && $valueContainer->relationLoaded('selectedOptions')) {
                        $translatedValues = $valueContainer->selectedOptions->map(function ($selectedOption) {
                            if (!$selectedOption->relationLoaded('option')) return null;

                            $optionTranslations = $this->formatTranslations($selectedOption->option->translations);
                            $processedOption = $this->processTranslations(['translations' => $optionTranslations]);

                            return [
                                'id' => $selectedOption->option->id,
                                'title' => $processedOption['title'] ?? null,
                                'translations' => $processedOption['translations'],
                            ];
                        })->filter()->values()->all();

                        $finalValue = collect($translatedValues)->pluck('id')->all();
                    }
                    break;

                case 'select':
                case 'radio':
                    $valueModel = $values->first();
                    if ($valueModel && $attributeData->relationLoaded('options')) {
                        $option = $attributeData->options->firstWhere('id', $valueModel->value);
                        if ($option) {
                            $optionTranslations = $this->formatTranslations($option->translations);
                            $finalValue = $this->processTranslations(['translations' => $optionTranslations]);
                        }
                    }
                    break;

                default:
                    $finalValue = $values->first()->value;
                    break;
            }





            $attributeArray = [
                'attribute_id' => $attributeData->id,
                'type' => $attributeData->type,
                'title' => $processedAttribute['title'] ?? null,
                'translations' => $attributeTranslations,
                'value' => $finalValue,
            ];

            // نضيف value_translations فقط إذا كان نوع الحقل checkbox وكانت المصفوفة غير فارغة
            if ($attributeData->type === 'checkbox' && !empty($translatedValues)) {
                $attributeArray['value_translations'] = $translatedValues;
            }

            $formattedAttributes[] = $attributeArray;
        }

        return $formattedAttributes;
    }
}
