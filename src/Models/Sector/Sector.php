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
            'posts' => 'posts',
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
            'image_url' => 'image',
        ];

        return array_merge($defaultMap, $customMap);
    }


    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.content',
            'translations.short_content',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.content',
            'translations.short_content',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    public function defineGlobalSearchTranslationAttributes(): array
    {
        return [
            'translations' => ['title', 'content', 'short_content'],
        ];
    }
}
