<?php

namespace HMsoft\Cms\Models\Sector;

use HMsoft\Cms\Models\Content\Post;
use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Shared\Category;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends GeneralModel
{

    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * Table Name In Database.
     */
    protected $table = "sectors";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];



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
            'posts' => 'posts',
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
            'image_url' => 'image',
        ];

        return array_merge($defaultMap, $customMap);
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be specifically filtered.
     */
    public function defineFilterableAttributes(): array
    {
        return parent::defineFilterableAttributes();
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be sorted.
     */
    public function defineSortableAttributes(): array
    {
        return parent::defineSortableAttributes();
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
        return (new SectorTranslation())->getTable();
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return 'sector_id';
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    /**
     * Get all of the translations for the Sector
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SectorTranslation::class, foreignKey: 'sector_id', localKey: 'id');
    }


    public function categories(): HasMany
    {
        // العلاقة المباشرة مع الفئات
        return $this->hasMany(Category::class, 'sector_id');
    }

    public function posts()
    {
        return $this->hasManyThrough(
            Post::class,
            Category::class,
            'sector_id',
            'id',
            'id',
            'id'
        )
            ->leftJoin('category_post', 'posts.id', '=', 'category_post.post_id')
            ->whereColumn('categories.id', 'category_post.category_id')
            ->select('posts.*');
        // )->join('category_post', 'posts.id', '=', 'category_post.post_id')
        //     ->whereColumn('categories.id', 'category_post.category_id')
        //     ->select('posts.*');
    }
}
