<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class DownloadResource extends BaseJsonResource
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
            'file_path' => $this->file_path, // From the accessor in the Download model
            'file_url' => $this->file_url, // From the accessor in the Download model
            'sort_number' => $this->sort_number,

            // The owner_id and owner_type are now part of the Download model itself
            'owner_id' => $this->owner_id,
            'owner_type' => class_basename($this->owner_type), // e.g., 'Post'

            // Replaces the old 'portfolio' key with a generic 'owner' key
            'owner' => $this->whenLoaded('owner', function () {
                // Using PostResource here. Be cautious of circular dependencies.
                // A simpler, dedicated resource might be better in some cases.
                return new PostResource($this->owner);
            }),

            // Translations processing logic remains the same
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
