<?php

namespace HMsoft\Cms\Http\Resources\Api;

use Illuminate\Http\Request;
use HMsoft\Cms\Http\Resources\BaseJsonResource;

class OurValueResource extends BaseJsonResource
{
    public function resolveData(Request $request): array
    {
        return [
            'id' => $this->id,
            'sort_number' => $this->sort_number,
            'is_active' => $this->is_active,
            'image' => $this->whenLoaded('image', $this->image),
            'translations' => $this->whenLoaded('translations', $this->translations),
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
