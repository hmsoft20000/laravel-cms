<?php

namespace HMsoft\Cms\Models\Organizations;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrganizationTranslation
 *
 * @property int $id Primary
 * @property mixed $locale
 * @property int $organization_id
 * @property mixed $name
 * @property mixed $country
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class OrganizationTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "organization_translations";

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

    /**
     * Get the Organization associated with the OrganizationTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, foreignKey: 'organization_id', ownerKey: 'id');
    }


    protected static function boot()
    {
        parent::boot();
    }
}
