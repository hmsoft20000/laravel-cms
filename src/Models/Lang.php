<?php

namespace HMsoft\Cms\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lang
 *
 * @property int $id Primary
 * @property mixed $locale
 * @property mixed $name
 * @property mixed $direction
 * @property mixed $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @package HMsoft\Cms\Models
 */
class Lang extends GeneralModel
{

    /**
     * Table Name In Database.
     */
    protected $table = "langs";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];


    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }


    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'name',
            'locale',
        ];
    }
}
