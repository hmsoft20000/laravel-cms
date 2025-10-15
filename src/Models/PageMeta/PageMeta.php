<?php

namespace HMsoft\Cms\Models\PageMeta;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PageMeta
 *
 * @property int $id Primary
 * @property mixed $name
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class PageMeta extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "pages_meta";

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
        ];
    }

    // =================================================================
    // RELATIONS
    // =================================================================

    public function translations(): HasMany
    {
        return $this->hasMany(PageMetaTranslation::class, foreignKey: 'pages_meta_id', localKey: 'id');
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
        ];
    }

    public function defineFilterableAttributes(): array
    {

        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.description',
            'translations.keywords',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {

        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.description',
            'translations.keywords',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            // Search in the 'title' and 'content' columns of the 'translations' relation
            'translations' => ['title', 'content', 'keywords'],
        ];
    }
}
