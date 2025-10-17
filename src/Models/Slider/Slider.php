<?php

namespace HMsoft\Cms\Models\Slider;

use HMsoft\Cms\Enums\SliderTypeEnum;
use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Shared\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Slider
 *
 * @property int $id Primary
 * @property mixed $status
 * @property string $image
 * @property mixed $type
 * @property mixed $published
 * @property mixed $resource_type
 * @property int $resource_id
 * @property mixed $background_color
 * @property \Carbon\Carbon $from_time
 * @property \Carbon\Carbon $to_time
 * @property int $sort_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Slider extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "sliders";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'sort_number' => 'integer',
            'published' => 'boolean',
            'status' => 'boolean',
            'from_time' => 'datetime',
            'to_time' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
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
            'category' => 'category',
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
            'image_url' => 'image', // The image_url accessor depends on the 'image' DB column.
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
            'content'
        ];
    }

    /**
     * {@inheritdoc}
     * Specifies the name of the translation table.
     */
    public function defineTranslationTableName(): ?string
    {
        return resolve(SliderTranslation::class)->getTable();
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return 'slider_id';
    }



    public function imageUrl(): Attribute
    {
        $image = $this->image;
        return new Attribute(
            get: fn() =>  isset($image) ?  storageDisk('public')->url(cmsImageDir('slider') . "/" . $image) :
                config('app.web_config.default_slider_image'),
        );
    }


    /**
     * Get all of the translations for the Slider
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SliderTranslation::class, foreignKey: 'slider_id', localKey: 'id');
    }

    public function scopePublished(Builder $query)
    {
        $query->where('published', true);
    }

    public function scopePopup(Builder $query)
    {
        $query->where('type', SliderTypeEnum::popup);
    }

    public function scopeHeader(Builder $query)
    {
        $query->where('type', SliderTypeEnum::header);
    }

    public function scopeFooter(Builder $query)
    {
        $query->where('type', SliderTypeEnum::footer);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status', true);
    }

    /**
     * Get the category that owns the Slider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return   $this->belongsTo(Category::class, 'resource_id', 'id');
    }
    public function scopeActiveInPeriod(Builder $query)
    {
        $now = now();
        $query->where('status', true)
            ->where(function ($wq) use ($now) {
                $wq->whereRaw(
                    "(from_time IS NULL AND to_time IS NULL)"
                )->orWhereRaw(
                    "(
                    ( from_time IS NOT NULL AND to_time IS NOT NULL) AND ('$now' <= to_time AND '$now' >= from_time )
                )"
                )->orWhereRaw(
                    "(
                    ( from_time IS NOT NULL AND to_time IS NULL) AND  ( '$now' >= from_time)
                )"
                )->orWhereRaw(
                    "(
                    ( from_time IS  NULL AND to_time IS NOT NULL) AND  ('$now' <= to_time)
                )"
                );
            });
    }

    protected static function boot()
    {
        parent::boot();
    }
}
