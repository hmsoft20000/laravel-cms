<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic Keyword Model.
 *
 * Represents a single keyword/tag that can be attached to any model.
 */
class Keyword extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "keywords";

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * Set to false as timestamps are often not needed for simple tags.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the parent owner model (Post, Product, etc.).
     * The name 'owner' tells Laravel to look for 'owner_id' and 'owner_type' columns.
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }
}
