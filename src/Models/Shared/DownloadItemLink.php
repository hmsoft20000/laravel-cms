<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadItemLink extends GeneralModel
{

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'download_item_links';

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }


    /**
     * Get the downloadItem that owns the DownloadItemLink
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function downloadItem(): BelongsTo
    {
        return $this->belongsTo(DownloadItem::class, 'download_item_id', 'id');
    }
}
