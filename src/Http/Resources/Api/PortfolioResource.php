<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PortfolioResource extends BaseJsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request): array
    {

        $defaultImageFromConfig = config("app.web_config.default_portfolio_image");

        return [
            'id' => $this->id,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'show_in_footer' => $this->show_in_footer,
            'show_in_header' => $this->show_in_header,
            'partners' => $this->whenLoaded('partners', function () {
                return  resolve(OrganizationResource::class, ['resource' => $this->partners]);
            }),
            'sponsors' => $this->whenLoaded('sponsors', function () {
                return  resolve(OrganizationResource::class, ['resource' => $this->sponsors]);
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
                return  resolve(CategoryResource::class, ['resource' => $this->categories])->toArray($request);
            }),
            'features' => $this->whenLoaded('features', function () use ($request) {
                return  resolve(FeatureResource::class, ['resource' => $this->features])->toArray($request);
            }),
            'downloads' => $this->whenLoaded('downloads', function () use ($request) {
                return  resolve(DownloadResource::class, ['resource' => $this->downloads])->toArray($request);
            }),
            'plans' => $this->whenLoaded('plans', function () use ($request) {
                return  resolve(DownloadResource::class, ['resource' => $this->plans])->toArray($request);
            }),

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

            if ($attributeData->type === 'checkbox' && !empty($translatedValues)) {
                $attributeArray['value_translations'] = $translatedValues;
            }

            $formattedAttributes[] = $attributeArray;
        }

        return $formattedAttributes;
    }
}
