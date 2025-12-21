<?php

namespace HMsoft\Cms\Models\Content;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Shared\Attribute as CustomAttribute;
use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Models\Sector\Sector;
use HMsoft\Cms\Traits\Attributes\HasAttributeValues;
use HMsoft\Cms\Traits\Categories\Categorizable;
use HMsoft\Cms\Traits\Blogs\HasNestedBlogs;
use HMsoft\Cms\Traits\Downloads\HasDownloads;
use HMsoft\Cms\Traits\Faqs\HasFaqs;
use HMsoft\Cms\Traits\Features\HasFeatures;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\General\Linkable;
use HMsoft\Cms\Traits\Keywords\HasKeywords;
use HMsoft\Cms\Traits\Media\DeletesAllMedia;
use HMsoft\Cms\Traits\Media\HasMedia;
use HMsoft\Cms\Traits\Plans\HasPlans;
use HMsoft\Cms\Traits\Services\HasNestedServices;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use HMsoft\Cms\Models\Shared\Review;
use HMsoft\Cms\Models\Shared\DownloadItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Portfolio extends GeneralModel
{
    use
        FileManagerTrait,
        Categorizable,
        HasFeatures,
        Linkable,
        HasMedia,
        HasFaqs,
        HasDownloads,
        HasPlans,
        DeletesAllMedia,
        HasKeywords,
        HasAttributeValues,
        HasNestedBlogs,
        HasNestedServices;

    protected $table = 'portfolios';

    protected $guarded = ['id'];


    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
            'show_in_header' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'parent_id' => 'integer',
            'owner_id' => 'integer',
        ];
    }

    public function getMorphClass()
    {
        return 'portfolio';
    }

    // =================================================================
    // RELATIONS
    // =================================================================

    public function categories()
    {
        return $this->morphToMany(
            Category::class,
            'owner',
            'categorizables',
            'owner_id',
            'category_id'
        );
    }

    /**
     * Get the parent owner model (e.g., Product, User).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Get all translations for the Post.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PortfolioTranslation::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'owner');
    }

    /**
     * Scope a query to only include attributes of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
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
            'sector' => 'sector',
            'organizations' => 'organizations',
            'partners' => 'partners',
            'sponsors' => 'sponsors',
            'media' => 'media',
            'keywords' => 'keywords',
            'features' => 'features',
            'downloads' => 'downloads',
            'plans' => 'plans',
            'faqs' => 'faqs',
            'attributeValues' => 'attributeValues',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'title' => 'translations.title',
            'content' => 'translations.content',
            'short_content' => 'translations.short_content',
            'category_id' => 'categories.id',
            'sector_id' => 'sector.id',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {

        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title', // <-- Allow filtering by the translated title
            'categories.id',      // <-- Allow filtering by category ID
            'category_id',
            'sector_id',
        ];

        // Logic for custom attributes remains the same
        $customAttributeIds = CustomAttribute::ofScope('portfolio')->where('is_filterable', true)
            ->pluck('id')
            ->toArray();

        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);

        return array_merge($baseColumns, $relatedAttributes, $customAttributeFilters);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title', // <-- Allow sorting by the translated title
        ];

        // Logic for custom attributes remains the same
        $customAttributeIds = CustomAttribute::ofScope('portfolio')->where('is_filterable', true)
            ->pluck('id')
            ->toArray();

        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);

        return array_merge($baseColumns, $customAttributeFilters, $relatedAttributes);
    }
    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            // Search in the 'title' and 'content' columns of the 'translations' relation
            'translations' => ['title', 'content', 'short_content'],

            // You could even search in deeply nested relations
            // 'categories.translations' => ['name', 'description']
        ];
    }
}
