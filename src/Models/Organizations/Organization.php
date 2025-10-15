<?php

namespace HMsoft\Cms\Models\Organizations;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Organization
 *
 * @property int $id Primary
 * @property string $image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Organization extends GeneralModel
{

    use HasSingleMedia, DeletesSingleMediaFile;

    protected $table = "organizations";
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }


    // =================================================================
    // RELATIONS
    // =================================================================

    /**
     * Get all of the translations for the Organization 
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(OrganizationTranslation::class, foreignKey: 'organization_id', localKey: 'id');
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
            'name'             => 'translations.name',
            'content'          => 'translations.content',
            'short_content'    => 'translations.short_content',
            'image_url'        => 'image',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.name',
            'translations.content',
            'translations.short_content',
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
            'translations' => ['name', 'content', 'short_content'],
        ];
    }
}
