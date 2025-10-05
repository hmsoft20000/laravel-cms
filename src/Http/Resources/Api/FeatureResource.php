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

        $owner_type = $this->owner_type;

        return [
            'id' => $this->id,
            'image' => $this->image,
            'image_url' => $this->image_url, // From the accessor in the Feature model
            'sort_number' => $this->sort_number,
            'is_active' => $this->is_active,

            // The owner_id and owner_type are now part of the Feature model itself
            "{$owner_type}_id" => $this->owner_id,
            'owner_type' => $this->owner_type,
            'owner_type' => $this->when(!request()->route('type'), class_basename($owner_type)), // e.g., 'Post'

            // Optionally, include the owner data if it's loaded
            'owner' => $this->whenLoaded('owner', function () {
                // This requires a new Resource for the owner, e.g., a simplified PostResource
                // For now, we can return the raw data or a specific resource.
                // Using PostResource might cause circular loading, so a simpler one is better.
                return new PostResource($this->owner);
            }),

            // Translations processing logic remains the same
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
