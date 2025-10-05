<?php

namespace HMsoft\Cms\Models\Sector;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SectorTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "sectors_translations";

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the sector associated with the SectorTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, foreignKey: 'sector_id', ownerKey: 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {});
    }
}
