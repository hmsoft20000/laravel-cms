<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Sector\Sector;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shared Category Model (Scoped using STI).
 * Categories can be assigned to various models like Posts, Products, etc.,
 * but are separated by a 'type' column.
 */
class Category extends GeneralModel
{
    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "categories";

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
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getMorphClass(): string
    {
        return 'category';
    }


    // =================================================================
    // RELATIONS
    // =================================================================

    /**
     * Get the sector that this category belongs to.
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    /**
     * Get all of the translations for the Category.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }


    // =================================================================
    // SCOPES
    // =================================================================

    /**
     * Scope a query to only include categories of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, foreignKey: 'parent_id', localKey: 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, foreignKey: 'parent_id', ownerKey: 'id');
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
            'sector' => 'sector',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'name' => 'translations.name',
            'description' => 'translations.description',
            'sector_name' => 'sector.translations.name',
            'image_url' => 'image',
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
