<?php

namespace HMsoft\Cms\Models\Content;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PostTranslation Model
 *
 * Stores the translatable content for a single locale of a Post.
 */
class PostTranslation extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'post_translations';

    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the parent post that owns the translation.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
