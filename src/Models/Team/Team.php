<?php

namespace HMsoft\Cms\Models\Team;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HMsoft\Cms\Traits\Media\HasSingleMedia;
use HMsoft\Cms\Traits\Media\DeletesSingleMediaFile;

/**
 * Class Team
 *
 * @property int $id Primary
 * @property mixed $name
 * @property string $image
 * @property mixed $job
 * @property mixed $facebook_link
 * @property mixed $twitter_link
 * @property mixed $google_plus_link
 * @property mixed $vimeo_link
 * @property mixed $youtube_link
 * @property mixed $pinterest_link
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package HMsoft\Cms\Models
 */
class Team extends GeneralModel
{

    use HasSingleMedia, DeletesSingleMediaFile;

    /**
     * Table Name In Database.
     */
    protected $table = "teams";

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }


    public function translations(): HasMany
    {
        return $this->hasMany(TeamTranslation::class, 'team_id');
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
            'name' => 'translations.name',
            'job' => 'translations.job',
            'image_url' => 'image', // The image_url accessor depends on the 'image' DB column.
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.name',
            'translations.job',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.name',
            'translations.job',
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
            'translations' => ['name', 'job'],
        ];
    }
}
