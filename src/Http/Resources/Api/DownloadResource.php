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
            // Translations processing logic remains the same
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
        ];
    }
}
