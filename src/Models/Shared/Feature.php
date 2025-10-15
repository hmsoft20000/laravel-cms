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

    protected $appends = ['image_url'];
    
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

    public function defineRelationships(): array
    {
        return [
            'translations' => 'translations',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            'name' => 'translations.name',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.name',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.name',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['name'],
        ];
    }
}
