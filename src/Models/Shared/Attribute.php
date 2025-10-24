<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shared Attribute Model (Scoped using STI).
 * Represents an attribute definition (e.g., "Color", "Size") separated by a 'scope' column.
 */
class Attribute extends GeneralModel
{
    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'attributes';

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'sort_number' => 'integer',
            'is_active' => 'boolean',
            'is_required' => 'boolean',
            'is_fast_search' => 'boolean',
        ];
    }

    public function isRequired(): bool
    {
        $hasColumn = array_key_exists('is_required', $this->getAttributes());
        return $hasColumn ? $this->getAttribute('is_required') : false;
    }

    // =================================================================
    // RELATIONS
    // =================================================================

    /**
     * The categories that this attribute is permissible for.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'attribute_category', 'attribute_id', 'category_id');
    }

    /**
     * Get all of the translations for the Attribute.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(AttributeTranslation::class, 'attribute_id');
    }

    /**
     * Get all of the options for the attribute (for select, radio, checkbox types).
     */
    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class, 'attribute_id');
    }

    // =================================================================
    // SCOPES
    // =================================================================

    /**
     * Scope a query to only include attributes of a given scope.
     */
    public function scopeOfScope(Builder $query, string $scope): void
    {
        $query->where('scope', $scope);
    }

    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
    */

    public function defineRelationships(): array
    {
        return [
            // 'Public API Name' => 'eloquentMethodName'
            'translations' => 'translations',
            'categories' => 'categories',
            'options' => 'options',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'name' => 'translations.name',
            'description' => 'translations.description',
            'image_url' => 'image', // The image_url accessor depends on the 'image' DB column.
        ];

        return array_merge($defaultMap, $customMap);
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
        ];
    }
}
