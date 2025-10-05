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
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function defineFilterableAttributes(): array
    {
        return [
            // Columns from the  table
            'id',

            // --- From 'translations' table ---
            'title',
            'description',
            'keywords',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function defineSortableAttributes(): array
    {
        return [
            // Columns from the  table
            'id',

            // --- From 'translations' table ---
            'title',
            'description',
            'keywords',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the 'translations' table to be included in the global search.
     */
    public function defineGlobalSearchTranslationAttributes(): array
    {
        return [
            'title',
            'description',
            'keywords',
        ];
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the translation table name.
     */
    public function defineTranslationTableName(): ?string
    {
        // Provide the exact name of your translation table.
        return 'pages_meta_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        // This is the column in 'translations' that links back to the 'type' table.
        return 'pages_meta_id';
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
        ];
    }


    public function translations(): HasMany
    {
        return $this->hasMany(PageMetaTranslation::class, foreignKey: 'pages_meta_id', localKey: 'id');
    }
}
