<?php

namespace HMsoft\Cms\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class BusinessSetting
 *
 * @property mixed $type
 * @property string $value
 * @property mixed $number
 * @property string $comment
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package HMsoft\Cms\Models
 */
class BusinessSetting extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "business_settings";

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'type';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['key'];

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
}
