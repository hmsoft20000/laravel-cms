<?php

namespace HMsoft\Cms\Models\Legal;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LegalTranslation extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "legal_translations";

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
     * Get the legal associated with the LegalTranslation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function legal(): BelongsTo
    {
        return $this->belongsTo(Legal::class, foreignKey: 'legal_id', ownerKey: 'id');
    }

}
