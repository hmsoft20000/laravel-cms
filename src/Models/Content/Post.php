<?php

namespace HMsoft\Cms\Models\Content;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Shared\Attribute as CustomAttribute;
use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Traits\Media\HasMedia;
use HMsoft\Cms\Traits\Categories\Categorizable;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Downloads\HasDownloads;
use HMsoft\Cms\Traits\Faqs\HasFaqs;
use HMsoft\Cms\Traits\Features\HasFeatures;
use HMsoft\Cms\Traits\Plans\HasPlans;
use HMsoft\Cms\Traits\General\Linkable;
use HMsoft\Cms\Traits\Media\DeletesAllMedia;
use HMsoft\Cms\Traits\Keywords\HasKeywords;
use HMsoft\Cms\Traits\Attributes\HasAttributeValues;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Post Model (Single Table Inheritance - STI)
 *
 * This is the base model for content types like Portfolio, Blog, Service.
 * It uses a 'type' column in the 'posts' table to differentiate them.
 * All shared relationships use a standardized 'owner' polymorphic relation.
 */
class Post extends GeneralModel
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
        HasAttributeValues;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];



    /**
     * The attributes that should be cast.
     * @var array
     */
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
        return 'post';
    }


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

    // =================================================================
    // RELATIONS
    // =================================================================

    /**
     * Get all translations for the Post.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class, 'post_id');
    }



    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     * This is the most important new method. It tells the JoinManager which
     * relationships are available for joining. The key is the API-friendly name,
     * and the value is the actual Eloquent method name on this model.
     */
    public function defineRelationships(): array
    {
        return [
            // 'Public API Name' => 'eloquentMethodName'
            'translations' => 'translations',
            'categories' => 'categories',
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

    /**
     * {@inheritdoc}
     * The field selection map is now much simpler.
     * It just maps an API field name to either a base table column or a
     * 'relationship.column' string. The service handles the rest.
     */
    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'title' => 'translations.title',
            'content' => 'translations.content',
            'short_content' => 'translations.short_content',
        ];

        return array_merge($defaultMap, $customMap);
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be specifically filtered.
     */
    public function defineFilterableAttributes(): array
    {
        $columns = parent::defineFilterableAttributes();

        $customAttributeIds = CustomAttribute::ofScope('post')->where('is_filterable', true)
            ->pluck('id')
            ->toArray();

        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);

        return array_merge($columns, $customAttributeFilters);
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be sorted.
     */
    public function defineSortableAttributes(): array
    {
        $columns = parent::defineFilterableAttributes();
        $customAttributeIds = CustomAttribute::ofScope('post')->where('is_filterable', true)
            ->pluck('id')
            ->toArray();

        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);
        return array_merge($columns, $customAttributeFilters);
    }

    /**
     * {@inheritdoc}
     * Defines columns from the main table for the global search.
     */
    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * Defines columns from the translation table for the global search.
     */
    public function defineGlobalSearchTranslationAttributes(): array
    {
        return [
            'title',
            'content',
            'short_content'
        ];
    }

    /**
     * {@inheritdoc}
     * Specifies the name of the translation table.
     */
    public function defineTranslationTableName(): ?string
    {
        return (new PostTranslation())->getTable();
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return 'post_id';
    }


    /**
     * Get the parent owner model (e.g., Product, User).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
