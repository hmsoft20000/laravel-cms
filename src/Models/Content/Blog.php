<?php

namespace HMsoft\Cms\Models\Content;

use Illuminate\Database\Eloquent\Builder;

/**
 * Blog Model.
 *
 * Extends the base Post model and automatically applies a global scope
 * to only query for posts of type 'blog'.
 */
class Blog extends Post
{
    /**
     * The value for the 'type' column in the 'posts' table.
     * Used for scoping and creating new models.
     */
    const POST_TYPE = 'blog';

    /**
     * The table associated with the model.
     * It's the same as the parent Post model table.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The "booted" method of the model.
     *
     * This method automatically applies a global scope to all queries
     * for this model, ensuring only 'blog' type posts are returned.
     */
    protected static function booted(): void
    {
        parent::booted(); // This is important to not override parent boot methods

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'blog');
        });
    }

    /**
     * Overrides the default newInstance method to set the type attribute automatically.
     * This ensures that when you do `new Blog()`, the 'type' is pre-filled.
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        // Only set the type for new, non-existing models
        if (!$exists) {
            $model->setAttribute('type', self::POST_TYPE);
        }

        return $model;
    }
}