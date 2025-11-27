<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\HasSingleFile;
use HMsoft\Cms\Traits\Media\DeletesSingleFileOnDelete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use HMsoft\Cms\Traits\Categories\Categorizable;
use HMsoft\Cms\Traits\Media\DeletesAllMedia;
use HMsoft\Cms\Traits\Media\HasMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Polymorphic Download Model.
 * Can be attached to any model (e.g., Post, Product).
 */
class Download extends GeneralModel
{
    use HasSingleFile, DeletesSingleFileOnDelete, Categorizable, HasMedia, DeletesAllMedia;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'downloads';

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
        ];
    }

    /**
     * Get the parent owner model (Post, Product, etc.).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * Get the downloadItem that owns the Download
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function downloadItem(): BelongsTo
    {
        return $this->belongsTo(DownloadItem::class, 'download_item_id', 'id');
    }

    /**
     * Scope a query to only include attributes of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
    }


    public function defineRelationships(): array
    {
        return [
            'downloadItems' => 'downloadItems',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            'name' => 'downloadItems.name',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'downloadItems.name',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'downloadItems.name',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'downloadItems' => ['name'],
        ];
    }
}
