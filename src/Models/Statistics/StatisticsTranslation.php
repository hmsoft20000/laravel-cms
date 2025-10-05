<?php

namespace HMsoft\Cms\Models\Statistics;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StatisticsTranslation Model
 *
 * Stores the translatable content for a single locale of a Statistics.
 */
class StatisticsTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "statistics_translations";

    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the statistics that owns the translation.
     */
    public function statistics(): BelongsTo
    {
        return $this->belongsTo(Statistics::class, 'statistics_id');
    }
}
