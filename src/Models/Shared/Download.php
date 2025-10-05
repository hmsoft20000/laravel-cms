<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\HasSingleFile;
use HMsoft\Cms\Traits\Media\DeletesSingleFileOnDelete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic Download Model.
 * Can be attached to any model (e.g., Post, Product).
 */
class Download extends GeneralModel
{
    use HasSingleFile, DeletesSingleFileOnDelete;

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
     * Get all of the translations for the download.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(DownloadTranslation::class, 'download_id');
    }


    /**
     * Scope a query to only include attributes of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
    }
}
