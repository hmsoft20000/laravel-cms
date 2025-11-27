<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HMsoft\Cms\Traits\Categories\Categorizable;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use HMsoft\Cms\Traits\Media\HasSingleMedia;

/**
 * Polymorphic Download Model.
 * Can be attached to any model (e.g., Post, Product).
 */
class DownloadItem extends GeneralModel
{
    use Categorizable, HasSingleMedia, DeletesSingleMediaFile;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'download_items';

    /**
     * The attributes that aren't mass assignable.
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
            'sort_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }


    /**
     * Get all of the translations for the download.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(DownloadItemTranslation::class, 'download_item_id');
    }


    /**
     * Get all of the links for the DownloadItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links(): HasMany
    {
        return $this->hasMany(DownloadItemLink::class, 'download_item_id', 'id');
    }

    public function defineRelationships(): array
    {
        return [
            'translations' => 'translations',
            'categories' => 'categories',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            'title' => 'translations.title',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title',
            'categories.id',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['title'],
        ];
    }
}
