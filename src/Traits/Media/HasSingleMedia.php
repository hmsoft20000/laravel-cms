<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Models\Shared\Medium;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSingleMedia
{
    /**
     * Get the model's single associated media file (image).
     */
    public function image(): MorphOne
    {
        // We add a condition to ensure we only get media marked as the single image.
        // This allows a model to have both a single image AND a gallery if needed.
        return $this->morphOne(Medium::class, 'owner')->where('media_type', 'image');
    }

    /**
     * Get the model's media relationship (alias for image() for compatibility with MediaRepository).
     */
    public function media(): MorphOne
    {
        return $this->image();
    }

    /**
     * An accessor to easily get the image URL.
     * Usage: $statistic->image_url
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->media?->file_url;
    }
}
