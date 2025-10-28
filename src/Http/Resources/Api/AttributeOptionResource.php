<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class AttributeOptionResource extends BaseJsonResource
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
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
