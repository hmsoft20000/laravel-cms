<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PlanFeature Model.
 * Represents a single feature or item within a Plan.
 */
class PlanFeature extends GeneralModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_features';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'sort_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the plan that this feature belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get all of the translations for the plan feature.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PlanFeatureTranslation::class, 'plan_feature_id');
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
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $relatedAttributes = [
            'translations.name',
        ];

        return array_merge($defaultMap, $relatedAttributes);
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
