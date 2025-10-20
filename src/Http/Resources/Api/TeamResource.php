<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;

class TeamResource extends BaseJsonResource
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
            'job' => $this->job,
            'image' => $this->image,
            'facebook_link' => $this->facebook_link,
            'twitter_link' => $this->twitter_link,
            'google_plus_link' => $this->google_plus_link,
            'vimeo_link' => $this->vimeo_link,
            'youtube_link' => $this->youtube_link,
            'pinterest_link' => $this->pinterest_link,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add image URL using the model's imageUrl attribute
        if ($this->image) {
            $data['image_url'] = $this->image_url;
        }

        // Add translations if requested
        if ($request->has('with_translations') && $request->with_translations) {
            $data['translations'] = $this->translations;
        }

        // Add social links array for easier frontend consumption
        $socialLinks = [];
        if ($this->facebook_link) $socialLinks['facebook'] = $this->facebook_link;
        if ($this->twitter_link) $socialLinks['twitter'] = $this->twitter_link;
        if ($this->google_plus_link) $socialLinks['google_plus'] = $this->google_plus_link;
        if ($this->vimeo_link) $socialLinks['vimeo'] = $this->vimeo_link;
        if ($this->youtube_link) $socialLinks['youtube'] = $this->youtube_link;
        if ($this->pinterest_link) $socialLinks['pinterest'] = $this->pinterest_link;

        if (!empty($socialLinks)) {
            $data['social_links'] = $socialLinks;
        }

        return $data;
    }
}
