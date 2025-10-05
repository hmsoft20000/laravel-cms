<?php

namespace HMsoft\Cms\Models\ContactUs;

use HMsoft\Cms\Models\GeneralModel;


class ContactUs extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "contact_us_messages";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [
        'id'
    ];

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
            // ContactUs doesn't have relationships, so we return empty array
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
            'email' => 'email',
            'subject' => 'subject',
            'message' => 'message',
        ];

        return array_merge($defaultMap, $customMap);
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be specifically filtered.
     */
    public function defineFilterableAttributes(): array
    {
        return [
            'status',
            'is_starred',
            'email',
            'name',
        ];
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
            'email',
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
        return null; // ContactUs doesn't have translations
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return null; // ContactUs doesn't have translations
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_starred' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'file_uploads' => 'array', // Cast file_uploads to array for easy access
    ];

    /**
     * Get the full URLs for uploaded files
     *
     * @return array
     */
    public function getFileUploadsUrlsAttribute(): array
    {
        $files = $this->file_uploads;

        if (!$files) {
            return [];
        }

        if (is_string($files)) {
            $files = json_decode($files, true);
            if (!is_array($files)) {
                return [];
            }
        }

        return array_map(function ($fileName) {
            return storageDisk('public')->url('contact_us_files/' . $fileName);
        }, $files);
    }
}
