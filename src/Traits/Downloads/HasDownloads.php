<?php

namespace HMsoft\Cms\Traits\Downloads;

use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Models\Shared\DownloadItem;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasDownloads
{
    /**
     * Get all of the downloads for the model.
     */
    // public function downloads(): MorphToMany
    // {
    //     return $this->morphToMany(Download::class, 'owner', 'download_items');
    // }
    public function downloads(): MorphToMany
    {
        return $this->morphToMany(
            DownloadItem::class, // The final model you want (maps to `download_items`)
            'owner',             // The polymorphic prefix (`owner_id`, `owner_type`)
            'downloads',         // The name of the intermediate PIVOT table
            'owner_id',          // Foreign key for this model (Portfolio) on the pivot
            'download_item_id'   // Foreign key for the DownloadItem model on the pivot
        );
    }
}
