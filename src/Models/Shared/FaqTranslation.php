<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FaqTranslation Model
 *
 * Stores the translatable content for a single locale of a FAQ.
 */
class FaqTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "faq_translations";

    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the faq that owns the translation.
     */
    public function faq(): BelongsTo
    {
        return $this->belongsTo(Faq::class, 'faq_id');
    }
}
