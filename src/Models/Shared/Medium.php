<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic Medium Model.
 * Represents any media file (image, video, pdf) that can be attached to any model.
 */
class Medium extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['file_url'];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'sort_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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
     * Accessor for the 'file_url' attribute.
     * Constructs the full URL to the media file dynamically.
     */
    protected function fileUrl(): EloquentAttribute
    {
        $defaultFile = config('web_config');
        $fileName = $this->file_path;

        // The owner relation must be loaded to access the `type`
        // if (!$this->relationLoaded('owner') || !$this->owner) {
        //     return EloquentAttribute::make(get: fn() => $defaultFile);
        // }
        // Get the post's type from the loaded owner model
        // $ownerType = strtolower($this->owner->type) ?? 'post';

        $ownerType = $this->owner_type;
        $ownerId = $this->owner_id;

        return EloquentAttribute::make(
            get: fn() => !is_null($fileName)
                ? (filter_var($fileName, FILTER_VALIDATE_URL)
                    ? $fileName
                    : storageDisk('public')->url("{$ownerType}/{$ownerId}/media/{$fileName}"))
                : $defaultFile
        );
    }

    // protected function fileUrl(): EloquentAttribute
    // {
    //     $defaultFile = null;
    //     $fileName = $this->file_path;
    //     // Avoid N+1 problem if owner is not loaded
    //     // if (!$this->relationLoaded('owner') || !$this->owner) {
    //     //     return EloquentAttribute::make(get: fn() => $defaultFile);
    //     // }
    //     // $ownerType = class_basename($this->owner_type); // e.g., 'Post'
    //     $ownerType = $this->owner_type;
    //     $ownerId = $this->owner_id;
    //     return EloquentAttribute::make(
    //         get: fn() => !is_null($fileName)
    //             ? (filter_var($fileName, FILTER_VALIDATE_URL)
    //                 ? $fileName
    //                 : storageDisk('public')->url("{$ownerType}/{$ownerId}/{$fileName}"))
    //             : $defaultFile
    //     );
    // }

}
