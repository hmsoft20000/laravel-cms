<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CategoryTranslation Model
 *
 * Stores the translatable content for a single locale of a Category.
 */
class CategoryTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "category_translations";

    protected $guarded = ['id'];


    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the category associated with the translation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
