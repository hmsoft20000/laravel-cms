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
     * 
     */
    public function defineRelationships(): array
    {
        return [
            // 'Public API Name' => 'eloquentMethodName'
            'translations' => 'translations',
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
            'name'             => 'translations.name',
            'content'          => 'translations.content',
            'short_content'    => 'translations.short_content',
            'image_url'        => 'image',
        ];

        return array_merge($defaultMap, $customMap);
    }


    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be specifically filtered.
     * The logic here remains the same, but it's now more powerful because the
     * service can handle `translations.name` style filters automatically.
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
        return  [
            'name',
            'content',
            'short_content'
        ];
    }


    /**
     * {@inheritdoc}
     * Specifies the name of the translation table.
     */
    public function defineTranslationTableName(): ?string
    {
        return (new OrganizationTranslation())->getTable();
    }


    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return 'organization_id';
    }



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
    | Model Relationships & Accessors
    |--------------------------------------------------------------------------
    */

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
}
