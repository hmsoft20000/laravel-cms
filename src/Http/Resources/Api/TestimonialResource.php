<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TestimonialResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function resolveData(Request $request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'sort_number' => $this->sort_number,
            'rate' => $this->rate,
            'is_active' => $this->is_active,
            'publish_at' => $this->publish_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add translations if requested
        if ($request->has('with_translations') && $request->with_translations) {
            $data['translations'] = $this->translations;
        }

        return $data;
    }
}
