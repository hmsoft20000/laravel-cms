<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DownloadTranslation Model
 *
 * Stores the translatable content for a single locale of a Download.
 */
class DownloadTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'download_translations';

    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the download that owns the translation.
     */
    public function download(): BelongsTo
    {
        return $this->belongsTo(Download::class, 'download_id');
    }
}
