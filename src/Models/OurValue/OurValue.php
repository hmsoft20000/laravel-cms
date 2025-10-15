<?php

namespace HMsoft\Cms\Models\OurValue;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OurValue extends GeneralModel
{
    use HasSingleMedia, DeletesSingleMediaFile;

    protected $table = "our_values";

    protected $fillable = [
        'image',
        'created_by',
        'updated_by',
    ];


    public function getMorphClass()
    {
        return 'our_value';
    }

    /**
     * Get all of the translations for the Statistics.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(OurValueTranslation::class, 'our_value_id');
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
