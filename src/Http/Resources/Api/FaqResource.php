<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class FaqResource extends BaseJsonResource
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
            'sort_number' => $this->sort_number,
            'is_active' => $this->is_active,
            // Translations processing logic remains the same
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
