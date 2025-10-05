<?php

namespace HMsoft\Cms\Models\Testimonial;

use HMsoft\Cms\Models\GeneralModel;


/**
 * Class Testimonial
 *
 * @property int $id Primary
 * @property mixed $name
 * @property string $message
 * @property float $rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Testimonial extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "testimonials";

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
            'rate' => 'float',
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
            // Testimonial doesn't have relationships, so we return empty array
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
            'name' => 'name',
            'message' => 'message',
            'rate' => 'rate',
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
        return [
            'name',
            'message',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines columns from the translation table for the global search.
     */
    public function defineGlobalSearchTranslationAttributes(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * Specifies the name of the translation table.
     */
    public function defineTranslationTableName(): ?string
    {
        return null; // Testimonial doesn't have translations
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return null; // Testimonial doesn't have translations
    }
}
