<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PlanTranslation Model
 *
 * Stores the translatable content for a single locale of a Plan.
 */
class PlanTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_translations';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the plan that owns the translation.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
