<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class FeatureResource extends BaseJsonResource
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
            'image' => $this->whenLoaded('media', $this->media),
            'image_url' => $this->image_url,
            'sort_number' => $this->sort_number,
            'is_active' => $this->is_active,
            // Translations processing logic remains the same
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
