<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ContactUsResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {

        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data = collect($data)
            ->only([
                'id',
                'name',
                'email',
                'subject',
                'mobile',
                'message',
                'status',
                'is_starred',
                'created_at',
                'updated_at',
            ])->all();

        // Add file uploads URLs if they exist
        if ($this->resource->file_uploads) {
            $data['file_uploads_urls'] = $this->resource->file_uploads_urls;
        }

        return $data;
    }
}
