<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Models\Shared\Medium;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSingleFile
{
    /**
     * Get the model's single associated media file.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(Medium::class, 'owner');
    }

    /**
     * An accessor to easily get the file URL.
     * Usage: $download->file_url
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file?->file_url;
    }
}
