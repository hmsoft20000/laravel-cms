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

    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
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


    public function defineFilterableAttributes(): array
    {
        return [
            'status',
            'is_starred',
            'email',
            'name',
        ];
    }


    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'name',
            'email',
        ];
    }
}
