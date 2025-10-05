<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class OrganizationResource extends BaseJsonResource
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
            'image' => $this->whenLoaded('image', $this->image),
            'sort_number' => $this->sort_number,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'website_url' => $this->website_url,
            'translations' => $this->whenLoaded('translations', function () {
                return $this->translations;
            }),
            'role' => $this->whenLoaded('role', function () {
                return new OrganizationsRolesResource($this->role);
            }),
            'roles' => $this->whenLoaded('roles', function () {
                return  OrganizationsRolesResource::collection($this->roles);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'roles' => $this->roles,
        ];
    }
}
