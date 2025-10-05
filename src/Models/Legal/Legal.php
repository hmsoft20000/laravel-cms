<?php

namespace HMsoft\Cms\Models\Legal;

use HMsoft\Cms\Models\GeneralModel;
use HMsoft\Cms\Models\Legal\LegalTranslation;
use HMsoft\Cms\Traits\Media\DeletesAllMedia;
use HMsoft\Cms\Traits\Media\HasMedia;
use HMsoft\Cms\Traits\Features\HasFeatures;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Legal extends GeneralModel
{

    use  HasMedia, HasFeatures, DeletesAllMedia;

    /**
     * Table Name In Database.
     */
    protected $table = "legals";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the translation table name.
     */
    public function defineTranslationTableName(): ?string
    {
        // Provide the exact name of your translation table.
        return 'legal_translations';
    }

    /**
     * {@inheritdoc}
     * This method is required by the interface to specify the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        // This is the column in 'translations' that links back to the 'type' table.
        return 'legal_id';
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'publish_at' => 'datetime',
        ];
    }

    public function getMorphClass()
    {
        return 'legal';
    }

    /**
     * Get all of the translations for the Sector
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(LegalTranslation::class, foreignKey: 'legal_id', localKey: 'id');
    }
}
