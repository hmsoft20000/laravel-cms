<?php

namespace HMsoft\Cms\Traits\Downloads;

use HMsoft\Cms\Models\Shared\Download;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDownloads
{
    /**
     * Get all of the model's downloads (Polymorphic).
     */
    public function downloads(): MorphMany
    {
        return $this->morphMany(Download::class, 'owner')->orderBy('sort_number');
    }
}
