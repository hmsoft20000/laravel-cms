<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic Plan Model.
 * Can be attached to any model (e.g., Post, Service).
 */
class Plan extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'plans';

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
            'is_featured' => 'boolean',
            'sort_number' => 'integer',
            'price' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the parent owner model (Post, Service, etc.).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * Get all of the translations for the plan.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PlanTranslation::class, 'plan_id');
    }


    /**
     * Scope a query to only include attributes of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
    }

    /**
     * A plan can have many features.
     */
    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class, 'plan_id');
    }



    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
    */

    public function defineRelationships(): array
    {
        return [
            'translations' => 'translations',
            'features' => 'features',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();
        return array_merge($defaultMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.name',
            'translations.description',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.name',
            'translations.description',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['name', 'description'],
            'features' => ['name', 'description'],
        ];
    }
}
