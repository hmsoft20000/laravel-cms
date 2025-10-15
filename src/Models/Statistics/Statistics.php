<?php

namespace HMsoft\Cms\Models\Statistics;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Statistics
 *
 * @property int $id Primary
 * @property mixed $title
 * @property string $value
 * @property string $icon
 * @property string $type
 * @property int $sort_number
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Statistics extends GeneralModel
{

    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * Table Name In Database.
     */
    protected $table = "statistics";

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
            'id' => 'integer',
            'sort_number' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get all of the translations for the Statistics.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(StatisticsTranslation::class, 'statistics_id');
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

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'title' => 'translations.title',
            'value' => 'translations.value',
            'icon' => 'icon',
            'type' => 'type',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColums = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.value',
        ];

        return array_merge($baseColums, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColums = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.title',
            'translations.value',
        ];

        return array_merge($baseColums, $relatedAttributes);
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [];
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['title', 'value'],
        ];
    }
}
