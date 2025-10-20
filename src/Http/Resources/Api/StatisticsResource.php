<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class StatisticsResource extends BaseJsonResource
{

    
    public function resolveData(Request $request)
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'sort_number' => $this->sort_number,
            'is_active' => $this->is_active,
            'image' => $this->whenLoaded('image', $this->image),
            'translations' => $this->whenLoaded('translations', $this->translations),
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        return $data;
    }
}
