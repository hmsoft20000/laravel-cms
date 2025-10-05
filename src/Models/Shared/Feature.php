<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;

/**
 * Polymorphic Feature Model.
 * Can be attached to any model (e.g., Post, Product).
 */
class Feature extends GeneralModel
{
    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "features";

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the parent owner model (Post, Product, etc.).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * Get all of the translations for the Feature.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FeatureTranslation::class, 'feature_id');
    }


    /**
     * Scope a query to only include attributes of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
    }
}
