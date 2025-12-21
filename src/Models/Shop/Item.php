<?php

namespace HMsoft\Cms\Models\Shop;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Shared\Attribute as CustomAttribute;
use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Traits\Attributes\HasAttributeValues;
use HMsoft\Cms\Traits\Categories\Categorizable;
use HMsoft\Cms\Traits\Downloads\HasDownloads;
use HMsoft\Cms\Traits\Faqs\HasFaqs;
use HMsoft\Cms\Traits\Blogs\HasBlogs;
use HMsoft\Cms\Traits\Features\HasFeatures;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\General\Linkable;
use HMsoft\Cms\Traits\Keywords\HasKeywords;
use HMsoft\Cms\Traits\Media\DeletesAllMedia;
use HMsoft\Cms\Traits\Media\HasMedia;
use HMsoft\Cms\Traits\Plans\HasPlans;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Item extends GeneralModel
{
    // استخدام جميع الـ Traits المشتركة من الحزمة
    use
        FileManagerTrait,
        Categorizable,
        HasFeatures,
        Linkable,
        HasMedia,
        HasFaqs,
        HasDownloads,
        HasBlogs,
        HasPlans,
        DeletesAllMedia,
        HasKeywords,
        HasAttributeValues;

    protected $table = 'items';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'manage_stock' => 'boolean',
            'is_virtual' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function getMorphClass()
    {
        // استخدام 'items' ليتطابق مع CmsRoute و morph_map
        return 'items';
    }

    // =================================================================
    // E-COMMERCE RELATIONS
    // =================================================================

    /**
     * جلب جميع الترجمات للمنتج
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ItemTranslation::class);
    }

    /**
     * جلب جميع التوليفات (Variants) للمنتج
     */
    public function variations(): HasMany
    {
        return $this->hasMany(ItemVariation::class);
    }

    /**
     * جلب جميع مجموعات الإضافات (Addons) للمنتج
     */
    public function addons(): HasMany
    {
        return $this->hasMany(ItemAddon::class);
    }

    /**
     * جلب المنتجات المكونة لهذه الحزمة (Bundled/Grouped)
     */
    public function childItemJoins(): HasMany
    {
        return $this->hasMany(ItemJoin::class, 'parent_item_id');
    }

    /**
     * جلب الحزم التي ينتمي إليها هذا المنتج
     */
    public function parentItemJoins(): HasMany
    {
        return $this->hasMany(ItemJoin::class, 'child_item_id');
    }

    /**
     * جلب المنتجات المرتبطة (Related, Upsell, Cross-sell)
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(ItemRelationship::class, 'item_id');
    }


    // =================================================================
    // TRAIT RELATIONS (e.g., Categories)
    // =================================================================

    /**
     * جلب الأقسام (Categories)
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            Category::class,
            'owner',
            'categorizables',
            'owner_id',
            'category_id'
        );
    }


    public function childItems(): HasMany
    {
        return $this->hasMany(ItemJoin::class, 'parent_item_id');
    }

    public function parentItems(): HasMany
    {
        return $this->hasMany(ItemJoin::class, 'child_item_id');
    }

    // =================================================================
    // AutoFilterable Implementation
    // =================================================================

    public function defineRelationships(): array
    {
        // دمج علاقات الحزمة مع العلاقات الجديدة
        return [
            // CMS Traits
            'translations' => 'translations',
            'categories' => 'categories',
            'media' => 'media',
            'keywords' => 'keywords',
            'features' => 'features',
            'downloads' => 'downloads',
            'plans' => 'plans',
            'faqs' => 'faqs',
            'attributeValues' => 'attributeValues',

            // E-commerce
            'variations' => 'variations',
            'addons' => 'addons',
            'childItemJoins' => 'childItemJoins',
            'relationships' => 'relationships',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        return array_merge(parent::defineFieldSelectionMap(), [
            'title' => 'translations.title',
            'content' => 'translations.content',
            'short_content' => 'translations.short_content',
            'category_id' => 'categories.id',
            'sector_id' => 'categories.sector.id'
        ]);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title',
            'categories.id',
            'category_id',
            'type',
            'price',
            'is_active',
            'sector_id',
        ];

        $customAttributeIds = CustomAttribute::ofScope($this->getMorphClass())->where('is_filterable', true)
            ->pluck('id')
            ->toArray();
        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);

        return array_merge($baseColumns, $relatedAttributes, $customAttributeFilters);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title',
            'price',
            'created_at',
        ];

        $customAttributeIds = CustomAttribute::ofScope($this->getMorphClass())->where('is_filterable', true)
            ->pluck('id')
            ->toArray();
        $customAttributeFilters = array_map(fn($id) => 'attribute_' . $id, $customAttributeIds);

        return array_merge($baseColumns, $customAttributeFilters, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['title', 'content', 'short_content'],
            'variations' => ['sku'],
        ];
    }
}
